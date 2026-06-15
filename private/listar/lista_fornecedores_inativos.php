<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.php?erro=restrito");
    exit;
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

// 2. Query alterada para listar APENAS os fornecedores Inativos
$sql_tabela = "SELECT * FROM fornecedores WHERE estado = 'Inativo' ORDER BY nome_empresa ASC";
$result_tabela = mysqli_query($conn, $sql_tabela);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arquivo de Fornecedores | MedTrack</title>
    <link rel="shortcut icon" href="../../assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/admin1240896.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
    <div class="container-fluid px-lg-4"> 
        <a class="navbar-brand d-flex align-items-center py-0" href="../dashboard.php">
            <img src="../../assets/img/hosp_icon_branco.png" alt="Logo" width="105" height="70" class="d-inline-block align-text-top me-2">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav mb-2 mb-lg-0 ms-3 small text-nowrap">
                <li class="nav-item"><a class="nav-link" href="../dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="../gestao_equip.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-microscope me-1"></i> Equipamentos</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos</a></li>
                        <li><a class="dropdown-item" href="listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><a class="dropdown-item" href="lista_componentes.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../garantia_contratos.php"><i class="fa-solid fa-file-shield me-2"></i> Registo de Garantias</a></li>
                        <li><a class="dropdown-item" href="lista_garantias.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="../localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle " href="../fornecedores.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-truck-medical me-1"></i> Fornecedores</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../fornecedores.php"><i class="fa-solid fa-address-book me-2"></i> Registo e Associação</a></li>
                        <li><a class="dropdown-item" href="lista_fornecedores.php"><i class="fa-solid fa-user-plus me-2"></i> Listagem de Fornecedores</a></li>
                        <li><a class="dropdown-item" href="lista_fornecedores_inativos.php"><i class="fa-solid fa-user-minus me-2"></i> Listagem de Fornecedores Inativos</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="../documentacao.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-file-invoice me-1"></i> Documentação</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../documentacao.php"><i class="fa-solid fa-folder-tree me-2"></i> Registo de Documentação</a></li>
                        <li><a class="dropdown-item" href="lista_documentos.php"><i class="fa-solid fa-folder-plus me-2"></i> Listagem de Documentos</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link" href="../editar_texto_frontend.php"><i class="fa-solid fa-edit me-1"></i> Editar Textos</a></li>
                <li class="nav-item"><a class="nav-link" href="../pesq_avan.php"><i class="fa-solid fa-magnifying-glass me-1"></i> Pesquisa</a></li>
            </ul>
            <div class="d-flex ms-auto ms-lg-0 pt-2 pt-lg-0">
                <a href="../logout.php" class="btn btn-outline-light btn-sm px-3 fw-semibold text-nowrap"><i class="fa-solid fa-right-from-bracket me-1"></i> Sair</a>
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

    <div class="mb-3">
        <a href="lista_fornecedores.php" class="btn btn-sm btn-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar aos Fornecedores Ativos
        </a>
    </div>

    <div class="card card-custom p-4 border-warning shadow-sm" id="listagem">
        <div class="border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-box-archive fs-4 me-2 text-warning"></i>
                <h5 class="fw-bold mb-0 text-dark">Arquivo Histórico de Fornecedores</h5>
            </div>
            <span class="badge bg-warning text-dark fw-bold">Inativos / Desativados</span>
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
                    if (mysqli_num_rows($result_tabela) > 0):
                        while ($row = mysqli_fetch_assoc($result_tabela)): 
                    ?>
                            <tr class="table-light text-muted">
                                <td>
                                    <div class="fw-bold text-secondary"><?php echo htmlspecialchars($row['nome_empresa'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if (!empty($row['website'])): ?>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-globe me-1"></i>
                                            <span class="text-decoration-none text-muted"><?php echo htmlspecialchars($row['website'], ENT_QUOTES, 'UTF-8'); ?></span>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <span class="badge bg-white text-secondary border fw-semibold">
                                        <?php echo htmlspecialchars($row['nif'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="small mb-1">
                                        <i class="fa-solid fa-envelope me-1"></i> <?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?>
                                    </div>
                                    <?php if (!empty($row['contacto_telefonico'])): ?>
                                        <div class="small">
                                            <i class="fa-solid fa-phone me-1"></i> <?php echo htmlspecialchars($row['contacto_telefonico'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if (!empty($row['pessoa_contacto'])): ?>
                                        <div class="fw-semibold small"><?php echo htmlspecialchars($row['pessoa_contacto'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>Não especificado</em></span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <div class="text-muted text-obs small" title="<?php echo htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo !empty($row['observacoes']) ? htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8') : 'Sem observações.'; ?>
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="../editar/editar_fornecedor.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary" title="Ver/Editar Dados">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="../eliminar/reativar_fornecedor.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-success" title="Reativar Fornecedor">
                                            <i class="fa-solid fa-arrows-rotate"></i>
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
                                <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-secondary"></i>
                                Não existem fornecedores inativos no histórico de arquivo.
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>