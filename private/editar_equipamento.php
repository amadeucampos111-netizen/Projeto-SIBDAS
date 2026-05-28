<?php
session_start();

// Configurações da Base de Dados
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "medtrack_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die("Falha na ligação: " . mysqli_connect_error()); }

// ==========================================
// AÇÃO 1: RECUPERAR OS DADOS ATUAIS PARA O FORMULÁRIO
// ==========================================

$equipamento = null;

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM equipamentos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $equipamento = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['mensagem_erro'] = "Equipamento não encontrado.";
        header("Location: gestao_equip.php");
        exit;
    }
    mysqli_stmt_close($stmt);
} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: gestao_equip.php");
    exit;
}

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
    $observacoes     = trim($_POST['observacoes']);

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
            $_SESSION['mensagem_sucesso'] = "Ficha do equipamento atualizada com sucesso!";
            header("Location: gestao_equip.php");
            exit;
        } else {
            $erro = mysqli_stmt_error($stmt_update);
        }
        mysqli_stmt_close($stmt_update);
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
    <style>
        body { font-family: 'Titillium Web', sans-serif; background-color: #f8f9fa; }
        .bg-custom-verde { background-color: #289224 !important; }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square me-2 text-warning"></i>Editar Equipamento Técnico</h4>
                    <a href="gestao_equip.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Voltar</a>
                </div>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger"><?php echo $erro; ?></div>
                <?php endif; ?>

                <!-- Formulário com Action apontada para si mesmo -->
                <form action="editar_equipamento.php" method="POST">
                    <!-- Campo Oculto (Hidden) crucial para saber qual ID estamos a atualizar -->
                    <input type="hidden" name="id" value="<?php echo $equipamento['id']; ?>">

                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código de Inventário</label>
                            <input type="text" class="form-control" name="codigo_interno" value="<?php echo htmlspecialchars($equipamento['codigo_interno']); ?>" required>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Designação do Equipamento</label>
                            <input type="text" class="form-control" name="designacao" value="<?php echo htmlspecialchars($equipamento['designacao']); ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Categoria Prática</label>
                            <select class="form-select" name="categoria" required>
                                <?php
                                $categorias = ["Monitorização", "Suporte de vida", "Terapia", "Diagnóstico", "Laboratório", "Esterilização", "Reabilitação"];
                                foreach ($categorias as $cat) {
                                    $selected = ($equipamento['categoria'] == $cat) ? 'selected' : '';
                                    echo "<option value='$cat' $selected>$cat</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Marca</label>
                            <input type="text" class="form-control" name="marca" value="<?php echo htmlspecialchars($equipamento['marca']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Modelo</label>
                            <input type="text" class="form-control" name="modelo" value="<?php echo htmlspecialchars($equipamento['modelo']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Número de Série</label>
                            <input type="text" class="form-control" name="numero_serie" value="<?php echo htmlspecialchars($equipamento['numero_serie']); ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Fabricante</label>
                            <input type="text" class="form-control" name="fabricante" value="<?php echo htmlspecialchars($equipamento['fabricante']); ?>" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Data de Aquisição</label>
                            <input type="date" class="form-control" name="data_aquisicao" value="<?php echo $equipamento['data_aquisicao']; ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Ano de Fabrico</label>
                            <input type="number" class="form-control" name="ano_fabrico" value="<?php echo $equipamento['ano_fabrico']; ?>" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Custo (€)</label>
                            <input type="number" step="0.01" class="form-control" name="custo_aquisicao" value="<?php echo $equipamento['custo_aquisicao']; ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo de Entrada</label>
                            <select class="form-select" name="tipo_entrada" required>
                                <?php
                                $entradas = ["Compra", "Doação", "Aluguer", "Empréstimo"];
                                foreach ($entradas as $ent) {
                                    $selected = ($equipamento['tipo_entrada'] == $ent) ? 'selected' : '';
                                    echo "<option value='$ent' $selected>$ent</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Localização Hospitalar</label>
                            <select class="form-select" name="localizacao_id" required>
                                <!-- Aqui idealmente faria um loop à tabela 'localizaciones'. Para fins de teste mantemos os IDs mapeados -->
                                <option value="1" <?php if($equipamento['localizacao_id'] == 1) echo 'selected'; ?>>Urgência Geral (Sala Reanimação 1)</option>
                                <option value="2" <?php if($equipamento['localizacao_id'] == 2) echo 'selected'; ?>>Bloco Operatório Central (Sala 3)</option>
                                <option value="3" <?php if($equipamento['localizacao_id'] == 3) echo 'selected'; ?>>UCI (Box 5)</option>
                                <option value="4" <?php if($equipamento['localizacao_id'] == 4) echo 'selected'; ?>>Imagiologia (Sala TAC)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estado Técnico</label>
                            <select class="form-select" name="estado_atual" required>
                                <?php
                                $estados = ["Ativo", "Em manutenção", "Inativo", "Em calibração", "Em quarentena", "Abatido"];
                                foreach ($estados as $est) {
                                    $selected = ($equipamento['estado_atual'] == $est) ? 'selected' : '';
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
                                    $selected = ($equipamento['criticidade'] == $crit) ? 'selected' : '';
                                    echo "<option value='$crit' $selected>$crit</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações Técnicas</label>
                            <textarea class="form-control" name="observacoes" rows="3"><?php echo htmlspecialchars($equipamento['observacoes']); ?></textarea>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="gestao_equip.php" class="btn btn-light px-4">Cancelar</a>
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