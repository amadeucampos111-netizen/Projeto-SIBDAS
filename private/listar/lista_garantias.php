<?php
session_start();



$port = 10464;
$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", $port);

$sql = "SELECT gc.*, e.designacao AS eq_nome, e.numero_serie, f.nome_empresa AS forn_nome 
        FROM garantias_contratos gc
        INNER JOIN equipamentos e ON gc.equipamento_id = e.id
        LEFT JOIN fornecedores f ON gc.entidade_responsavel_id = f.id
        ORDER BY e.designacao ASC";
$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Lista de Contratos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="shortcut icon" href="../assets/img/hosp_icon.png" type="image/png">
    <link rel="stylesheet" href="../assets/css/admin1240896.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
   
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../dashboard.php">
            <img src="../assets/img/hosp_icon_branco.png" alt="Logo" width="150" height="100" class="d-inline-block align-text-top me-2">
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
                <a href="../../public/index.html" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <?php if(isset($_SESSION['msg_sucesso'])): ?> <div class="alert alert-success"><?php echo $_SESSION['msg_sucesso']; unset($_SESSION['msg_sucesso']); ?></div> <?php endif; ?>
    <?php if(isset($_SESSION['msg_erro'])): ?> <div class="alert alert-danger"><?php echo $_SESSION['msg_erro']; unset($_SESSION['msg_erro']); ?></div> <?php endif; ?>

    <div class="card p-4 shadow-sm border-0">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-history text-success me-2"></i>Controlo de Garantias e Contratos</h5>
            <a href="../garantia_contratos.php" class="btn btn-success text-white fw-semibold"><i class="fa-solid fa-plus me-1"></i> Novo Contrato</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Equipamento</th>
                        <th>Garantia Base</th>
                        <th>Contrato Manutenção</th>
                        <th>Entidade Responsável</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $expirado = (!empty($row['data_fim_garantia']) && $row['data_fim_garantia'] < date('Y-m-d'));
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?php echo htmlspecialchars($row['eq_nome']); ?></div>
                            <small class="text-muted">S/N: <?php echo htmlspecialchars($row['numero_serie']); ?></small>
                        </td>
                        <td>
                            <?php if(!empty($row['data_fim_garantia'])): ?>
                                <small>Até: <?php echo date('d/m/Y', strtotime($row['data_fim_garantia'])); ?></small>
                                <?php echo $expirado ? "<span class='badge bg-danger ms-1'>Expirada</span>" : "<span class='badge bg-success ms-1'>Ativa</span>"; ?>
                            <?php else: echo "<em>Não definida</em>"; endif; ?>
                        </td>
                        <td>
                            <?php if($row['tem_contrato_manutencao']): ?>
                                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['tipo_contrato']); ?></span>
                                <small class="d-block text-muted">Ações: <?php echo htmlspecialchars($row['periodicidade']); ?></small>
                            <?php else: echo "<span class='text-muted'>Não possui</span>"; endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['forn_nome'] ?? 'Gestão Interna'); ?></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="../editar/editar_garantia.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning text-dark"><i class="fa-solid fa-pen"></i></a>
                                <a href="../eliminar/eliminar_garantia.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" onclick="return confirm('Apagar este registo de cobertura técnica?');"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>