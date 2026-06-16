<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // Expulsar o intruso de volta para o formulário de login
    header("Location: ../public/login.php?erro=restrito");
    exit; // Interrompe imediatamente a execução do resto da página
}

// 1. Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$port = 10464;
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// 2. Query ALTERADA: Agora filtra por estado = 'Ativo' (Soft Delete)
$sql_tabela = "SELECT * FROM localizaciones WHERE estado = 'Ativo' ORDER BY edificio ASC, piso ASC, servico_departamento ASC";
$result_tabela = mysqli_query($conn, $sql_tabela);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Localizações | Apoio ao Inventário Hospitalar</title>
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
                        <li><a class="dropdown-item" href="listar/lista_componentes_inativos.php"><i class="fa-solid fa-plus me-2"></i> Listagem de Componentes Inativos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="garantia_contratos.php"><i class="fa-solid fa-file-shield me-2"></i> Registo de Garantias e Contratos</a></li>
                        <li><a class="dropdown-item" href="listar/lista_garantias.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias e Contratos</a></li>
                        <li><a class="dropdown-item" href="listar/lista_garantias_inativas.php"><i class="fa-solid fa-file-alt me-2"></i> Lista de Garantias e Contratos Inativos</a></li>
                    </ul>
                </li>
                
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle " href="../localizacao.php" role="button" data-bs-toggle="dropdown"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="localizacao.php"><i class="fa-solid fa-map-location-dot me-2"></i> Gerir Localizações</a></li>
                        <li><a class="dropdown-item" href="listar/lista_localizacoes_inativas.php"><i class="fa-solid fa-box-archive me-2"></i> Arquivo de Localizações</a></li>
                    </ul>
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
                        <li><a class="dropdown-item" href="listar/lista_documentos_inativos.php"><i class="fa-solid fa-folder-minus me-2"></i> Listagem de Documentos Inativos</a></li>
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

<div class="container mt-5 mb-5 card p-4 mb-4 shadow-sm border-0 rounded-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Gestão de Localizações Hospitalares</h2>
            <p class="text-muted mb-0">Registos, acompanhamentos e controlo das localizações dos equipamentos.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="listar/lista_localizacoes_inativas.php" class="btn btn-outline-warning btn-sm fw-semibold"><i class="fa-solid fa-box-archive me-1"></i> Ver Arquivo Inativos</a>
            <a href="#listagem" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-list me-1"></i> Ir para Lista</a>
        </div>
    </div>

    <div class="card card-custom p-4 mb-4 shadow-sm border-0 rounded-3">
        <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-primary">
            <i class="fa-solid fa-map-location-dot fs-4 me-2"></i>
            <h5 class="fw-bold mb-0 text-dark">Inserir Nova Localização Hospitalar</h5>
        </div>
        
        <form action="inserir/inserir_localizacao.php" method="POST">
            <div class="row g-3">
                <div class="col-12 col-md-3">
                    <label for="edificio" class="form-label fw-semibold">Edifício / Bloco</label>
                    <input type="text" class="form-control" id="edificio" name="edificio" placeholder="Ex: Edifício Central" required>
                </div>

                <div class="col-12 col-md-2">
                    <label for="piso" class="form-label fw-semibold">Piso / Andar</label>
                    <input type="text" class="form-control" id="piso" name="piso" placeholder="Ex: 0, 1, -1" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="servico_departamento" class="form-label fw-semibold">Serviço ou Departamento</label>
                    <input type="text" class="form-control" id="servico_departamento" name="servico_departamento" placeholder="Ex: Urgência Geral, UCI, Bloco Operatório" required>
                </div>

                <div class="col-12 col-md-3">
                    <label for="sala_gabinete" class="form-label fw-semibold">Sala / Gabinete / Box</label>
                    <input type="text" class="form-control" id="sala_gabinete" name="sala_gabinete" placeholder="Ex: Sala Reanimação 1, Box 5" required>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar</button>
                <button type="submit" class="btn btn-primary px-4 fw-semibold">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Gravar Localização
                </button>
            </div>
        </form>
    </div>

    <div class="container mt-4 mb-2 p-0" id="listagem">

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

        <div class="card card-custom p-4" id="listagem-localizacoes">
            <div class="border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between text-secondary">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-map-location-dot fs-4 me-2 text-dark"></i>
                    <h5 class="fw-bold mb-0 text-dark">Localizações Hospitalares Ativas</h5>
                </div>
                <span class="badge bg-custom-azul text-white">Configuração do Sistema</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">ID</th>
                            <th>Edifício / Bloco</th>
                            <th>Piso / Andar</th>
                            <th>Serviço / Departamento</th>
                            <th>Sala / Gabinete / Box</th>
                            <th class="text-center" style="width: 120px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Validar se existem registos ativos na base de dados
                        if (mysqli_num_rows($result_tabela) > 0):
                            
                            // Ciclo While para ler linha a linha
                            while ($row = mysqli_fetch_assoc($result_tabela)): 
                        ?>
                                <tr>
                                    <td class="text-muted fw-semibold">#<?php echo $row['id']; ?></td>
                                    
                                    <td class="fw-bold text-dark">
                                        <i class="fa-solid fa-building text-secondary me-2"></i><?php echo htmlspecialchars($row['edificio'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1.5">
                                             <?php echo htmlspecialchars($row['piso'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    
                                    <td class="fw-semibold text-primary">
                                        <?php echo htmlspecialchars($row['servico_departamento'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    
                                    <td>
                                        <span class="text-secondary"><?php echo htmlspecialchars($row['sala_gabinete'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <a href="editar/editar_localizacao.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Editar Localização">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <a href="eliminar/eliminar_localizacao.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-warning" title="Arquivar (Soft Delete)">
                                                <i class="fa-solid fa-box-archive"></i>
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
                                    <i class="fa-solid fa-map-pin fs-2 d-block mb-2 text-secondary"></i>
                                    Nenhuma localização hospitalar ativa mapeada na base de dados.
                                </td>
                            </tr>
                        <?php 
                        endif; 
                        
                        // Fechar a ligação após carregar a tabela
                        mysqli_close($conn);
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>