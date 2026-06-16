<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // Expulsar o intruso de volta para o formulário de login
    header("Location: ../../public/login.html?erro=restrito");
    exit; // Interrompe imediatamente a execução do resto da página
}

// Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);
if (!$conn) { die("Falha na ligação: " . mysqli_connect_error()); }

// Inicializar a variável para o documento
$documento = null;

// ==========================================
// AÇÃO 1: RECUPERAR OS DADOS ATUAIS (GET)
// ==========================================
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // ALTERADO: Adicionado filtro de estado para garantir integridade do arquivo ativo
    $sql = "SELECT * FROM documentacao WHERE id = ? AND estado = 'Ativo'";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $documento = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['mensagem_erro'] = "Registo de documentação não encontrado ou inativo.";
        header("Location: ../listar/lista_documentos.php");
        exit;
    }
    mysqli_stmt_close($stmt);
} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../listar/lista_documentos.php");
    exit;
}

// ==========================================
// AÇÃO 2: PROCESSAR A ATUALIZAÇÃO (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id              = intval($_POST['id']);
    $equipamento_id  = intval($_POST['equipamento_id']);
    $tipo_documento  = trim($_POST['tipo_documento']);
    $nome_documento  = trim($_POST['nome_documento']);
    $caminho_bd      = trim($_POST['caminho_atual']); // Por omissão, mantém o caminho existente
    $data_documento  = trim($_POST['data_documento']);
    $data_validade   = !empty($_POST['data_validade']) ? trim($_POST['data_validade']) : null;

    $enums_validos = [
        'Manual de utilizador', 'Manual de serviço', 'Certificado de calibração', 
        'Contrato de manutenção', 'Fatura ou guia de aquisição', 
        'Declaração de conformidade', 'Relatório técnico'
    ];

    $erros_data = [];
    $d_doc = null;
    $d_val = null;

    if (!empty($data_documento)) {
        $d_doc = DateTime::createFromFormat('Y-m-d', $data_documento);
        if (!$d_doc || $d_doc->format('Y-m-d') !== $data_documento) {
            $erros_data[] = "A data do documento introduzida é inválida.";
        } elseif ($d_doc > new DateTime()) {
            $erros_data[] = "A data do documento não pode ser uma data futura.";
        }
    }

    if ($data_validade !== null) {
        $d_val = DateTime::createFromFormat('Y-m-d', $data_validade);
        if (!$d_val || $d_val->format('Y-m-d') !== $data_validade) {
            $erros_data[] = "A data de validade introduzida é inválida.";
        }
    }

    if ($d_doc && $d_val) {
        if ($d_val < $d_doc) {
            $erros_data[] = "A data de validade não pode ser anterior à data do documento.";
        }
    }

    // --- LOGICA DE TRATAMENTO E UPLOAD DO NOVO FICHEIRO (SE FOR ENVIADO) ---
    $upload_ok = true;
    if (isset($_FILES['novo_documento_media']) && $_FILES['novo_documento_media']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['novo_documento_media']['tmp_name'];
        $fileName    = $_FILES['novo_documento_media']['name'];
        $fileSize    = $_FILES['novo_documento_media']['size'];
        
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $extensoes_permitidas = ['pdf', 'jpg', 'jpeg', 'png'];

        if (in_array($fileExtension, $extensoes_permitidas)) {
            if ($fileSize <= 5242880) { // Restrição técnica de 5MB
                $uploadFileDir = '../../uploads/documentos/';
                
                // Garantir criação física da pasta de destino
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // Remover fisicamente o anexo antigo para evitar lixo no storage
                    $ficheiro_antigo = '../../' . $_POST['caminho_atual'];
                    if (!empty($_POST['caminho_atual']) && file_exists($ficheiro_antigo)) {
                        unlink($ficheiro_antigo);
                    }
                    // Definir o novo caminho que será guardado na BD
                    $caminho_bd = 'uploads/documentos/' . $newFileName;
                } else {
                    $upload_ok = false;
                    $erros_data[] = "Erro de permissões ao mover o novo ficheiro para o servidor.";
                }
            } else {
                $upload_ok = false;
                $erros_data[] = "O novo ficheiro excede o limite estipulado de 5MB.";
            }
        } else {
            $upload_ok = false;
            $erros_data[] = "Extensão de ficheiro proibida. Use apenas PDF, JPG ou PNG.";
        }
    }

    if ($equipamento_id <= 0 || empty($nome_documento) || empty($data_documento) || !in_array($tipo_documento, $enums_validos)) {
        $erro = "Todos os campos obrigatórios devem estar corretamente preenchidos.";
    } elseif (!empty($erros_data) || !$upload_ok) {
        $erro = implode("<br>", $erros_data);
    } else {
        // SQL Parametrizado atualizado com o caminho dinâmico ($caminho_bd)
        $sql_update = "UPDATE documentacao SET 
                        tipo_documento=?, nome_documento=?, nome_ficheiro_caminho=?, 
                        data_documento=?, data_validade=?, equipamento_id=? 
                       WHERE id=?";
                       
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "sssssii", 
                $tipo_documento, $nome_documento, $caminho_bd, 
                $data_documento, $data_validade, $equipamento_id, $id
            );
            
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION['mensagem_sucesso'] = "Índice de documentação atualizado com sucesso!";
                header("Location: ../listar/lista_documentos.php");
                exit;
            } else {
                $erro = "Erro técnico ao atualizar na base de dados: " . mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Documentação | MedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-light" style="font-family: 'Titillium Web', sans-serif;">

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="card shadow-sm border-0 rounded-3 p-4 bg-white">
                <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-file-pen me-2 text-primary"></i>Editar Registo de Documentação
                    </h4>
                    <a href="../listar/lista_documentos.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="fa-solid fa-circle-exclamation me-2"></i>
                        <div><?php echo $erro; ?></div>
                    </div>
                <?php endif; ?>

                <form action="editar_documentacao.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo isset($documento) ? $documento['id'] : $id; ?>">
                    <input type="hidden" name="caminho_atual" value="<?php echo htmlspecialchars($_POST['caminho_atual'] ?? $documento['nome_ficheiro_caminho'] ?? ''); ?>">

                    <div class="row g-3">
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Equipamento Médico</label>
                            <select class="form-select" name="equipamento_id" required>
                                <?php
                                $eq_id_atual = $_POST['equipamento_id'] ?? $documento['equipamento_id'] ?? 0;
                                $res_eq = mysqli_query($conn, "SELECT id, designacao, numero_serie FROM equipamentos ORDER BY designacao ASC");
                                while ($eq = mysqli_fetch_assoc($res_eq)) {
                                    $selected = ($eq['id'] == $eq_id_atual) ? 'selected' : '';
                                    echo "<option value='{$eq['id']}' $selected>" . htmlspecialchars($eq['designacao']) . " (S/N: " . htmlspecialchars($eq['numero_serie']) . ")</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Tipo de Documento</label>
                            <select class="form-select" name="tipo_documento" required>
                                <?php
                                $tipo_atual = $_POST['tipo_documento'] ?? $documento['tipo_documento'] ?? '';
                                $tipos = [
                                    'Manual de utilizador', 'Manual de serviço', 'Certificado de calibração', 
                                    'Contrato de manutenção', 'Fatura ou guia de aquisição', 
                                    'Declaração de conformidade', 'Relatório técnico'
                                ];
                                foreach ($tipos as $t) {
                                    $selected = ($t === $tipo_atual) ? 'selected' : '';
                                    echo "<option value='$t' $selected>$t</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Nome Descritivo do Documento</label>
                            <input type="text" class="form-control" name="nome_documento" 
                                   value="<?php echo htmlspecialchars($_POST['nome_documento'] ?? $documento['nome_documento'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12">
                            <div class="p-3 bg-light rounded border border-start-3 border-info mb-1">
                                <span class="small fw-bold text-dark d-block mb-1"><i class="fa-solid fa-paperclip me-1"></i> Ficheiro Atualmente Ativo:</span>
                                <?php 
                                $caminho_atual_doc = $_POST['caminho_atual'] ?? $documento['nome_ficheiro_caminho'] ?? '';
                                if(!empty($caminho_atual_doc)): 
                                ?>
                                    <a href="../../<?php echo $caminho_atual_doc; ?>" target="_blank" class="small text-decoration-none text-primary font-monospace">
                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> <?php echo basename($caminho_atual_doc); ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small"><em>Nenhum ficheiro anexado.</em></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold text-primary"><i class="fa-solid fa-arrows-rotate me-1"></i>Substituir Ficheiro Anexo (Opcional)</label>
                            <input type="file" class="form-control" name="novo_documento_media" accept=".pdf,.png,.jpg,.jpeg">
                            <small class="text-muted" style="font-size: 0.78rem;">Deixe vazio caso queira manter o anexo original ativo. Máx 5MB.</small>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Data de Emissão / Documento</label>
                            <input type="date" class="form-control" name="data_documento" 
                                   value="<?php echo htmlspecialchars($_POST['data_documento'] ?? $documento['data_documento'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Data de Validade (Opcional)</label>
                            <input type="date" class="form-control" name="data_validade" 
                                   value="<?php echo htmlspecialchars($_POST['data_validade'] ?? $documento['data_validade'] ?? ''); ?>">
                        </div>

                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2 border-top pt-3">
                        <a href="../listar/lista_documentos.php" class="btn btn-light border px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-semibold text-white">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Alterações
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>
<?php mysqli_close($conn); ?>