<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // 3. Expulsar o intruso de volta para o formulário de login
    header("Location: ../../public/login.php?erro=restrito");
    exit; // Interrompe imediatamente a execução do resto da página
}

$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", 10464);

if (!$conn) { die("Falha na ligação: " . mysqli_connect_error()); }

$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);
$contrato = null;

if($id > 0) {
    $res = mysqli_query($conn, "SELECT * FROM garantias_contratos WHERE id = $id");
    $contrato = mysqli_fetch_assoc($res);
}

// ==========================================
// PROCESSAR A ATUALIZAÇÃO (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $contrato) {
    $equipamento_id          = intval($_POST['equipamento_id']);
    $entidade_responsavel_id = !empty($_POST['entidade_responsavel_id']) ? intval($_POST['entidade_responsavel_id']) : null;
    $data_inicio_garantia    = !empty($_POST['data_inicio_garantia']) ? trim($_POST['data_inicio_garantia']) : null;
    $data_fim_garantia       = !empty($_POST['data_fim_garantia']) ? trim($_POST['data_fim_garantia']) : null;
    $tem_contrato_manutencao = isset($_POST['tem_contrato_manutencao']) ? 1 : 0;
    $tipo_contrato           = ($tem_contrato_manutencao === 1) ? trim($_POST['tipo_contrato']) : null;
    $periodicidade           = ($tem_contrato_manutencao === 1) ? trim($_POST['periodicidade']) : null;
    $observacoes             = !empty($_POST['observacoes']) ? trim($_POST['observacoes']) : null;

    // ==========================================
    // NOVA SECÇÃO: VALIDAÇÃO DAS DATAS (OPCIONAIS)
    // ==========================================
    $erros_data = [];
    $d_inicio = null;
    $d_fim = null;

    // 1. Validar data de início (se preenchida)
    if ($data_inicio_garantia !== null) {
        $d_inicio = DateTime::createFromFormat('Y-m-d', $data_inicio_garantia);
        if (!$d_inicio || $d_inicio->format('Y-m-d') !== $data_inicio_garantia) {
            $erros_data[] = "A data de início da garantia introduzida é inválida.";
        }
    }

    // 2. Validar data de fim (se preenchida)
    if ($data_fim_garantia !== null) {
        $d_fim = DateTime::createFromFormat('Y-m-d', $data_fim_garantia);
        if (!$d_fim || $d_fim->format('Y-m-d') !== $data_fim_garantia) {
            $erros_data[] = "A data de fim da garantia introduzida é inválida.";
        }
    }

    // 3. Validar a coerência cronológica entre ambas (se ambas forem válidas)
    if ($d_inicio && $d_fim) {
        if ($d_fim < $d_inicio) {
            $erros_data[] = "A data de fim da garantia não pode ser anterior à data de início.";
        }
    }
    // ==========================================

    // Se existirem problemas nas datas, armazena para exibir no alert do Bootstrap
    if (!empty($erros_data)) {
        $erro = implode("<br>", $erros_data);
    } else {
        // Sem erros: Procede para a atualização segura
        $sql = "UPDATE garantias_contratos SET equipamento_id=?, data_inicio_garantia=?, data_fim_garantia=?, tem_contrato_manutencao=?, tipo_contrato=?, entidade_responsavel_id=?, periodicidade=?, observacoes=? WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        if($stmt) {
            mysqli_stmt_bind_param($stmt, "issssissi", $equipamento_id, $data_inicio_garantia, $data_fim_garantia, $tem_contrato_manutencao, $tipo_contrato, $entidade_responsavel_id, $periodicidade, $observacoes, $id);
            if(mysqli_stmt_execute($stmt)) {
                $_SESSION['msg_sucesso'] = "Alterações gravadas com sucesso!";
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                header("Location: ../listar/lista_garantias.php");
                exit;
            } else { 
                $erro = "Erro ao atualizar: " . mysqli_stmt_error($stmt); 
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Se o registo não existe e não é um POST válido, expulsa
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
        <h5 class="fw-bold border-bottom pb-2 mb-3 text-dark">
            <i class="fa-solid fa-file-pen text-warning me-2"></i>Editar Garantia
        </h5>
        
        <?php if(isset($erro)): ?>
            <div class="alert alert-danger d-flex align-items-center">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <div><?php echo $erro; ?></div>
            </div>
        <?php endif; ?>
        
        <form action="editar_garantia.php" method="POST">
            <input type="hidden" name="id" value="<?php echo $contrato['id']; ?>">
            
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Equipamento</label>
                    <select class="form-select" name="equipamento_id" required>
                        <?php
                        $eq_selecionado = $_POST['equipamento_id'] ?? $contrato['equipamento_id'];
                        $res = mysqli_query($conn, "SELECT e.id, e.designacao, e.numero_serie FROM equipamentos e LEFT JOIN garantias_contratos gc ON e.id = gc.equipamento_id WHERE gc.id IS NULL OR e.id = {$contrato['equipamento_id']} ORDER BY e.designacao ASC");
                        while($row = mysqli_fetch_assoc($res)) {
                            $sel = ($row['id'] == $eq_selecionado) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $sel>{$row['designacao']} (S/N: {$row['numero_serie']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Entidade Responsável</label>
                    <select class="form-select" name="entidade_responsavel_id">
                        <?php 
                        $entidade_selecionada = isset($_POST['equipamento_id']) ? ($_POST['entidade_responsavel_id'] ?? '') : $contrato['entidade_responsavel_id']; 
                        ?>
                        <option value="" <?php echo empty($entidade_selecionada) ? 'selected' : ''; ?>>Gestão Interna</option>
                        <?php
                        $res = mysqli_query($conn, "SELECT id, nome_empresa FROM fornecedores ORDER BY nome_empresa ASC");
                        while($row = mysqli_fetch_assoc($res)) {
                            $sel = ($row['id'] == $entidade_selecionada) ? 'selected' : '';
                            echo "<option value='{$row['id']}' $sel>{$row['nome_empresa']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="bg-light p-3 rounded border mb-3">
                <h6 class="fw-bold text-secondary mb-2">Garantia do Fabricante</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small">Início</label>
                        <input type="date" class="form-control" name="data_inicio_garantia" value="<?php echo htmlspecialchars($_POST['data_inicio_garantia'] ?? $contrato['data_inicio_garantia'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small">Fim</label>
                        <input type="date" class="form-control" name="data_fim_garantia" value="<?php echo htmlspecialchars($_POST['data_fim_garantia'] ?? $contrato['data_fim_garantia'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <?php 
            // Controlar o estado do switch e inputs se houver um recarregamento por erro
            $switch_checked = isset($_POST['equipamento_id']) ? isset($_POST['tem_contrato_manutencao']) : (bool)$contrato['tem_contrato_manutencao'];
            $tipo_atual = $_POST['tipo_contrato'] ?? $contrato['tipo_contrato'] ?? '';
            $periodicidade_atual = $_POST['periodicidade'] ?? $contrato['periodicidade'] ?? '';
            ?>

            <div class="p-3 rounded border mb-3" style="background-color: #fffdf0;">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="switchContrato" name="tem_contrato_manutencao" value="1" <?php if($switch_checked) echo 'checked'; ?> onchange="document.getElementById('divContrato').style.display = this.checked ? 'block' : 'none'">
                    <label class="form-check-label fw-bold" for="switchContrato">Possui Contrato de Manutenção?</label>
                </div>
                <div id="divContrato" style="display:<?php echo $switch_checked ? 'block' : 'none'; ?>;">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small">Tipo de Contrato</label>
                            <input type="text" class="form-control" name="tipo_contrato" value="<?php echo htmlspecialchars($tipo_atual); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Periodicidade</label>
                            <select class="form-select" name="periodicidade">
                                <option value="Trimestral" <?php if($periodicidade_atual == 'Trimestral') echo 'selected'; ?>>Trimestral</option>
                                <option value="Semestral" <?php if($periodicidade_atual == 'Semestral') echo 'selected'; ?>>Semestral</option>
                                <option value="Anual" <?php if($periodicidade_atual == 'Anual') echo 'selected'; ?>>Anual</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Observações</label>
                <textarea class="form-control" name="observacoes" rows="2"><?php echo htmlspecialchars($_POST['observacoes'] ?? $contrato['observacoes'] ?? ''); ?></textarea>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="../listar/lista_garantias.php" class="btn btn-light">Cancelar</a>
                <button type="submit" class="btn btn-warning text-dark fw-semibold">Atualizar Registo</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<?php mysqli_close($conn); ?>