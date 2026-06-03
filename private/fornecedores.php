<?php
session_start();

// 1. Configurações da Base de Dados
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "medtrack_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// 2. Query para ler todos os fornecedores por ordem alfabética
$sql_tabela = "SELECT * FROM fornecedores ORDER BY nome_empresa ASC";
$result_tabela = mysqli_query($conn, $sql_tabela);
?>

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
     <style> 
    @media (min-width: 992px) {
    .navbar-nav .nav-item.dropdown:hover .dropdown-menu {
        display: block;
        margin-top: 0; /* Remove o espaço que pode fazer o menu fechar sozinho */
        animation: fadeInNavbar 0.2s ease-in-out; /* Adiciona uma transição suave */
    }
}

/* Pequena animação de surgimento suave */
@keyframes fadeInNavbar {
    from {
        opacity: 0;
        transform: translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Customização visual opcional para os itens do submenu combinarem com o MedTrack */
.dropdown-menu {
    border: none;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
}

.dropdown-item {
    font-size: 0.9rem;
    padding: 8px 16px;
    color: #495057;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f1f3f5;
    color: #198754; /* Cor verde padrão do Bootstrap ou a cor do teu projeto */
    padding-left: 20px; /* Efeito de ligeiro deslize ao passar o rato */
}
    </style>
    
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
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="gestao_equip.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-microscope me-1"></i> Equipamentos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item dropdown-item" href="gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
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
                        <li><a class="dropdown-menu-item dropdown-item" href="fornecedores.php"><i class="fa-solid fa-address-book me-2"></i> Registo e Associação de Fornecedores</a></li>
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
    
    <div class="card p-4 mb-4 shadow-sm border-0 rounded-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Gestão de Fornecedores de Equipamentos</h2>
                <p class="text-muted mb-0">Registos, acompanhamentos e controlo de fornecedores.</p>
            </div>
            <!-- Botão de Atalho para Scroll -->
            <a href="listar/lista_fornecedores.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-list me-1"></i> Ir para Lista</a>
        </div>
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-success">
        <i class="fa-solid fa-truck-field fs-4 me-2"></i>
        <h5 class="fw-bold mb-0 text-dark">Registar Novo Fornecedor</h5>
    </div>
    
    <form action="inserir/inserir_fornecedor.php" method="POST">
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
                <input type="text" class="form-control" id="contacto_telefonico" name="contacto_telefonico" placeholder="Ex: +351 210 000 000">
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
                <label for="telefone_pessoa_contacto" class="form-label fw-semibold">Telefone / Telemóvel pessoal de contacto</label>
                <input type="text" class="form-control" id="telefone_pessoa_contacto" name="telefone_pessoa_contacto" placeholder="Ex: +351 910 000 000">
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



<div class="card p-4 mb-4 shadow-sm border-0 rounded-3">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-primary">
        <i class="fa-solid fa-link fs-4 me-2"></i>
        <h5 class="fw-bold mb-0 text-dark">Associar Fornecedor/Função a Equipamento</h5>
    </div>
    
    <form action="associar_fornecedor_equipamentos.php" method="POST">
        <div class="row g-3">
            
            <div class="col-12 col-md-4">
                <label for="equipamento_id" class="form-label fw-semibold">Equipamento Médico</label>
                <select class="form-select" id="equipamento_id" name="equipamento_id" required>
                    <option value="" selected disabled>Selecione o Equipamento...</option>
                    <?php
                    $conn = mysqli_connect("localhost", "root", "", "medtrack_db");
                    if ($conn) {
                        $res_eq = mysqli_query($conn, "SELECT id, designacao, numero_serie FROM equipamentos ORDER BY designacao ASC");
                        while ($eq = mysqli_fetch_assoc($res_eq)) {
                            echo "<option value='{$eq['id']}'>" . htmlspecialchars($eq['designacao']) . " (S/N: " . htmlspecialchars($eq['numero_serie']) . ")</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label for="fornecedor_id" class="form-label fw-semibold">Fornecedor / Entidade</label>
                <select class="form-select" id="fornecedor_id" name="fornecedor_id" required>
                    <option value="" selected disabled>Selecione o Fornecedor...</option>
                    <?php
                    if ($conn) {
                        // Nota: Ajuste 'proveedores' para 'fornecedores' se alterou o nome da tabela no passo anterior
                        $res_forn = mysqli_query($conn, "SELECT id, nome_empresa FROM fornecedores ORDER BY nome_empresa ASC");
                        while ($forn = mysqli_fetch_assoc($res_forn)) {
                            echo "<option value='{$forn['id']}'>" . htmlspecialchars($forn['nome_empresa']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label for="tipo_fornecedor" class="form-label fw-semibold">Classificação </label>
                <select class="form-select" id="tipo_fornecedor" name="tipo_fornecedor" required>
                    <option value="" selected disabled>Selecione o Tipo/Função...</option>
                    <option value="Fabricante">Fabricante</option>
                    <option value="Distribuidor ou fornecedor comercial">Distribuidor ou fornecedor comercial</option>
                    <option value="Empresa de assistência técnica">Empresa de assistência técnica</option>
                    <option value="Fornecedor de consumíveis ou acessórios">Fornecedor de consumíveis ou acessórios</option>
                </select>
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar</button>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="fa-solid fa-plus me-1"></i> Criar Associação
            </button>
        </div>
    </form>
</div>
<?php if(isset($conn)) mysqli_close($conn); ?>
</body>
</html>