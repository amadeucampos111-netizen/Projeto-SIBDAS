<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // 3. Expulsar o intruso de volta para o formulário de login
    // Ajusta o caminho se o teu login.php estiver numa pasta acima (ex: ../login.php)
    header("Location: ../public/login.php?erro=restrito");
    exit; // Interrompe imediatamente a execução do resto da página
}

$host = "vsgate-s1.dei.isep.ipp.pt";
$port = 10464;
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";

    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// 2. Query que junta a documentação ao respetivo equipamento
$sql = "SELECT d.*, e.designacao AS nome_equipamento, e.numero_serie 
        FROM documentacao d
        INNER JOIN equipamentos e ON d.equipamento_id = e.id
        ORDER BY e.designacao ASC, d.tipo_documento ASC";

$result = mysqli_query($conn, $sql);


?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentação | Apoio ao Inventário Hospitalar</title>
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

<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Gestão de Documentação Técnica</h2>
                <p class="text-muted mb-0">Registos, acompanhamentos e controlo da documentação técnica dos equipamentos.</p>
            </div>
            <!-- Botão de Atalho para Scroll -->
            <a href="listar/lista_documentos.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-list me-1"></i> Ir para Lista</a>
        </div>
    <div class="row justify-content-center">
        <div class="container mt-5 mb-5 card p-4 mb-4 shadow-sm border-0 rounded-3">

            <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['mensagem_erro'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?></div>
            <?php endif; ?>

            <div class="card p-4 shadow-sm border-0 rounded-3">
                <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-primary">
                    <i class="fa-solid fa-file-arrow-up fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-dark">Upload de Documentação Técnica</h5>
                </div>

                <form action="inserir/inserir_documento.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        
                        <div class="col-12 col-md-6">
                            <label for="equipamento_id" class="form-label fw-semibold">Equipamento Associado</label>
                            <select class="form-select" id="equipamento_id" name="equipamento_id" required>
                                <option value="" selected disabled>Selecione o equipamento...</option>
                                <?php
                                if ($conn) {
                                    $res = mysqli_query($conn, "SELECT id, designacao, numero_serie FROM equipamentos ORDER BY designacao ASC");
                                    while ($eq = mysqli_fetch_assoc($res)) {
                                        echo "<option value='{$eq['id']}'>" . htmlspecialchars($eq['designacao']) . " (S/N: " . htmlspecialchars($eq['numero_serie']) . ")</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="tipo_documento" class="form-label fw-semibold">Tipo de Documento</label>
                            <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                <option value="" selected disabled>Selecione o tipo oficial...</option>
                                <option value="Manual de utilizador">Manual de utilizador</option>
                                <option value="Manual de serviço">Manual de serviço</option>
                                <option value="Certificado de calibração">Certificado de calibração</option>
                                <option value="Contrato de manutenção">Contrato de manutenção</option>
                                <option value="Fatura ou guia de aquisição">Fatura ou guia de aquisição</option>
                                <option value="Declaração de conformidade">Declaração de conformidade</option>
                                <option value="Relatório técnico">Relatório técnico</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="nome_documento" class="form-label fw-semibold">Nome Descritivo do Documento</label>
                            <input type="text" class="form-control" id="nome_documento" name="nome_documento" placeholder="Ex: Certificado Anual de Calibração ISQ 2026" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="data_documento" class="form-label fw-semibold">Data de Emissão / Documento</label>
                            <input type="date" class="form-control" id="data_documento" name="data_documento" required>
                        </div>

                        <div class="col-12 col-md-6">
                            <label for="data_validade" class="form-label fw-semibold">Data de Validade (Opcional)</label>
                            <input type="date" class="form-control" id="data_validade" name="data_validade">
                            <div class="form-text">Aplicável a calibrações ou contratos com fim programado.</div>
                        </div>

                        <div class="col-12">
                            <label for="nome_ficheiro_caminho" class="form-label fw-semibold">Caminho do Ficheiro</label>
                            <input type="text" class="form-control" id="nome_ficheiro_caminho" name="nome_ficheiro_caminho" required>
                        </div>

                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary px-4">Limpar</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Guardar Documento
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>
