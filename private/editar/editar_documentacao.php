<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // 3. Expulsar o intruso de volta para o formulário de login
    // Ajusta o caminho se o teu login.php estiver numa pasta acima (ex: ../login.php)
    header("Location: ../../public/login.php?erro=restrito");
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
    
    $sql = "SELECT * FROM documentacao WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $documento = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['mensagem_erro'] = "Registo de documentação não encontrado.";
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
    $id                     = intval($_POST['id']);
    $equipamento_id         = intval($_POST['equipamento_id']);
    $tipo_documento         = trim($_POST['tipo_documento']);
    $nome_documento         = trim($_POST['nome_documento']);
    $nome_ficheiro_caminho  = trim($_POST['nome_ficheiro_caminho']);
    $data_documento         = trim($_POST['data_documento']);
    $data_validade          = !empty($_POST['data_validade']) ? trim($_POST['data_validade']) : null;

    // Array de Controlo para o ENUM da tabela
    $enums_validos = [
        'Manual de utilizador', 'Manual de serviço', 'Certificado de calibração', 
        'Contrato de manutenção', 'Fatura ou guia de aquisição', 
        'Declaração de conformidade', 'Relatório técnico'
    ];

    // Validar se os campos obrigatórios foram mantidos preenchidos
    if ($equipamento_id <= 0 || empty($nome_documento) || empty($nome_ficheiro_caminho) || empty($data_documento) || !in_array($tipo_documento, $enums_validos)) {
        $erro = "Todos os campos obrigatórios devem estar corretamente preenchidos.";
    } else {
        $sql_update = "UPDATE documentacao SET 
                        tipo_documento=?, nome_documento=?, nome_ficheiro_caminho=?, 
                        data_documento=?, data_validade=?, equipamento_id=? 
                       WHERE id=?";
                       
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "sssssii", 
                $tipo_documento, $nome_documento, $nome_ficheiro_caminho, 
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
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-file-pen me-2 text-warning"></i>Editar Registo de Documentação
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

                <form action="editar_documentacao.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo isset($documento) ? $documento['id'] : $id; ?>">

                    <div class="row g-3">
                        
                        <div class="col-12 col-md-6">
                            <label for="equipamento_id" class="form-label fw-semibold">Equipamento Médico</label>
                            <select class="form-select" id="equipamento_id" name="equipamento_id" required>
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
                            <label for="tipo_documento" class="form-label fw-semibold">Tipo de Documento</label>
                            <select class="form-select" id="tipo_documento" name="tipo_documento" required>
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
                            <label for="nome_documento" class="form-label fw-semibold">Nome Descritivo do Documento</label>
                            <input type="text" class="form-control" id="nome_documento" name="nome_documento" 
                                   value="<?php echo htmlspecialchars($_POST['nome_documento'] ?? $documento['nome_documento'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12">
                            <label for="nome_ficheiro_caminho" class="form-label fw-semibold">Caminho / Diretório do Ficheiro no PC ou Rede</label>
                            <input type="text" class="form-control" id="nome_ficheiro_caminho" name="nome_ficheiro_caminho" 
                                   value="<?php echo htmlspecialchars($_POST['nome_ficheiro_caminho'] ?? $documento['nome_ficheiro_caminho'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="data_documento" class="form-label fw-semibold">Data de Emissão / Documento</label>
                            <input type="date" class="form-control" id="data_documento" name="data_documento" 
                                   value="<?php echo htmlspecialchars($_POST['data_documento'] ?? $documento['data_documento'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="data_validade" class="form-label fw-semibold">Data de Validade (Opcional)</label>
                            <input type="date" class="form-control" id="data_validade" name="data_validade" 
                                   value="<?php echo htmlspecialchars($_POST['data_validade'] ?? $documento['data_validade'] ?? ''); ?>">
                        </div>

                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="../listar/lista_documentos.php" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-warning text-dark px-4 fw-semibold">
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