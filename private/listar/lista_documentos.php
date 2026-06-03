<?php
session_start();

$host = "localhost";
    $user = "root";
    $pass = ""; 
    $dbname = "medtrack_db";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// 2. Query que junta a documentação ao respetivo equipamento
$sql = "SELECT d.*, e.designacao AS nome_equipamento, e.numero_serie 
        FROM documentacao d
        INNER JOIN equipamentos e ON d.equipamento_id = e.id
        ORDER BY e.designacao ASC, d.tipo_documento ASC";

$result = mysqli_query($conn, $sql);


?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação | Apoio ao Inventário Hospitalar</title>
    <link rel="shortcut icon" href="../assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/admin1240896.css">
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

<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
            <i class="fa-solid fa-square-heart me-2"></i> MedTrack
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                
                <li class="nav-item">
                    <a class="nav-link" href="../dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="../gestao_equip.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-microscope me-1"></i> Equipamentos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item dropdown-item" href="../gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="../garantia_contratos.php"><i class="fa-solid fa-file-shield me-2"></i> Registo de Garantias e Contratos</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="lista_garantias.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias e Contratos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="../localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle " href="../fornecedores.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-truck-medical me-1"></i> Fornecedores
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item dropdown-item" href="../fornecedores.php"><i class="fa-solid fa-address-book me-2"></i> Registo e Associação de Fornecedores</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="lista_fornecedores.php"><i class="fa-solid fa-user-plus me-2"></i> Listagem de Fornecedores</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="../documentacao.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-file-invoice me-1"></i> Documentação
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-menu-item dropdown-item" href="../documentacao.php"><i class="fa-solid fa-folder-tree me-2"></i> Registo de Documentação</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="lista_documentos.php"><i class="fa-solid fa-folder-plus me-2"></i> Listagem de Documentos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="../pesq_avan.php"><i class="fa-solid fa-magnifying-glass me-1"></i> Pesquisa</a>
                </li>
            </ul>
            
            <div class="d-flex">
                <a href="../../public/index.html" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5" id="listagem">

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card card-custom p-4">
        <div class="border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-folder-tree fs-4 me-2 text-success"></i>
                <h5 class="fw-bold mb-0 text-dark">Arquivo Digitalizado & Índice de Manuais</h5>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Equipamento Destino</th>
                        <th>Documento / Categoria</th>
                        <th>Localização no PC / Rede</th>
                        <th>Data Emissão</th>
                        <th>Validade</th>
                        <th class="text-center" style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (mysqli_num_rows($result) > 0):
                        while ($row = mysqli_fetch_assoc($result)): 
                            
                            // Determinar se o documento está caducado ou prestes a caducar (se houver validade)
                            $classe_validade = "text-dark";
                            if (!empty($row['data_validade'])) {
                                $hoje = date('Y-m-d');
                                if ($row['data_validade'] < $hoje) {
                                    $classe_validade = "text-danger fw-bold"; // Expirado
                                }
                            }
                    ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['nome_equipamento'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <small class="text-muted">S/N: <?php echo htmlspecialchars($row['numero_serie'], ENT_QUOTES, 'UTF-8'); ?></small>
                                </td>
                                
                                <td>
                                    <div class="fw-semibold text-dark mb-1"><?php echo htmlspecialchars($row['nome_documento'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <span class="badge bg-light text-dark border">
                                        <i class="fa-solid fa-file-lines me-1 text-secondary"></i>
                                        <?php echo htmlspecialchars($row['tipo_documento'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="caminho-local text-secondary" title="<?php echo htmlspecialchars($row['nome_ficheiro_caminho'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <i class="fa-solid fa-computer me-1"></i>
                                        <?php echo htmlspecialchars($row['nome_ficheiro_caminho'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <small><?php echo date('d/m/Y', strtotime($row['data_documento'])); ?></small>
                                </td>
                                
                                <td class="<?php echo $classe_validade; ?>">
                                    <small>
                                        <?php 
                                        if (!empty($row['data_validade'])) {
                                            echo date('d/m/Y', strtotime($row['data_validade']));
                                            if ($row['data_validade'] < date('Y-m-d')) {
                                                echo " <span class='badge bg-danger ms-1 text-white' style='font-size:0.65rem;'>Expirado</span>";
                                            }
                                        } else {
                                            echo "<span class='text-muted'><em>Permanente</em></span>";
                                        }
                                        ?>
                                    </small>
                                </td>
                                
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">

                                        <a href="../editar/editar_documentacao.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Editar Documentação">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>

                                        <button type="button" class="btn btn-outline-secondary" title="Copiar Caminho" 
                                                onclick="navigator.clipboard.writeText('<?php echo addslashes($row['nome_ficheiro_caminho']); ?>'); alert('Caminho copiado para a área de transferência!');">
                                            <i class="fa-solid fa-copy"></i>
                                        </button>
                                        
                                        <a href="../eliminar/eliminar_documentacao.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" title="Apagar Registo" 
                                           onclick="return confirm('Tem a certeza que deseja remover o índice deste documento? Isto não apagará o ficheiro no vosso computador, apenas remove o registo no MedTrack.');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                        <tr>
                            <td colspan="6" class="text-center p-5 text-muted">
                                <i class="fa-solid fa-folder-minus fs-2 d-block mb-2 text-secondary"></i>
                                Nenhuma documentação técnica foi indexada até ao momento.
                            </td>
                        </tr>
                    <?php 
                    endif; 
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>
