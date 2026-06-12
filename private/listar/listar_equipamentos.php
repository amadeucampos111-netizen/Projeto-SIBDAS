<?php

session_start();

$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

// Criar a ligação
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

// Verificar se houve erro
if (!$conn) {
    die("Erro na ligação: " . mysqli_connect_error());
} 


$query_tabela = "SELECT e.*, l.servico_departamento, l.sala_gabinete 
                 FROM equipamentos e
                 INNER JOIN localizaciones l ON e.localizacao_id = l.id
                 ORDER BY e.id DESC";

$result_tabela = mysqli_query($conn, $query_tabela);


// Coloque isto mesmo no topo do ficheiro HTML ou antes do Card do Formulário
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['mensagem_sucesso'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['mensagem_sucesso']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensagem_sucesso']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['mensagem_erro'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $_SESSION['mensagem_erro']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['mensagem_erro']); ?>
<?php endif; ?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Equipamentos | MedTrack</title>
    <link rel="shortcut icon" href="../assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/admin1240896.css">
    
    
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
<div class="card card-custom p-4" id="listagem">
    <div class="border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between text-secondary">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-table-list fs-4 me-2 text-dark"></i>
            <h5 class="fw-bold mb-0 text-dark">Equipamentos Registados no Sistema</h5>
        </div>
        <span class="badge bg-custom-verde text-white">Dados Reais em Tempo Real</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Cód. Inventário</th>
                    <th>Designação</th>
                    <th>Categoria</th>
                    <th>Localização</th>
                    <th>Criticidade</th>
                    <th>Estado</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // 4. Validar se existem registos na base de dados
                if (mysqli_num_rows($result_tabela) > 0):
                    
                    // 5. Iniciar o ciclo While para ler linha a linha da base de dados
                    while ($row = mysqli_fetch_assoc($result_tabela)): 
                        
                        // Lógica visual para mudar as cores das Badges de Criticidade conforme o valor
                        $classe_criticidade = 'bg-secondary';
                        if ($row['criticidade'] === 'Suporte de vida') $classe_criticidade = 'bg-danger';
                        elseif ($row['criticidade'] === 'Alta') $classe_criticidade = 'bg-warning text-dark';
                        elseif ($row['criticidade'] === 'Média') $classe_criticidade = 'bg-info text-dark';
                        elseif ($row['criticidade'] === 'Baixa') $classe_criticidade = 'bg-secondary';
                        
                        // Lógica visual para mudar as cores das Badges de Estado
                        $classe_estado = 'bg-success';
                        if ($row['estado_atual'] === 'Em manutenção') $classe_estado = 'bg-warning text-dark';
                        elseif ($row['estado_atual'] === 'Inativo') $classe_estado = 'bg-danger';
                        elseif ($row['estado_atual'] === 'Ativo') $classe_estado = 'bg-success';
                        elseif ($row['estado_atual'] === 'Em calibração') $classe_estado = 'bg-warning text-dark';
                        elseif ($row['estado_atual'] === 'Em quarentena') $classe_estado = 'bg-warning text-dark';
                        elseif ($row['estado_atual'] === 'Abatido') $classe_estado = 'bg-dark';
                ?>
                        <tr>
                            <!-- Código de Inventário -->
                            <td class="fw-bold"><?php echo htmlspecialchars($row['codigo_interno'], ENT_QUOTES, 'UTF-8'); ?></td>
                            
                            <!-- Designação, Marca e Modelo -->
                            <td>
                                <div class="fw-semibold"><?php echo htmlspecialchars($row['designacao'], ENT_QUOTES, 'UTF-8'); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($row['marca'] . " - " . $row['modelo'], ENT_QUOTES, 'UTF-8'); ?></small>
                            </td>
                            
                            <!-- Categoria -->
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['categoria'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            
                            <!-- Localização Dinâmica vinda do INNER JOIN -->
                            <td>
                                <small>
                                    <?php echo htmlspecialchars($row['servico_departamento'] . " (" . $row['sala_gabinete'] . ")", ENT_QUOTES, 'UTF-8'); ?>
                                </small>
                            </td>
                            
                            <!-- Criticidade Estilizada -->
                            <td><span class="badge <?php echo $classe_criticidade; ?>"><?php echo htmlspecialchars($row['criticidade'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            
                            <!-- Estado Técnico Estilizado -->
                            <td><span class="badge <?php echo $classe_estado; ?>"><?php echo htmlspecialchars($row['estado_atual'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                            
                            <!-- Botões de Ação com IDs Dinâmicos -->
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="../ver_equipamento.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-info" title="Consultar Detalhes">
                                        <i class="fa-solid fa-eye"></i>
                                     </a>
                                    <a href="../editar/editar_equipamento.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Editar Ficha">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <a href="../eliminar/eliminar_equipamento.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" title="Abater/Apagar" onclick="return confirm('Tem a certeza que deseja eliminar este equipamento?');">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                    <!-- Mensagem amigável caso a tabela da BD esteja vazia -->
                    <tr>
                        <td colspan="7" class="text-center p-4 text-muted">
                            <i class="fa-solid fa-box-open fs-3 d-block mb-2"></i>
                            Nenhum equipamento foi registado ainda na base de dados.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>