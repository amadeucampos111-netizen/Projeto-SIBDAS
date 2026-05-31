<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores | Apoio ao Inventário Hospitalar</title>
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
                        <a class="nav-link" href="localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fa-solid fa-truck-medical me-1"></i> Fornecedores</a>
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
    <div class="card p-4 mb-4 shadow-sm border-0 rounded-3">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-success">
        <i class="fa-solid fa-truck-field fs-4 me-2"></i>
        <h5 class="fw-bold mb-0 text-dark">Registar Novo Fornecedor / Parceiro</h5>
    </div>
    
    <form action="inserir_fornecedor.php" method="POST">
        <div class="row g-3">
            
            <div class="col-12 col-md-8">
                <label for="nome_empresa" class="form-label fw-semibold">Nome da Empresa / Entidade</label>
                <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" placeholder="Ex: Siemens Healthineers Portugal" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="nif" class="form-label fw-semibold">NIF (Contribuinte)</label>
                <input type="text" class="form-control" id="nif" name="nif" placeholder="Ex: 500123456" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="contacto_telefonico" class="form-label fw-semibold">Contacto Telefónico</label>
                <input type="text" class="form-control" id="contacto_telefonico" name="contacto_telefonico" placeholder="Ex: +351 919045678">
            </div>

            <div class="col-12 col-md-4">
                <label for="email" class="form-label fw-semibold">Email de Contacto</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ex: assistencia@empresa.pt" required>
            </div>

            <div class="col-12">
                <label for="morada" class="form-label fw-semibold">Morada Oficial</label>
                <input type="text" class="form-control" id="morada" name="morada" placeholder="Ex: Avenida da República, Nº 10, Lisboa">
            </div>

            <div class="col-12">
                <label for="website" class="form-label fw-semibold">Website</label>
                <input type="text" class="form-control" id="website" name="website" placeholder="Ex: www.empresa.pt">
            </div>

            <div class="col-12">
                <label for="pessoa_contacto" class="form-label fw-semibold">Pessoa de Contacto</label>
                <input type="text" class="form-control" id="pessoa_contacto" name="pessoa_contacto" placeholder="Ex: Eng. Carlos Silva">
            </div>

            <div class="col-12 col-md-4">
                <label for="telefone_pessoa_contacto" class="form-label fw-semibold">Telefone / Telemóvel da pessoa de contacto</label>
                <input type="text" class="form-control" id="telefone_pessoa_contacto" name="telefone_pessoa_contacto" placeholder="Ex: +351 210 000 000">
            </div>


            <div class="col-12">
                <label for="observacoes" class="form-label fw-semibold">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Ex: Tempo de resposta de 24h. Cobertura total de peças sob garantia contratual."></textarea>
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar</button>
            <button type="submit" class="btn btn-success px-4 fw-semibold text-white">
                <i class="fa-solid fa-floppy-disk me-1"></i> Gravar Fornecedor
            </button>
        </div>
    </form>
</div>
</body>
</html>