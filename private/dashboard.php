<?php
session_start();

$host = "vsgate-s1.dei.isep.ipp.pt";
$port   = 10464; 
$user = "1240896";
$pass = "campos_896"; 
$dbname = "db1240896";

// Criar a ligação
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

// Verificar se houve erro
if (!$conn) {
    die("Erro na ligação: " . mysqli_connect_error());
} else {
    // Apaga esta linha depois de testares, serve só para termos a certeza!
    echo "";
}

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

$query_equip = "SELECT COUNT(*) as total FROM equipamentos WHERE estado_atual = 'Em Manutenção'";
$result_equip = mysqli_query($conn, $query_equip);
$data_equip = mysqli_fetch_assoc($result_equip);
$total_equipamentos_em_manutencao = $data_equip['total'];

$query_equip = "SELECT COUNT(*) as total FROM equipamentos WHERE estado_atual = 'Inativo'";
$result_equip = mysqli_query($conn, $query_equip);
$data_equip = mysqli_fetch_assoc($result_equip);
$total_equipamentos_inativos = $data_equip['total'];

$query_localizacoes = "SELECT COUNT(*) as total FROM localizaciones";
$result_localizacoes = mysqli_query($conn, $query_localizacoes);
$data_localizacoes = mysqli_fetch_assoc($result_localizacoes);
$total_localizacoes = $data_localizacoes['total'];

// Consulta SQL para contar os equipamentos agrupados por serviço/departamento
$query_servicos = "SELECT l.servico_departamento, COUNT(e.id) as total 
                   FROM localizaciones l
                   LEFT JOIN equipamentos e ON e.localizacao_id = l.id
                   GROUP BY l.servico_departamento
                   ORDER BY total DESC";

$result_servicos = mysqli_query($conn, $query_servicos);

// Consulta SQL para contar os equipamentos cuja data de fim de garantia é anterior à data de hoje
$query_garantia_exp = "SELECT COUNT(*) as total 
                       FROM equipamentos e
                       INNER JOIN garantias_contratos g ON e.id = g.equipamento_id
                       WHERE g.data_fim_garantia < CURDATE()";

$result_garantia_exp = mysqli_query($conn, $query_garantia_exp);
$data_garantia_exp = mysqli_fetch_assoc($result_garantia_exp);

// Guarda o valor na variável para usar no Dashboard
$total_garantias_expiradas = $data_garantia_exp['total'];

// Consulta SQL para encontrar equipamentos que não têm nenhuma linha correspondente na tabela documentacao
$query_sem_doc = "SELECT COUNT(*) as total 
                  FROM equipamentos e
                  LEFT JOIN documentacao d ON e.id = d.equipamento_id
                  WHERE d.equipamento_id IS NULL";

$result_sem_doc = mysqli_query($conn, $query_sem_doc);
$data_sem_doc = mysqli_fetch_assoc($result_sem_doc);

// Guarda o valor na variável para o Dashboard
$total_sem_documentacao = $data_sem_doc['total'];

// Consulta SQL para contar apenas equipamentos de suporte de vida agrupados por serviço
$query_suporte_vida = "SELECT l.servico_departamento, COUNT(e.id) as total 
                       FROM localizaciones l
                       LEFT JOIN equipamentos e ON e.localizacao_id = l.id AND e.categoria = 'Suporte de vida'
                       GROUP BY l.servico_departamento
                       ORDER BY total DESC";

