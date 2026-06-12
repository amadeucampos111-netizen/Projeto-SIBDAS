<?php
// 1. Iniciar a sessão para mensagens de feedback se necessário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}



// 2. Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Erro na ligação à base de dados: " . mysqli_connect_error());
}

// 3. Query para listar os componentes e trazer as informações do equipamento pai associado
$query = "SELECT c.*, e.designacao AS equipamento_nome, e.codigo_interno AS equipamento_codigo 
          FROM componentes_associados c
          INNER JOIN equipamentos e ON c.equipamento_pai_id = e.id
          ORDER BY c.id DESC";

$result = mysqli_query($conn, $query);

$query_equips = "SELECT id, designacao, codigo_interno FROM equipamentos ORDER BY designacao ASC";
$result_equips = mysqli_query($conn, $query_equips);
$equipamentos_lista = mysqli_fetch_all($result_equips, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Componentes | MedTrack</title>
    <link rel="shortcut icon" href="../assets/img/hosp_icon.png" type="image/png">
    <link href="../assets/css/admin1240896.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    
</head>

<body>
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

    <div class="container mt-5 mb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Componentes e Módulos Associados</h2>
                <p class="text-muted mb-0">Listagem de sub-módulos, sensores e peças vinculadas aos equipamentos médicos hospitalares.</p>
            </div>
            <a href="../gestao_equip.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Voltar à Gestão
            </a>
        </div>

        <div class="card card-custom p-4 bg-white">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="8%">ID</th>
                            <th width="12%">Cód. Componente</th>
                            <th width="20%">Designação do Componente</th>
                            <th width="15%">Nº de Série</th>
                            <th width="25%">Equipamento Principal (Vínculo)</th>
                            <th width="15%">Observações</th>
                            <th width="5%">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result && mysqli_num_rows($result) > 0): 
                            while ($row = mysqli_fetch_assoc($result)): 
                        ?>
                            <tr>
                                <td class="fw-bold text-secondary">#<?php echo $row['id']; ?></td>
                                <td>
                                    <span class="badge bg-secondary text-white font-monospace">
                                        <?php echo !empty($row['codigo_componente']) ? htmlspecialchars($row['codigo_componente']) : '---'; ?>
                                    </span>
                                </td>
                                <td class="fw-semibold text-dark">
                                    <?php echo htmlspecialchars($row['designacao_componente']); ?>
                                </td>
                                <td class="text-muted">
                                    <?php echo !empty($row['numero_serie_componente']) ? htmlspecialchars($row['numero_serie_componente']) : '<span class="text-muted-light small">N/A</span>'; ?>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-success">
                                            <i class="fa-solid fa-microscope me-1 small"></i><?php echo htmlspecialchars($row['equipamento_nome']); ?>
                                        </span>
                                        <small class="text-muted font-monospace small">Inv: <?php echo htmlspecialchars($row['equipamento_codigo']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted d-block text-truncate" style="max-width: 200px;" title="<?php echo htmlspecialchars($row['observacoes']); ?>">
                                        <?php echo !empty($row['observacoes']) ? htmlspecialchars($row['observacoes']) : '<em>Sem notas.</em>'; ?>
                                    </small>
                                </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="../editar/editar_componente.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Editar Ficha">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="../eliminar/eliminar_componentes.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" title="Abater/Apagar" onclick="return confirm('Tem a certeza que deseja eliminar este componente?');">
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
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="fa-solid fa-folder-open fs-3 d-block mb-2 text-secondary"></i>
                                    Nenhum componente associado foi encontrado no inventário.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
mysqli_close($conn);
?>