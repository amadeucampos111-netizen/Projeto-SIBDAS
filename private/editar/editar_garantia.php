<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Destrói qualquer resíduo de sessão inválida
    session_unset();
    session_destroy();
    
    // Manda de volta para o formulário
    header("Location: ../../public/login.html");
    exit;
}

// 2. Opcional: Expiração automática por inatividade (Ex: 10 minutos = 600 segundos)
$tempo_maximo_inatividade = 600; 
if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso'] > $tempo_maximo_inatividade)) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.html?status=sessao_expirada");
    exit;
}
// Atualiza o relógio do último clique se ele continuar ativo
$_SESSION['ultimo_acesso'] = time();

$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", 10464);

$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);
$contrato = null;

if($id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM garantias_contratos WHERE id = $id");
    $contrato = mysqli_fetch_assoc($res);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $contrato) {
    $equipamento_id          = intval($_POST['equipamento_id']);
    $entidade_responsavel_id = !empty($_POST['entidade_responsavel_id']) ? intval($_POST['entidade_responsavel_id']) : null;
    $data_inicio_garantia    = !empty($_POST['data_inicio_garantia']) ? trim($_POST['data_inicio_garantia']) : null;
    $data_fim_garantia       = !empty($_POST['data_fim_garantia']) ? trim($_POST['data_fim_garantia']) : null;
    $tem_contrato_manutencao = isset($_POST['tem_contrato_manutencao']) ? 1 : 0;
    $tipo_contrato           = ($tem_contrato_manutencao === 1) ? trim($_POST['tipo_contrato']) : null;
    $periodicidade           = ($tem_contrato_manutencao === 1) ? trim($_POST['periodicidade']) : null;
    $observacoes             = !empty($_POST['observacoes']) ? trim($_POST['observacoes']) : null;

    $sql = "UPDATE garantias_contratos SET equipamento_id=?, data_inicio_garantia=?, data_fim_garantia=?, tem_contrato_manutencao=?, tipo_contrato=?, entidade_responsavel_id=?, periodicidade=?, observacoes=? WHERE id=?";
    $stmt = mysqli_prepare($conn, $sql);
    if($stmt) {
        mysqli_stmt_bind_param($stmt, "issssissi", $equipamento_id, $data_inicio_garantia, $data_fim_garantia, $tem_contrato_manutencao, $tipo_contrato, $entidade_responsavel_id, $periodicidade, $observacoes, $id);
        if(mysqli_stmt_execute($stmt)) {
            $_SESSION['msg_sucesso'] = "Alterações gravadas com sucesso!";
            header("Location: ../listar/lista_garantias.php");
            exit;
        } else { $erro = "Erro ao atualizar: " . mysqli_stmt_error($stmt); }
    }
}

if(!$contrato) { header("Location: ../listar/lista_garantias.php"); exit; }
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Editar Contrato</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 border-0 shadow-sm">
        <h5 class="fw-bold border-bottom pb-2 mb-3 text-dark"><i class="fa-solid fa-file-pen text-warning me-2"></i>Editar Garantia</h5>
        <?php if(isset($erro)): ?><div class="alert alert-danger"><?php echo $erro; ?></div><?php endif; ?>
        
        <form action="editar_garantia.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $contrato['id']; ?>">
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Equipamento</label>
                    <select class="form-select" name="equipamento_id" required>
                        <?php
                        // Lista o equipamento atual associado E também os outros que estão livres
                        $res = mysqli_query($conn, "SELECT e.id, e.designacao, e.numero_serie FROM equipamentos e LEFT JOIN garantias_contratos gc ON e.id = gc.equipamento_id WHERE gc.id IS NULL OR e.id = {$contrato['equipamento_id']} ORDER BY e.designacao ASC");
                        while($row = mysqli_fetch_assoc($res)) {
                            $sel = ($row['id'] == $contrato['equipamento_id']) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $sel>{$row['designacao']} (S/N: {$row['numero_serie']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Entidade Responsável</label>
                    <select class="form-select" name="entidade_responsavel_id">
                        <option value="">Gestão Interna</option>
                        <?php
                        $res = mysqli_query($conn, "SELECT id, nome_empresa FROM fornecedores ORDER BY nome_empresa ASC");
                        while($row = mysqli_fetch_assoc($res)) {
                            $sel = ($row['id'] == $contrato['entidade_responsavel_id']) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $sel>{$row['nome_empresa']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="bg-light p-3 rounded border mb-3">
                <h6 class="fw-bold text-secondary mb-2">Garantia do Fabricante</h6>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label small">Início</label><input type="date" class="form-control" name="data_inicio_garantia" value="<?php echo $contrato['data_inicio_garantia']; ?>"></div>
                    <div class="col-md-6"><label class="form-label small">Fim</label><input type="date" class="form-control" name="data_fim_garantia" value="<?php echo $contrato['data_fim_garantia']; ?>"></div>
                </div>
            </div>

            <div class="p-3 rounded border mb-3" style="background-color: #fffdf0;">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="switchContrato" name="tem_contrato_manutencao" value="1" <?php if($contrato['tem_contrato_manutencao']) echo 'checked'; ?> onchange="document.getElementById('divContrato').style.display = this.checked ? 'block' : 'none'">
                    <label class="form-check-label fw-bold" for="switchContrato">Possui Contrato de Manutenção?</label>
                </div>
                <div id="divContrato" style="display:<?php echo $contrato['tem_contrato_manutencao'] ? 'block' : 'none'; ?>;">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label small">Tipo de Contrato</label><input type="text" class="form-control" name="tipo_contrato" value="<?php echo htmlspecialchars($contrato['tipo_contrato'] ?? ''); ?>"></div>
                        <div class="col-md-6">
                            <label class="form-label small">Periodicidade</label>
                            <select class="form-select" name="periodicidade">
                                <option value="Trimestral" <?php if($contrato['periodicidade'] == 'Trimestral') echo 'selected'; ?>>Trimestral</option>
                                <option value="Semestral" <?php if($contrato['periodicidade'] == 'Semestral') echo 'selected'; ?>>Semestral</option>
                                <option value="Anual" <?php if($contrato['periodicidade'] == 'Anual') echo 'selected'; ?>>Anual</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3"><label class="form-label fw-semibold">Observações</label><textarea class="form-control" name="observacoes" rows="2"><?php echo htmlspecialchars($contrato['observacoes'] ?? ''); ?></textarea></div>
            <div class="d-flex justify-content-end gap-2">
                <a href="../listar/lista_garantias.php" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-warning text-dark fw-semibold">Atualizar Registo</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>