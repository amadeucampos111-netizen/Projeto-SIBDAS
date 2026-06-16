<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.html?erro=restrito");
    exit;
}

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    $_SESSION['mensagem_erro'] = "Acesso negado. Esta área está reservada exclusivamente a Administradores.";
    header("Location: ../dashboard.php");
    exit;
}

$host = "vsgate-s1.dei.isep.ipp.pt";
$port = 10464;
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Falha na ligação à base de dados: " . mysqli_connect_error());
}

// Query que recolhe apenas os componentes com estado 'Inativo'
$query = "SELECT c.*, e.designacao AS equipamento_nome, e.codigo_interno AS equipamento_codigo 
          FROM componentes_associados c
          INNER JOIN equipamentos e ON c.equipamento_pai_id = e.id
          WHERE c.estado = 'Inativo'
          ORDER BY c.id DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Arquivo de Componentes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../../assets/img/hosp_icon.png" type="image/png">
    <link rel="stylesheet" href="../../assets/css/admin1240896.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
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
                    <a class="nav-link dropdown-toggle " href="../gestao_equip.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-microscope me-1"></i> Equipamentos</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos</a></li>
                        <li><a class="dropdown-item" href="listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><a class="dropdown-item" href="lista_componentes.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes</a></li>
                        <li><a class="dropdown-item" href="lista_componentes_inativos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes Inativos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../garantia_contratos.php"><i class="fa-solid fa-file-shield me-2"></i> Registo de Garantias</a></li>
                        <li><a class="dropdown-item" href="lista_garantias.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias</a></li>
                        <li><a class="dropdown-item" href="lista_garantias_inativas.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias Inativas</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="../localizacao.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../localizacao.php"><i class="fa-solid fa-map-location-dot me-2"></i> Gerir Localizações</a></li>
                        <li><a class="dropdown-item" href="lista_localizacoes_inativas.php"><i class="fa-solid fa-box-archive me-2"></i> Arquivo de Localizações</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="../fornecedores.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-truck-medical me-1"></i> Fornecedores</a>
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
                        <li><a class="dropdown-item" href="lista_documentos_inativos.php"><i class="fa-solid fa-folder-minus me-2"></i> Listagem de Documentos Inativos</a></li>
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
    <?php if(isset($_SESSION['mensagem_erro'])): ?> 
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
    <?php endif; ?>

    <div class="mb-3">
        <a href="lista_componentes.php" class="btn btn-sm btn-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar aos Componentes Ativos
        </a>
    </div>

    <div class="card p-4 shadow-sm border-warning border-1 bg-white">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="fa-solid fa-box-archive text-warning me-2"></i>Arquivo de Componentes Inativos
                </h5>
                <p class="text-muted small mb-0">Histórico de sub-módulos e sensores desassociados temporariamente do inventário ativo.</p>
            </div>
            <span class="badge bg-warning text-dark fw-bold">Arquivo Técnico</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="8%">ID</th>
                        <th width="12%">Cód. Componente</th>
                        <th width="25%">Designação do Componente</th>
                        <th width="15%">Nº de Série</th>
                        <th width="25%">Antigo Vínculo (Equipamento)</th>
                        <th class="text-center" width="5%">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($result && mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                        <tr class="text-muted">
                            <td class="fw-semibold">#<?php echo $row['id']; ?></td>
                            <td>
                                <span class="badge bg-light text-muted border font-monospace px-2 py-1.5">
                                    <?php echo !empty($row['codigo_componente']) ? htmlspecialchars($row['codigo_componente']) : '---'; ?>
                                </span>
                            </td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($row['designacao_componente']); ?></td>
                            <td><?php echo !empty($row['numero_serie_componente']) ? htmlspecialchars($row['numero_serie_componente']) : 'N/A'; ?></td>
                            <td>
                                <div class="d-flex flex-column text-muted">
                                    <span class="small fw-bold"><i class="fa-solid fa-microscope me-1"></i><?php echo htmlspecialchars($row['equipamento_nome']); ?></span>
                                    <small class="font-monospace x-small">Inv: <?php echo htmlspecialchars($row['equipamento_codigo']); ?></small>
                                </div>
                            </td>
                            <td class="text-center">
                                <a href="../eliminar/reativar_componentes.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success" title="Reativar e Restaurar Componente">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="6" class="text-center p-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-secondary"></i>
                                Não existem sub-módulos ou componentes no arquivo histórico.
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