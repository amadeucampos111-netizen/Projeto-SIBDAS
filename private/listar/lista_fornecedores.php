<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // 3. Expulsar o intruso de volta para o formulário de login
    // Ajusta o caminho se o teu login.php estiver numa pasta acima (ex: ../login.php)
    header("Location: ../../public/login.php?erro=restrito");
    exit; // Interrompe imediatamente a execução do resto da página
}

// 1. Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

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
    <link rel="shortcut icon" href="../../assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../../assets/css/admin1240896.css">
    
    
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
            <img src="../../assets/img/hosp_icon_branco.png" alt="Logo" width="150" height="100" class="d-inline-block align-text-top me-2">
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
                        <li><a class="dropdown-menu-item dropdown-item" href="../gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos e Componentes</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="lista_componentes.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes</a></li>
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
                        <li><a class="dropdown-menu-item dropdown-item" href="../fornecedores.php"><i class="fa-solid fa-address-book me-2"></i> Registo, Associação e Desassociação de Fornecedores</a></li>
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
                <a href="../logout.php" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container mt-5 mb-5">

    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card card-custom p-4" id="listagem">
        <div class="border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-truck-field fs-4 me-2 text-success"></i>
                <h5 class="fw-bold mb-0 text-dark">Fornecedores & Parceiros de Assistência</h5>
            </div>
            <span class="badge bg-success text-white">Contactos Ativos</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Empresa / Entidade</th>
                        <th>NIF</th>
                        <th>Contactos Gerais</th>
                        <th>Pessoa de Contacto</th>
                        <th>Observações / SLAs</th>
                        <th class="text-center" style="width: 120px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // 3. Validar se existem fornecedores registados
                    if (mysqli_num_rows($result_tabela) > 0):
                        
                        // 4. Correr o loop para listar cada fornecedor
                        while ($row = mysqli_fetch_assoc($result_tabela)): 
                    ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['nome_empresa'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if (!empty($row['website'])): ?>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-globe me-1 text-primary"></i>
                                            <a href="http://<?php echo str_replace(['http://', 'https://'], '', $row['website']); ?>" target="_blank" class="text-decoration-none">
                                                <?php echo htmlspecialchars($row['website'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <span class="badge bg-light text-secondary border fw-semibold">
                                        <?php echo htmlspecialchars($row['nif'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="small mb-1">
                                        <i class="fa-solid fa-envelope text-muted me-1"></i> 
                                        <a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></a>
                                    </div>
                                    <?php if (!empty($row['contacto_telefonico'])): ?>
                                        <div class="small text-secondary">
                                            <i class="fa-solid fa-phone text-muted me-1"></i> 
                                            <?php echo htmlspecialchars($row['contacto_telefonico'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if (!empty($row['pessoa_contacto'])): ?>
                                        <div class="fw-semibold text-dark small"><?php echo htmlspecialchars($row['pessoa_contacto'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php if (!empty($row['telefone_pessoa_contacto'])): ?>
                                            <small class="text-muted">
                                                <i class="fa-solid fa-mobile-screen text-muted me-1"></i> 
                                                <?php echo htmlspecialchars($row['telefone_pessoa_contacto'], ENT_QUOTES, 'UTF-8'); ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>Não especificado</em></span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <div class="text-muted text-obs" title="<?php echo htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo !empty($row['observacoes']) ? htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8') : 'Sem observações.'; ?>
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="../editar/editar_fornecedor.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Editar Fornecedor">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="../eliminar/eliminar_fornecedor.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" title="Apagar Fornecedor" onclick="return confirm('Tem a certeza que deseja eliminar este fornecedor? Verifique se existem equipamentos vinculados a ele para evitar inconsistências.');">
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
                                <i class="fa-solid fa-truck-ramp-box fs-2 d-block mb-2 text-secondary"></i>
                                Nenhum fornecedor ou parceiro técnico foi registado até ao momento.
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

</div>
</body>
</html>