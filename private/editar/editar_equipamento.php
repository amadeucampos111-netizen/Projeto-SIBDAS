<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // 3. Expulsar o intruso de volta para o formulário de login
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

// ==========================================
// AÇÃO 1: RECUPERAR OS DADOS ATUAIS PARA O FORMULÁRIO
// ==========================================

$equipamento = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM equipamentos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $id_param = $id; // atribuição explícita por segurança
    mysqli_stmt_bind_param($stmt, "i", $id_param);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $equipamento = mysqli_fetch_assoc($result);
        
        // --- CORREÇÃO DO ERRO DE DEPRECATED ---
        // Se as observações na BD forem NULL ou vazias, define como "Sem observações"
        $obs_limpas = trim($equipamento['observacoes'] ?? '');
        $equipamento['observacoes'] = ($obs_limpas !== '') ? $obs_limpas : "Sem observações";
        
    } else {
        $_SESSION['mensagem_erro'] = "Equipamento não encontrado.";
        header("Location: ../listar/listar_equipamentos.php");
        exit;
    }
    mysqli_stmt_close($stmt);
} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../listar/listar_equipamentos.php");
    exit;
}

// Inicializa a variável de erro para evitar warnings na renderização HTML
$erro = null;

// ==========================================
// AÇÃO 2: PROCESSAR A ATUALIZAÇÃO (UPDATE)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id              = intval($_POST['id']);
    $codigo_interno  = trim($_POST['codigo_interno']);
    $designacao      = trim($_POST['designacao']);
    $categoria       = $_POST['categoria'];
    $marca           = trim($_POST['marca']);
    $modelo          = trim($_POST['modelo']);
    $numero_serie    = trim($_POST['numero_serie']);
    $fabricante      = trim($_POST['fabricante']);
    $data_aquisicao  = $_POST['data_aquisicao'];
    $ano_fabrico     = intval($_POST['ano_fabrico']);
    $custo_aquisicao = floatval($_POST['custo_aquisicao']);
    $tipo_entrada    = $_POST['tipo_entrada'];
    $localizacao_id  = intval($_POST['localizacao_id']);
    $estado_atual    = $_POST['estado_atual'];
    $criticidade     = $_POST['criticidade'];
    
    // Validação robusta contra valores nulos ou vazios no POST
    $observacoes = isset($_POST['observacoes']) ? trim($_POST['observacoes']) : '';
    if ($observacoes === '') {
        $observacoes = "Sem observações";
    }

    // ==========================================
    // NOVA SECÇÃO: VALIDAÇÃO DAS DATAS
    // ==========================================
    $erros_data = [];
    $ano_atual = intval(date('Y'));

    // 1. Validar a estrutura real da data de aquisição
    $d = DateTime::createFromFormat('Y-m-d', $data_aquisicao);
    $data_valida = $d && $d->format('Y-m-d') === $data_aquisicao;

    if (!$data_valida) {
        $erros_data[] = "A data de aquisição introduzida é inválida.";
    } else {
        $ano_aquisicao = intval($d->format('Y'));
        
        // Impedir que a data de aquisição seja no futuro
        if ($d > new DateTime()) {
            $erros_data[] = "A data de aquisição não pode ser uma data futura.";
        }
    }

    // 2. Validar o ano de fabrico
    if ($ano_fabrico < 1900 || $ano_fabrico > $ano_atual) {
        $erros_data[] = "O ano de fabrico deve ser um ano válido entre 1900 e " . $ano_atual . ".";
    }

    // 3. Validar a coerência cronológica entre ambas
    if (empty($erros_data)) {
        if ($ano_aquisicao < $ano_fabrico) {
            $erros_data[] = "A data de aquisição não pode ser anterior ao ano de fabrico do equipamento.";
        }
    }
    // ==========================================

    // SÓ EXECUTA O UPDATE SE NÃO HOUVER ERROS DE VALIDAÇÃO
    if (empty($erros_data)) {
        $sql_update = "UPDATE equipamentos SET 
                        codigo_interno=?, designacao=?, categoria=?, marca=?, modelo=?, 
                        numero_serie=?, fabricante=?, data_aquisicao=?, ano_fabrico=?, 
                        custo_aquisicao=?, tipo_entrada=?, localizacao_id=?, estado_atual=?, 
                        criticidade=?, observacoes=? 
                       WHERE id=?";
                       
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "ssssssssidsisssi", 
                $codigo_interno, $designacao, $categoria, $marca, $modelo,
                $numero_serie, $fabricante, $data_aquisicao, $ano_fabrico,
                $custo_aquisicao, $tipo_entrada, $localizacao_id, $estado_atual,
                $criticidade, $observacoes, $id
            );
            
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION['mensagem_sucesso'] = "Ficha do equipamento updated com sucesso!";
                header("Location: ../listar/listar_equipamentos.php");
                exit;
            } else {
                $erro = mysqli_stmt_error($stmt_update);
            }
            mysqli_stmt_close($stmt_update);
        }
    } else {
        // Junta todos os erros de data numa única string para exibir na interface
        $erro = implode("<br>", $erros_data);
        
        // Realimenta a variável $equipamento para que o formulário não quebre ao falhar a submissão
        $equipamento = $_POST;
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Equipamento | MedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/admin1240896.css">
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>Editar Equipamento Técnico</h4>
                    <a href="../listar/listar_equipamentos.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Voltar</a>
                </div>

                <?php if (!empty($erro)): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>

                <form action="editar_equipamento.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($equipamento['id'] ?? ''); ?>">

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código de Inventário</label>
                            <input type="text" class="form-control" name="codigo_interno" value="<?php echo htmlspecialchars($equipamento['codigo_interno'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Designação do Equipamento</label>
                            <input type="text" class="form-control" name="designacao" value="<?php echo htmlspecialchars($equipamento['designacao'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Categoria Prática</label>
                            <select class="form-select" name="categoria" required>
                                <?php
                                $categorias = ["Monitorização", "Suporte de vida", "Terapia", "Diagnóstico", "Laboratório", "Esterilização", "Reabilitação"];
                                foreach ($categorias as $cat) {
                                    $selected = (isset($equipamento['categoria']) && $equipamento['categoria'] == $cat) ? 'selected' : '';
                                    echo "<option value='$cat' $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Marca</label>
                            <input type="text" class="form-control" name="marca" value="<?php echo htmlspecialchars($equipamento['marca'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Modelo</label>
                            <input type="text" class="form-control" name="modelo" value="<?php echo htmlspecialchars($equipamento['modelo'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Número de Série</label>
                            <input type="text" class="form-control" name="numero_serie" value="<?php echo htmlspecialchars($equipamento['numero_serie'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fabricante</label>
                            <input type="text" class="form-control" name="fabricante" value="<?php echo htmlspecialchars($equipamento['fabricante'] ?? ''); ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Data de Aquisição</label>
                            <input type="date" class="form-control" name="data_aquisicao" value="<?php echo htmlspecialchars($equipamento['data_aquisicao'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Ano de Fabrico</label>
                            <input type="number" class="form-control" name="ano_fabrico" value="<?php echo htmlspecialchars($equipamento['ano_fabrico'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Custo (€)</label>
                            <input type="number" step="0.01" class="form-control" name="custo_aquisicao" value="<?php echo htmlspecialchars($equipamento['custo_aquisicao'] ?? ''); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo de Entrada</label>
                            <select class="form-select" name="tipo_entrada" required>
                                <?php
                                $entradas = ["Compra", "Doação", "Aluguer", "Empréstimo"];
                                foreach ($entradas as $ent) {
                                    $selected = (isset($equipamento['tipo_entrada']) && $equipamento['tipo_entrada'] == $ent) ? 'selected' : '';
                                    echo "<option value='$ent' $selected>$ent</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Localização Hospitalar</label>
                            <select class="form-select" name="localizacao_id" required>
                                <option value="" disabled <?php if(!isset($equipamento['localizacao_id'])) echo 'selected'; ?>>Selecione uma localização...</option>
                                <?php
                                if ($conn) {
                                    $sql_loc = "SELECT id, edificio, piso, servico_departamento, sala_gabinete FROM localizaciones ORDER BY edificio ASC";
                                    $res_loc = mysqli_query($conn, $sql_loc);
                                    
                                    if ($res_loc) {
                                        while ($loc = mysqli_fetch_assoc($res_loc)) {
                                            $selected = (isset($equipamento['localizacao_id']) && $equipamento['localizacao_id'] == $loc['id']) ? 'selected' : '';
                                            echo "<option value='{$loc['id']}' {$selected}>" . htmlspecialchars($loc['edificio'] . " - " . $loc['piso'] . " - " . $loc['servico_departamento'] . " - " . $loc['sala_gabinete'], ENT_QUOTES, 'UTF-8') . "</option>";
                                        }
                                    } else {
                                        echo "<option value='' disabled>Erro ao carregar localizações: " . mysqli_error($conn) . "</option>";
                                    }
                                } else {
                                    echo "<option value='' disabled>Falha na ligação à Base de Dados</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estado Técnico</label>
                            <select class="form-select" name="estado_atual" required>
                                <?php
                                $estados = ["Ativo", "Em manutenção", "Inativo", "Em calibração", "Em quarentena", "Abatido"];
                                foreach ($estados as $est) {
                                    $selected = (isset($equipamento['estado_atual']) && $equipamento['estado_atual'] == $est) ? 'selected' : '';
                                    echo "<option value='$est' $selected>$est</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Grau de Criticidade</label>
                            <select class="form-select" name="criticidade" required>
                                <?php
                                $criticidades = ["Baixa", "Média", "Alta", "Suporte de vida"];
                                foreach ($criticidades as $crit) {
                                    $selected = (isset($equipamento['criticidade']) && $equipamento['criticidade'] == $crit) ? 'selected' : '';
                                    echo "<option value='$crit' $selected>$crit</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações Técnicas</label>
                            <textarea class="form-control" name="observacoes" rows="3"><?php echo htmlspecialchars($equipamento['observacoes'] ?? ''); ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="../listar/listar_equipamentos.php" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn bg-custom-verde text-white px-4">
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