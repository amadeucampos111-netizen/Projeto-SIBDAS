<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.php?erro=restrito");
    exit;
}

$port = 10464;
$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", $port);

if (!$conn) {
    die("Falha na ligação à base de dados: " . mysqli_connect_error());
}

// Filtro adaptado para recolher exclusivamente os dados cujo estado é 'Inativo'
$sql = "SELECT gc.*, e.designacao AS eq_nome, e.numero_serie, f.nome_empresa AS forn_nome 
        FROM garantias_contratos gc
        INNER JOIN equipamentos e ON gc.equipamento_id = e.id
        LEFT JOIN fornecedores f ON gc.entidade_responsavel_id = f.id
        WHERE gc.estado = 'Inativo'
        ORDER BY e.designacao ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Arquivo de Contratos</title>
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
                    <a class="nav-link dropdown-toggle" href="../gestao_equip.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-microscope me-1"></i> Equipamentos</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="../gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos</a></li>
                        <li><a class="dropdown-item" href="listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><a class="dropdown-item" href="lista_componentes.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../garantia_contratos.php"><i class="fa-solid fa-file-shield me-2"></i> Registo de Garantias</a></li>
                        <li><a class="dropdown-item" href="lista_garantias.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias</a></li>
                        <li><a class="dropdown-item" href="lista_garantias_inativas.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias Inativas</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle " href="../localizacao.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
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

<div class="container mt-5">
    <?php if(isset($_SESSION['msg_sucesso'])): ?> 
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div> 
    <?php endif; ?>

    <div class="mb-3">
        <a href="lista_garantias.php" class="btn btn-sm btn-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Voltar aos Contratos Ativos
        </a>
    </div>

    <div class="card p-4 shadow-sm border-warning border-1">
        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-2">
            <h5 class="fw-bold mb-0 text-dark">
                <i class="fa-solid fa-box-archive text-warning me-2"></i>Arquivo Histórico de Garantias Inativas
            </h5>
            <span class="badge bg-warning text-dark fw-bold">Arquivo</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Equipamento</th>
                        <th>Garantia Base</th>
                        <th>Contrato Manutenção</th>
                        <th>Entidade Responsável</th>
                        <th class="text-center" style="width: 100px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if(mysqli_num_rows($result) > 0):
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                        <tr class="text-muted">
                            <td>
                                <div class="fw-bold text-secondary"><?php echo htmlspecialchars($row['eq_nome']); ?></div>
                                <small class="text-muted">S/N: <?php echo htmlspecialchars($row['numero_serie']); ?></small>
                            </td>
                            <td>
                                <?php if(!empty($row['data_fim_garantia'])): ?>
                                    <small>Terminou em: <?php echo date('d/m/Y', strtotime($row['data_fim_garantia'])); ?></small>
                                    <span class='badge bg-secondary ms-1'>Arquivada</span>
                                <?php else: echo "<em class='text-muted'>Não definida</em>"; endif; ?>
                            </td>
                            <td>
                                <?php if($row['tem_contrato_manutencao']): ?>
                                    <span class="badge bg-light text-muted border"><?php echo htmlspecialchars($row['tipo_contrato']); ?></span>
                                    <small class="d-block text-muted">Ações: <?php echo htmlspecialchars($row['periodicidade']); ?></small>
                                <?php else: echo "<span class='text-muted'>Não possuía</span>"; endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['forn_nome'] ?? 'Gestão Interna'); ?></td>
                            <td class="text-center">
                                <a href="../eliminar/reativar_garantia.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success" title="Reativar Registo">
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </a>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center p-5 text-muted">
                                <i class="fa-solid fa-folder-open fs-2 d-block mb-2 text-secondary"></i>
                                O arquivo histórico de garantias encontra-se vazio.
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