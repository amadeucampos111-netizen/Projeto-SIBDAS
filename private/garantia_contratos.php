<?php
session_start();


$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", 10464);


?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Nova Garantia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="assets/img/hosp_icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin1240896.css">
   
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="assets/img/hosp_icon_branco.png" alt="Logo" width="150" height="100" class="d-inline-block align-text-top me-2">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="gestao_equip.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-microscope me-1"></i> Equipamentos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item dropdown-item" href="gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos e Componentes</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/lista_componentes.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="garantia_contratos.php"><i class="fa-solid fa-file-shield me-2"></i> Registo de Garantias e Contratos</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/lista_garantias.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias e Contratos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle " href="fornecedores.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-truck-medical me-1"></i> Fornecedores
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item dropdown-item" href="fornecedores.php"><i class="fa-solid fa-address-book me-2"></i> Registo, Associação e Desassociação de Fornecedores</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/lista_fornecedores.php"><i class="fa-solid fa-user-plus me-2"></i> Listagem de Fornecedores</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="documentacao.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-file-invoice me-1"></i> Documentação
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item dropdown-item" href="documentacao.php"><i class="fa-solid fa-folder-tree me-2"></i> Registo de Documentação</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/lista_documentos.php"><i class="fa-solid fa-folder-plus me-2"></i> Listagem de Documentos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="pesq_avan.php"><i class="fa-solid fa-magnifying-glass me-1"></i> Pesquisa</a>
                </li>
            </ul>
            
            <div class="d-flex">
                <a href="../public/index.html" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="card p-4 shadow-sm border-0">
        <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-shield me-2 text-primary"></i>Registar Garantia / Contrato</h5>
            <a href="listar/lista_garantias.php" class="btn btn-outline-secondary btn-sm">Ver Todos</a>
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