$result_suporte_vida = mysqli_query($conn, $query_suporte_vida);
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
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="assets/img/hosp_icon_branco.png" alt="Logo" width="150" height="100" class="d-inline-block align-text-top me-2">
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
                        <li><a class="dropdown-menu-item dropdown-item" href="gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos e Componentes</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><a class="dropdown-menu-item dropdown-item" href="listar/lista_componentes.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes</a></li>
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
                        <li><a class="dropdown-menu-item dropdown-item" href="fornecedores.php"><i class="fa-solid fa-address-book me-2"></i> Registo, Associação e Desassociação de Fornecedores</a></li>
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
                <div class="card card-stats border-indicador-verde h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Equipamentos em Manutenção</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_equipamentos_em_manutencao) ? $total_equipamentos_em_manutencao : '0'; ?></h3>
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
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Equipamentos inativos</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_equipamentos_inativos) ? $total_equipamentos_inativos : '0'; ?></h3>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-stethoscope"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-4 col-lg-3">
    <div class="card card-stats border-left border-warning h-100" style="border-left: 5px solid #ffc107 !important;">
        <div class="card-body d-flex align-items-center justify-content-between p-4">
            <div>
                <h6 class="text-uppercase fw-bold text-muted small mb-1">Garantias Expiradas</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_garantias_expiradas) ? $total_garantias_expiradas : '0'; ?></h3>
            </div>
            <div class="icon-box bg-warning bg-opacity-10 text-warning">
                <i class="fa-solid fa-calendar-xmark"></i>
            </div>
        </div>
    </div>
</div>
            <div class="col-12 col-md-4 col-lg-3">
    <div class="card card-stats border-left border-secondary h-100" style="border-left: 5px solid #6c757d !important;">
        <div class="card-body d-flex align-items-center justify-content-between p-4">
            <div>
                <h6 class="text-uppercase fw-bold text-muted small mb-1">Sem Documentação</h6>
                <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_sem_documentacao) ? $total_sem_documentacao : '0'; ?></h3>
            </div>
            <div class="icon-box bg-secondary bg-opacity-10 text-secondary">
                <i class="fa-solid fa-file-circle-question"></i>
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

            <div class="col-12 col-md-4 col-lg-3">
                <div class="card card-stats border-indicador-azul h-100">
                    <div class="card-body d-flex align-items-center justify-content-between p-4">
                        <div>
                            <h6 class="text-uppercase fw-bold text-muted small mb-1">Total Localizações</h6>
                            <h3 class="fw-bold mb-0 text-dark"><?php echo isset($total_localizacoes) ? $total_localizacoes : '0'; ?></h3>
                        </div>
                        <div class="icon-box bg-info bg-opacity-10 text-info">
                            <i class="fa-solid fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>

<div class="row mt-4 g-4"> 
    
    <div class="col-12 col-lg-6">
        <div class="card card-stats p-4 shadow-sm border-0 h-100"> <div class="d-flex align-items-center mb-3">
                <div class="icon-box bg-success bg-opacity-10 text-custom-verde me-3">
                    <i class="fa-solid fa-hospital"></i>
                </div>
                <h5 class="fw-bold mb-0 text-dark">Equipamentos por Serviço</h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Serviço / Departamento</th>
                            <th class="text-end">Qtd. Equipamentos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Executa o ciclo para ler todos os serviços encontrados na base de dados
                        while ($row = mysqli_fetch_assoc($result_servicos)): 
                        ?>
                            <tr>
                                <td class="fw-semibold text-secondary">
                                    <?php echo htmlspecialchars($row['servico_departamento'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="text-end fw-bold text-dark">
                                    <span class="badge bg-custom-verde px-2 py-1.5">
                                        <?php echo $row['total']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-6">
        <div class="card card-stats p-4 shadow-sm border-0 h-100"> <div class="d-flex align-items-center mb-3">
                <div class="icon-box bg-danger bg-opacity-10 text-danger me-3">
                    <i class="fa-solid fa-heart-pulse"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">Dispositivos de Suporte de Vida</h5>
                    <small class="text-muted">Distribuição de equipamentos críticos por serviço</small>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Serviço / Departamento</th>
                            <th class="text-end">Críticos Ativos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Ciclo para ler os serviços e a quantidade de aparelhos de suporte de vida
                        while ($row_sv = mysqli_fetch_assoc($result_suporte_vida)): 
                        ?>
                            <tr>
                                <td class="fw-semibold text-secondary">
                                    <?php echo htmlspecialchars($row_sv['servico_departamento'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td class="text-end fw-bold">
                                    <?php if ($row_sv['total'] > 0): ?>
                                        <span class="badge bg-danger px-2 py-1.5">
                                            <?php echo $row_sv['total']; ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-muted border px-2 py-1.5">0</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>