<?php
include('conect_bd.php');

// Contar quantos equipamentos existem
$query_equip = "SELECT COUNT(*) as total FROM equipamentos";
$result_equip = mysqli_query($conn, $query_equip);
$data_equip = mysqli_fetch_assoc($result_equip);
$total_equipamentos = $data_equip['total'];

// Contar quantos fornecedores existem
$query_forn = "SELECT COUNT(*) as total FROM fornecedores";
$result_forn = mysqli_query($conn, $query_forn);
$data_forn = mysqli_fetch_assoc($result_forn);
$total_fornecedores = $data_forn['total'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Apoio ao Inventário Hospitalar</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin1240896.css">
    
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="#">
            <i class="fa-solid fa-heart-pulse me-2"></i>
            <span><strong>MedTrack</strong> | Inventário</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="equipamentos.php"><i class="fa-solid fa-stethoscope me-1"></i> Equipamentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="fornecedores.php"><i class="fa-solid fa-truck-medical me-1"></i> Fornecedores</a>
                </li>
            </ul>
            
            <div class="d-flex align-items-center text-white">
                <span class="me-3">
                    <i class="fa-solid fa-user-circle me-1"></i> 
                </span>
                <a href="../public/index.php" class="btn btn-sm btn-outline-light d-flex align-items-center">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">Gestão de Dispositivos Médicos</h2>
            <p class="text-muted">Monitorização e ciclo de vida tecnológico do inventário hospitalar.</p>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi bg-primary text-white h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold text-white-50 small mb-1">Total Dispositivos</h6>
                        <h3 class="display-6 fw-bold mb-0"><?php echo $total_eq; ?></h3>
                    </div>
                    <div class="icon-shape">
                        <i class="fa-solid fa-microscope"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi bg-danger text-white h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold text-white-50 small mb-1">Alta Criticidade</h6>
                        <h3 class="display-6 fw-bold mb-0"><?php echo $criticos; ?></h3>
                    </div>
                    <div class="icon-shape">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi bg-warning text-dark h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold text-dark-50 small mb-1">Empréstimos Inter-Serviços</h6>
                        <h3 class="display-6 fw-bold mb-0"><?php echo $emprestimos; ?></h3>
                    </div>
                    <div class="icon-shape text-dark" style="background: rgba(0,0,0,0.08);">
                        <i class="fa-solid fa-right-left"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card card-kpi bg-info text-white h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-uppercase fw-semibold text-white-50 small mb-1">Doc. Em Falta</h6>
                        <h3 class="display-6 fw-bold mb-0"><?php echo $docs_em_falta; ?></h3>
                    </div>
                    <div class="icon-shape">
                        <i class="fa-solid fa-file-circle-xmark"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold m-0 text-secondary"><i class="fa-solid fa-bell me-2 text-warning"></i>Últimos Equipamentos Monitorizados</h5>
                    <button class="btn btn-sm btn-primary"><i class="fa-solid fa-plus me-1"></i> Novo Registo</button>
                </div>
                
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Série / ID</th>
                            <th>Equipamento</th>
                            <th>Serviço / Localização</th>
                            <th>Criticidade</th>
                            <th>Estado</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span class="text-monospace font-weight-bold">#VP-2026-09</span></td>
                            <td>
                                <strong>Ventilador Pulmonar</strong><br>
                                <small class="text-muted">Modelo: Puritan Bennett 980</small>
                            </td>
                            <td>Bloco Operatório Central</td>
                            <td><span class="badge bg-danger">Máxima (Nível 3)</span></td>
                            <td><span class="badge bg-success-subtle text-success border border-success-subtle">Em Operação</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border"><i class="fa-regular fa-eye"></i></button>
                                <button class="btn btn-sm btn-light border text-primary"><i class="fa-regular fa-pen-to-square"></i></button>
                            </td>
                        </tr>
                        <tr>
                            <td><span class="text-monospace">#MM-2026-44</span></td>
                            <td>
                                <strong>Monitor Multiparamétrico</strong><br>
                                <small class="text-muted">Modelo: Mindray BeneVision N17</small>
                            </td>
                            <td>Unidade de Cuidados Intensivos</td>
                            <td><span class="badge bg-warning text-dark">Média (Nível 2)</span></td>
                            <td><span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Sob Manutenção</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border"><i class="fa-regular fa-eye"></i></button>
                                <button class="btn btn-sm btn-light border text-primary"><i class="fa-regular fa-pen-to-square"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>