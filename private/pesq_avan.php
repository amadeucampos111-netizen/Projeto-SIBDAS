<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // 3. Expulsar o intruso de volta para o formulário de login
    // Ajusta o caminho se o teu login.php estiver numa pasta acima (ex: ../login.php)
    header("Location: ../public/login.php?erro=restrito");
    exit; // Interrompe imediatamente a execução do resto da página
}

// 1. Configuração da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$port = 10464; 
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);
if (!$conn) {
    die("Erro na ligação à base de dados: " . mysqli_connect_error());
}

// CRUCIAL: Força a ligação a usar UTF-8 correto. 
// Isto garante que os acentos enviados pelo formulário combinem com o que está na BD.
mysqli_set_charset($conn, "utf8mb4");

// 2. Recolha e Tratamento Rigoroso dos Filtros
// Criamos uma função interna para limpar e normalizar o texto inserido pelo utilizador


$codigo_interno = isset($_GET['codigo_interno']) ? mysqli_real_escape_string($conn, trim($_GET['codigo_interno'])) : '';
$designacao     = isset($_GET['designacao'])     ? mysqli_real_escape_string($conn, trim($_GET['designacao'])) : '';
$marca          = isset($_GET['marca'])          ? mysqli_real_escape_string($conn, trim($_GET['marca'])) : '';
$modelo         = isset($_GET['modelo'])         ? mysqli_real_escape_string($conn, trim($_GET['modelo'])) : '';
$numero_serie   = isset($_GET['numero_serie'])   ? mysqli_real_escape_string($conn, trim($_GET['numero_serie'])) : '';
$servico        = isset($_GET['servico'])        ? mysqli_real_escape_string($conn, trim($_GET['servico'])) : ''; 
$estado_atual   = $_GET['estado_atual'] ?? '';
$fornecedor     = isset($_GET['fornecedor'])     ? mysqli_real_escape_string($conn, trim($_GET['fornecedor'])) : ''; 
$categoria      = $_GET['categoria'] ?? '';
$criticidade    = $_GET['criticidade'] ?? '';

// Preferências de Visualização e Ordenação
$ordenar_por = $_GET['ordenar_por'] ?? 'designacao';
$direcao     = $_GET['direcao'] ?? 'ASC';
$vista       = $_GET['vista'] ?? 'resumo';

// 3. Construção Dinâmica da Query SQL
// Nota: Adicionamos "COLLATE utf8mb4_general_ci" nas buscas por texto.
// O "_ci" significa Case Insensitive E Accent Insensitive (Ignora Maiúsculas/Minúsculas E Acentos automaticamente).
$sql = "SELECT e.*, 
               l.servico_departamento, l.edificio, l.piso, l.sala_gabinete,
               GROUP_CONCAT(DISTINCT f.nome_empresa SEPARATOR ', ') AS nomes_fornecedores
        FROM equipamentos e
        INNER JOIN localizaciones l ON e.localizacao_id = l.id
        LEFT JOIN equipamento_fornecedor ef ON e.id = ef.equipamento_id
        LEFT JOIN fornecedores f ON ef.fornecedor_id = f.id
        WHERE 1=1";

$params = [];
$types = "";

if (!empty($codigo_interno)) {
    $sql .= " AND e.codigo_interno COLLATE utf8mb4_general_ci LIKE ?";
    $params[] = "%$codigo_interno%";
    $types .= "s";
}
if (!empty($designacao)) {
    $sql .= " AND e.designacao COLLATE utf8mb4_general_ci LIKE ?";
    $params[] = "%$designacao%";
    $types .= "s";
}
if (!empty($marca)) {
    $sql .= " AND e.marca COLLATE utf8mb4_general_ci LIKE ?";
    $params[] = "%$marca%";
    $types .= "s";
}
if (!empty($modelo)) {
    $sql .= " AND e.modelo COLLATE utf8mb4_general_ci LIKE ?";
    $params[] = "%$modelo%";
    $types .= "s";
}
if (!empty($numero_serie)) {
    $sql .= " AND e.numero_serie COLLATE utf8mb4_general_ci LIKE ?";
    $params[] = "%$numero_serie%";
    $types .= "s";
}
if (!empty($servico)) {
    $sql .= " AND l.servico_departamento COLLATE utf8mb4_general_ci LIKE ?";
    $params[] = "%$servico%";
    $types .= "s";
}
if (!empty($estado_atual)) {
    $sql .= " AND e.estado_atual = ?";
    $params[] = $estado_atual;
    $types .= "s";
}
if (!empty($categoria)) {
    $sql .= " AND e.categoria = ?";
    $params[] = $categoria;
    $types .= "s";
}
if (!empty($criticidade)) {
    $sql .= " AND e.criticidade = ?";
    $params[] = $criticidade;
    $types .= "s";
}

