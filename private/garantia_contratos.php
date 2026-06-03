<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "medtrack_db");


?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Nova Garantia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>
<div class="container mt-5">
    <div class="card p-4 shadow-sm border-0">
        <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-shield me-2 text-primary"></i>Registar Garantia / Contrato</h5>
            <a href="listar_garantias.php" class="btn btn-outline-secondary btn-sm">Ver Todos</a>
        </div>

        <?php if (isset($erro)): ?> <div class="alert alert-danger"><?php echo $erro; ?></div> <?php endif; ?>

        <form action="inserir/inserir_garantia_contrato.php" method="POST">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Equipamento Médico (Sem contrato ativo)</label>
                    <select class="form-select" name="equipamento_id" required>
                        <option value="" selected disabled>Selecione...</option>
                        <?php
                        $res = mysqli_query($conn, "SELECT e.id, e.designacao, e.numero_serie FROM equipamentos e LEFT JOIN garantias_contratos gc ON e.id = gc.equipamento_id WHERE gc.id IS NULL ORDER BY e.designacao ASC");
                        while($row = mysqli_fetch_assoc($res)) echo "<option value='{$row['id']}'>{$row['designacao']} (S/N: {$row['numero_serie']})</option>";
                        ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Entidade Responsável (Fornecedor)</label>
                    <select class="form-select" name="entidade_responsavel_id">
                        <option value="">Gestão Interna</option>
                        <?php
                        $res = mysqli_query($conn, "SELECT id, nome_empresa FROM fornecedores ORDER BY nome_empresa ASC");
                        while($row = mysqli_fetch_assoc($res)) echo "<option value='{$row['id']}'>{$row['nome_empresa']}</option>";
                        ?>
                    </select>
                </div>
            </div>

            <div class="bg-light p-3 rounded border mb-3">
                <h6 class="fw-bold text-secondary mb-2">Garantia do Fabricante</h6>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label small">Início</label><input type="date" class="form-control" name="data_inicio_garantia"></div>
                    <div class="col-md-6"><label class="form-label small">Fim</label><input type="date" class="form-control" name="data_fim_garantia"></div>
                </div>
            </div>

            <div class="p-3 rounded border mb-3" style="background-color: #f0f7ff;">
                <div class="form-check form-switch mb-2">
                    <input class="form-check-input" type="checkbox" id="switchContrato" name="tem_contrato_manutencao" value="1" onchange="document.getElementById('divContrato').style.display = this.checked ? 'block' : 'none'">
                    <label class="form-check-label fw-bold" for="switchContrato">Possui Contrato de Manutenção?</label>
                </div>
                <div id="divContrato" style="display:none;">
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label small">Tipo de Contrato</label><input type="text" class="form-control" name="tipo_contrato" placeholder="Ex: Preventivo, Total"></div>
                        <div class="col-md-6">
                            <label class="form-label small">Periodicidade</label>
                            <select class="form-select" name="periodicidade">
                                <option value="Trimestral">Trimestral</option>
                                <option value="Semestral" selected>Semestral</option>
                                <option value="Anual">Anual</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3"><label class="form-label fw-semibold">Observações</label><textarea class="form-control" name="observacoes" rows="2"></textarea></div>
            <button type="submit" class="btn btn-primary w-100 fw-semibold">Gravar Cobertura</button>
        </form>
    </div>
</div>
</body>
</html>