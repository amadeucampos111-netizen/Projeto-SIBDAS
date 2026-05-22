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


// Consulta para contar os equipamentos que têm o estado igual a 'Ativo'
$query_equip = "SELECT COUNT(*) as total FROM equipamentos WHERE estado_atual = 'Ativo'";
$result_equip = mysqli_query($conn, $query_equip);
$data_equip = mysqli_fetch_assoc($result_equip);
$total_equipamentos_ativos = $data_equip['total'];
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Apoio ao Inventário Hospitalar</title>
    <link rel="shortcut icon" href="assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/admin1240896.css">
    
    
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="fa-solid fa-square-heart me-2"></i> MedTrack
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestao_equip.php"><i class="fa-solid fa-microscope me-1"></i> Equipamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="fornecedores.php"><i class="fa-solid fa-truck-medical me-1"></i> Fornecedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="documentacao.php"><i class="fa-solid fa-file-invoice me-1"></i> Documentação</a>
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
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-dark mb-1">Painel de Controlo</h2>
                <p class="text-muted">Bem-vindo ao sistema de gestão do Inventário Hospitalar.</p>
            </div>
        </div>

        <div class="row g-4 mt-2">
            
            <div class="col-12 col-md-4 col-lg-3">
                <div class="card card-stats border-indicador-verde h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Equipamentos</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_equipamentos) ? $total_equipamentos : '0'; ?></h3>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-stethoscope"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                <div class="card card-stats border-indicador-verde h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Equipamentos ativos</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_equipamentos_ativos) ? $total_equipamentos_ativos : '0'; ?></h3>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-stethoscope"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
                <div class="card card-stats border-indicador-azul h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Fornecedores</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_fornecedores) ? $total_fornecedores : '0'; ?></h3>
                        </div>
                        <div class="icon-box bg-success bg-opacity-10 text-custom-verde">
                            <i class="fa-solid fa-building-shield"></i>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>