// Agrupamento obrigatório devido ao uso de GROUP_CONCAT
$sql .= " GROUP BY e.id, l.id";

if (!empty($fornecedor)) {
    $sql .= " HAVING nomes_fornecedores COLLATE utf8mb4_general_ci LIKE ?";
    $params[] = "%$fornecedor%";
    $types .= "s";
}

// Lista de segurança para ordenação
$colunas_validas = [
    'designacao' => 'e.designacao',
    'codigo_interno' => 'e.codigo_interno',
    'criticidade' => 'e.criticidade',
    'id' => 'e.id',
    'servico' => 'l.servico_departamento'
];
$coluna_ordenar = $colunas_validas[$ordenar_por] ?? 'e.designacao';
$direcao = ($direcao === 'DESC') ? 'DESC' : 'ASC';

$sql .= " ORDER BY $coluna_ordenar $direcao";

// Execução segura com Prepared Statements
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    if (!empty($types)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
} else {
    die("Erro ao processar consulta: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisa avançada | Apoio ao Inventário Hospitalar</title>
    <link rel="shortcut icon" href="../assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/admin1240896.css">
 
    
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
    <div class="container-fluid px-lg-4"> <a class="navbar-brand d-flex align-items-center py-0" href="dashboard.php">
            <img src="../assets/img/hosp_icon_branco.png" alt="Logo" width="105" height="70" class="d-inline-block align-text-top me-2">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
            <ul class="navbar-nav mb-2 mb-lg-0 ms-3 small text-nowrap">
                
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="gestao_equip.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-microscope me-1"></i> Equipamentos
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="gestao_equip.php"><i class="fa-solid fa-list me-2"></i> Registar Equipamentos e Componentes</a></li>
                        <li><a class="dropdown-item" href="listar/listar_equipamentos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Equipamentos</a></li>
                        <li><a class="dropdown-item" href="listar/lista_componentes.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="garantia_contratos.php"><i class="fa-solid fa-file-shield me-2"></i> Registo de Garantias e Contratos</a></li>
                        <li><a class="dropdown-item" href="listar/lista_garantias.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias e Contratos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="fornecedores.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-truck-medical me-1"></i> Fornecedores
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="fornecedores.php"><i class="fa-solid fa-address-book me-2"></i> Registo, Associação e Desassociação de Fornecedores</a></li>
                        <li><a class="dropdown-item" href="listar/lista_fornecedores.php"><i class="fa-solid fa-user-plus me-2"></i> Listagem de Fornecedores</a></li>
                        <li><a class="dropdown-item" href="listar/lista_fornecedores_inativos.php"><i class="fa-solid fa-user-minus me-2"></i> Listagem de Fornecedores Inativos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="documentacao.php" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-file-invoice me-1"></i> Documentação
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="documentacao.php"><i class="fa-solid fa-folder-tree me-2"></i> Registo de Documentação</a></li>
                        <li><a class="dropdown-item" href="listar/lista_documentos.php"><i class="fa-solid fa-folder-plus me-2"></i> Listagem de Documentos</a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link " href="editar_texto_frontend.php"><i class="fa-solid fa-edit me-1"></i> Editar Textos</a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="pesq_avan.php"><i class="fa-solid fa-magnifying-glass me-1"></i> Pesquisa</a>
                </li>
            </ul>
            
            <div class="d-flex ms-auto ms-lg-0 pt-2 pt-lg-0">
                <a href="logout.php" class="btn btn-outline-light btn-sm px-3 fw-semibold text-nowrap">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1"><i class="fa-solid fa-magnifying-glass me-2 text-primary"></i>Módulo de Consulta e Filtragem</h2>
            <p class="text-muted mb-0">Pesquise no inventário utilizando filtros combinados e escolha o formato de visualização.</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-arrow-left me-1"></i> Voltar</a>
    </div>

    <div class="card filter-card bg-white p-4 mb-4">
        <form method="GET" action="pesq_avan.php" id="formPesquisa">
            <input type="hidden" name="vista" value="<?php echo htmlspecialchars($vista); ?>">

            <div class="row g-3">
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Código Interno</label>
                    <input type="text" class="form-control form-control-sm" name="codigo_interno" value="<?php echo htmlspecialchars($codigo_interno); ?>">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Designação</label>
                    <input type="text" class="form-control form-control-sm" name="designacao" value="<?php echo htmlspecialchars($designacao); ?>">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Marca</label>
                    <input type="text" class="form-control form-control-sm" name="marca" value="<?php echo htmlspecialchars($marca); ?>">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Modelo</label>
                    <input type="text" class="form-control form-control-sm" name="modelo" value="<?php echo htmlspecialchars($modelo); ?>">
                </div>
                
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Número de Série</label>
                    <input type="text" class="form-control form-control-sm" name="numero_serie" value="<?php echo htmlspecialchars($numero_serie); ?>">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Serviço Hospitalar</label>
                    <input type="text" class="form-control form-control-sm" name="servico" value="<?php echo htmlspecialchars($servico); ?>" placeholder="Ex: Urgências">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Estado</label>
                    <select class="form-select form-select-sm" name="estado_atual">
                        <option value="">Todos</option>
                        <option value="Ativo" <?php echo $estado_atual === 'Ativo' ? 'selected' : ''; ?>>Ativo (Operacional)</option>
                        <option value="Inativo" <?php echo $estado_atual === 'Inativo' ? 'selected' : ''; ?>>Inativo</option>
                        <option value="Em manutenção" <?php echo $estado_atual === 'Em manutenção' ? 'selected' : ''; ?>>Em Manutenção</option>
                        <option value="Em calibração" <?php echo $estado_atual === 'Em calibração' ? 'selected' : ''; ?>>Em Calibração</option>
                        <option value="Em quarentena" <?php echo $estado_atual === 'Em quarentena' ? 'selected' : ''; ?>>Em Quarentena</option>
                        <option value="Abatido" <?php echo $estado_atual === 'Abatido' ? 'selected' : ''; ?>>Abatido</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Fornecedor</label>
                    <input type="text" class="form-control form-control-sm" name="fornecedor" value="<?php echo htmlspecialchars($fornecedor); ?>">
                </div>

                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Categoria</label>
                    <select class="form-select form-select-sm" name="categoria">
                        <option value="">Todos</option>
                        <option value="Monitorização" <?php echo $categoria === 'Monitorização' ? 'selected' : ''; ?>>Monitorização</option>
                        <option value="Suporte de vida" <?php echo $categoria === 'Suporte de vida' ? 'selected' : ''; ?>>Suporte de vida</option>
                        <option value="Terapia" <?php echo $categoria === 'Terapia' ? 'selected' : ''; ?>>Terapia</option>
                        <option value="Diagnóstico" <?php echo $categoria === 'Diagnóstico' ? 'selected' : ''; ?>>Diagnóstico</option>
                        <option value="Laboratório" <?php echo $categoria === 'Laboratório' ? 'selected' : ''; ?>>Laboratório</option>
                        <option value="Esterilização" <?php echo $categoria === 'Esterilização' ? 'selected' : ''; ?>>Esterilização</option>
                        <option value="Reabilitação" <?php echo $categoria === 'Reabilitação' ? 'selected' : ''; ?>>Reabilitação</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-semibold text-muted">Criticidade</label>
                    <select class="form-select form-select-sm" name="criticidade">
                        <option value="">Todas</option>
                         <option value="Suporte de vida" <?php echo $criticidade === 'Suporte de vida' ? 'selected' : ''; ?>>Suporte de vida</option>
                        <option value="Alta" <?php echo $criticidade === 'Alta' ? 'selected' : ''; ?>>Alta</option>
                        <option value="Média" <?php echo $criticidade === 'Média' ? 'selected' : ''; ?>>Média</option>
                        <option value="Baixa" <?php echo $criticidade === 'Baixa' ? 'selected' : ''; ?>>Baixa</option>
                    </select>
                </div>

                <div class="col-12 col-sm-6 col-md-4">
                    <label class="form-label small fw-semibold text-muted">Ordenar por</label>
                    <div class="input-group input-group-sm">
                        <select class="form-select" name="ordenar_por">
                            <option value="designacao" <?php echo $ordenar_por === 'designacao' ? 'selected' : ''; ?>>Designação</option>
                            <option value="codigo_interno" <?php echo $ordenar_por === 'codigo_interno' ? 'selected' : ''; ?>>Código Interno</option>
                            <option value="criticidade" <?php echo $ordenar_por === 'criticidade' ? 'selected' : ''; ?>>Criticidade</option>
                            <option value="id" <?php echo $ordenar_por === 'id' ? 'selected' : ''; ?>>Data de Registo</option>
                        </select>
                        <select class="form-select" name="direcao">
                            <option value="ASC" <?php echo $direcao === 'ASC' ? 'selected' : ''; ?>>Crescente (A-Z)</option>
                            <option value="DESC" <?php echo $direcao === 'DESC' ? 'selected' : ''; ?>>Decrescente (Z-A)</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 align-items-end mt-4">
                    <a href="pesq_avan.php" class="btn btn-sm btn-light border"><i class="fa-solid fa-broom me-1"></i> Limpar Filtros</a>
                    <button type="submit" class="btn btn-sm btn-primary px-4"><i class="fa-solid fa-filter me-1"></i> Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <span class="text-muted small">Foram encontrados <strong><?php echo mysqli_num_rows($result); ?></strong> equipamentos.</span>
        <div class="btn-group shadow-sm">
            <?php
            $url_base = $_SERVER['QUERY_STRING'];
            // Remove o parâmetro antigo da vista para não duplicar na URL
            parse_str($url_base, $query_array);
            
            $query_array['vista'] = 'resumo';
            $url_resumo = "pesq_avan.php?" . http_build_query($query_array);

            ?>
            <a href="<?php echo $url_resumo; ?>" class="btn btn-sm btn-outline-secondary <?php echo $vista === 'resumo' ? 'active' : ''; ?>">
                <i class="fa-solid fa-list me-1"></i> Vista Resumo
            </a>
        </div>
    </div>

    <?php if (mysqli_num_rows($result) > 0): ?>
        
        <?php if ($vista === 'resumo'): ?>
            <div class="card filter-card p-3 bg-white">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="table-light">
                            <tr>
                                <th>Cód. Interno</th>
                                <th>Designação</th>
                                <th>Marca/Modelo</th>
                                <th>Nº Série</th>
                                <th>Serviço</th>
                                <th>Criticidade</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td class="fw-bold font-monospace text-primary"><?php echo htmlspecialchars($row['codigo_interno']); ?></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['designacao']); ?></td>
                                    <td><?php echo htmlspecialchars($row['marca'] . " / " . $row['modelo']); ?></td>
                                    <td class="text-muted font-monospace"><?php echo htmlspecialchars($row['numero_serie']); ?></td>
                                    <td><?php echo htmlspecialchars($row['servico_departamento']); ?></td>
                                    <td>
                                        <?php 
                                        $classe_criticidade = 'bg-secondary';
                                        if ($row['criticidade'] === 'Suporte de vida') $classe_criticidade = 'bg-danger';
                                        elseif ($row['criticidade'] === 'Alta') $classe_criticidade = 'bg-warning text-dark';
                                        elseif ($row['criticidade'] === 'Média') $classe_criticidade = 'bg-info text-dark';
                                        ?>
                                        <span class="badge <?php echo $classe_criticidade; ?>"><?php echo htmlspecialchars($row['criticidade']); ?></span>
                                    </td>
                                    <td>
                                        <?php 
                                        $classe_criticidade = 'bg-secondary';
                                        if ($row['estado_atual'] === 'Ativo') $classe_criticidade = 'bg-success';
                                        elseif ($row['estado_atual'] === 'Abatido') $classe_criticidade = 'bg-danger';
                                        elseif ($row['estado_atual'] === 'Inativo') $classe_criticidade = 'bg-warning text-dark';
                                        elseif ($row['estado_atual'] === 'Em manutenção') $classe_criticidade = 'bg-warning text-dark';
                                        elseif ($row['estado_atual'] === 'Em quarentena') $classe_criticidade = 'bg-warning text-dark';
                                        elseif ($row['estado_atual'] === 'Em calibração') $classe_criticidade = 'bg-warning text-dark';
                                        ?>
                                        <span class="badge <?php echo $classe_criticidade; ?>">
                                            <?php echo htmlspecialchars($row['estado_atual']); ?>
                                        </span>
                                    </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="ver_equipamento2.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-info" title="Vista detalhada">
                                        <i class="fa-solid fa-eye"></i>
                                     </a>
                                    
                                </div>
                            </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php else: ?>
            <div class="row g-3">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col-12 col-md-6 col-xl-4">
                        <div class="card result-card bg-white p-4 h-100 border-start border-4 <?php echo ($row['estado_atual'] == 'Operacional') ? 'border-success' : (($row['estado_atual'] == 'Avariado') ? 'border-danger' : 'border-warning'); ?>">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="text-muted font-monospace small d-block">INV: <?php echo htmlspecialchars($row['codigo_interno']); ?></span>
                                    <h5 class="fw-bold text-dark mb-0"><?php echo htmlspecialchars($row['designacao']); ?></h5>
                                </div>
                                <span class="badge <?php echo ($row['estado_atual'] == 'Operacional') ? 'bg-success' : (($row['estado_atual'] == 'Avariado') ? 'bg-danger' : 'bg-warning text-dark'); ?>">
                                    <?php echo htmlspecialchars($row['estado_atual']); ?>
                                </span>
                            </div>
                            
                            <hr class="text-muted my-2">
                            
                            <div class="row g-2 my-1 small">
                                <div class="col-6"><strong>Marca:</strong> <?php echo htmlspecialchars($row['marca']); ?></div>
                                <div class="col-6"><strong>Modelo:</strong> <?php echo htmlspecialchars($row['modelo']); ?></div>
                                <div class="col-6"><strong>Nº Série:</strong> <span class="font-monospace"><?php echo htmlspecialchars($row['numero_serie']); ?></span></div>
                                <div class="col-6"><strong>Serviço:</strong> <?php echo htmlspecialchars($row['servico_departamento']); ?></div>
                                <div class="col-6"><strong>Categoria:</strong> <?php echo htmlspecialchars($row['categoria'] ?? 'N/A'); ?></div>
                                <div class="col-6"><strong>Fornecedor:</strong> <?php echo htmlspecialchars($row['fornecedor'] ?? 'N/A'); ?></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                                <div>
                                    <span class="small text-muted me-1">Criticidade:</span>
                                    <?php 
                                     $classe_criticidade = 'bg-secondary';
                        if ($row['criticidade'] === 'Suporte de vida') $classe_criticidade = 'bg-danger';
                        elseif ($row['criticidade'] === 'Alta') $classe_criticidade = 'bg-warning text-dark';
                        elseif ($row['criticidade'] === 'Média') $classe_criticidade = 'bg-info text-dark';
                                    ?>
                                    <span class="badge <?php echo $classe_criticidade; ?>"><?php echo htmlspecialchars($row['criticidade']); ?></span>
                                </div>
                                <button class="btn btn-xs btn-link p-0 text-decoration-none small"><i class="fa-solid fa-circle-info"></i> Ficha Completa</button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="text-center p-5 bg-white rounded shadow-sm">
            <i class="fa-solid fa-folder-open text-muted fs-1 mb-3"></i>
            <h5 class="text-dark fw-semibold">Nenhum equipamento corresponde aos filtros aplicados.</h5>
            <p class="text-muted small">Tente ajustar os critérios de pesquisa combinada.</p>
        </div>
    <?php endif; ?>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/1240896.js"></script>
</body>
</html>
<?php
mysqli_close($conn);?>