<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localizações | Apoio ao Inventário Hospitalar</title>
    <link rel="shortcut icon" href="assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/admin1240896.css">
    
    
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
                <i class="fa-solid fa-square-heart me-2"></i> MedTrack
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestao_equip.php"><i class="fa-solid fa-microscope me-1"></i> Equipamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
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
    
    <!-- Inserir nova localização -->
    <div class="card card-custom p-4 mb-4 shadow-sm border-0 rounded-3">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-primary">
        <i class="fa-solid fa-map-location-dot fs-4 me-2"></i>
        <h5 class="fw-bold mb-0 text-dark">Inserir Nova Localização Hospitalar</h5>
    </div>
    
    <form action="inserir_localizacao.php" method="POST">
        <div class="row g-3">
            
            <div class="col-12 col-md-3">
                <label for="edificio" class="form-label fw-semibold">Edifício / Bloco</label>
                <input type="text" class="form-control" id="edificio" name="edificio" placeholder="Ex: Edifício Central" required>
            </div>

            <div class="col-12 col-md-2">
                <label for="piso" class="form-label fw-semibold">Piso / Andar</label>
                <input type="text" class="form-control" id="piso" name="piso" placeholder="Ex: 0, 1, -1" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="servico_departamento" class="form-label fw-semibold">Serviço ou Departamento</label>
                <input type="text" class="form-control" id="servico_departamento" name="servico_departamento" placeholder="Ex: Urgência Geral, UCI, Bloco Operatório" required>
            </div>

            <div class="col-12 col-md-3">
                <label for="sala_gabinete" class="form-label fw-semibold">Sala / Gabinete / Box</label>
                <input type="text" class="form-control" id="sala_gabinete" name="sala_gabinete" placeholder="Ex: Sala Reanimação 1, Box 5" required>
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar</button>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="fa-solid fa-floppy-disk me-1"></i> Gravar Localização
            </button>
        </div>
    </form>
</div>
</body>
</html>