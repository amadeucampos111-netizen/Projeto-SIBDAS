<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    
    // Por segurança, limpa qualquer resíduo de sessão que possa existir
    session_unset();
    session_destroy();
    
    // 3. Expulsar o intruso de volta para o formulário de login
    // Ajusta o caminho se o teu login.php estiver numa pasta acima (ex: ../login.php)
    header("Location: ../public/login.html?erro=restrito");
    exit; // Interrompe imediatamente a execução do resto da página
}

$host = "vsgate-s1.dei.isep.ipp.pt";
$port = 10464;
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";

 try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, //Força o PDO a disparar exceções (erros controláveis) caso uma query falhe
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC //Configura o formato de resposta da BD para matrizes associativas (onde acedes aos valores pelos nomes das colunas
    ]);
} catch (\PDOException $e) {
    die("Erro temporário no servidor.");
}

// Procura os equipamentos ativos para popular o select de vínculo
$equips = $pdo->query("SELECT id, designacao, codigo_interno FROM equipamentos ORDER BY designacao ASC")->fetchAll();

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
    
    <?php if (isset($_SESSION['mensagem_sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['mensagem_sucesso']; unset($_SESSION['mensagem_sucesso']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['mensagem_erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> <?php echo $_SESSION['mensagem_erro']; unset($_SESSION['mensagem_erro']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card p-4 shadow-sm bg-white border-0">
        <h4 class="fw-bold text-dark mb-4"><i class="fa-solid fa-file-medical text-success me-2"></i>Registar Novo Documento Técnico</h4>
        
        <form action="inserir/inserir_documento.php" method="POST" enctype="multipart/form-data">
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tipo de Documento</label>
                    <select name="tipo_documento" class="form-select" required>
                        <option value="" disabled selected>Selecione uma opção...</option>
                        <option value="Manual de utilizador">Manual de utilizador</option>
                        <option value="Manual de serviço">Manual de serviço</option>
                        <option value="Certificado de calibração">Certificado de calibração</option>
                        <option value="Contrato de manutenção">Contrato de manutenção</option>
                        <option value="Fatura ou guia de aquisição">Fatura ou guia de aquisição</option>
                        <option value="Declaração de conformidade">Declaração de conformidade</option>
                        <option value="Relatório técnico">Relatório técnico</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Designação do Documento (Nome)</label>
                    <input type="text" name="nome_documento" class="form-control" placeholder="Ex: Manual Técnico do Ventilador X" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Data do Documento</label>
                    <input type="date" name="data_documento" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Data de Validade (Opcional)</label>
                    <input type="date" name="data_validade" class="form-control">
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Equipamento Vinculado</label>
                    <select name="equipamento_id" class="form-select" required>
                        <option value="" disabled selected>Selecione o equipamento...</option>
                        <?php foreach($equips as $e): ?>
                            <option value="<?php echo $e['id']; ?>">
                                <?php echo htmlspecialchars($e['designacao']) . " (Inv: " . htmlspecialchars($e['codigo_interno']) . ")"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label class="form-label fw-semibold">Anexar Ficheiro Digital (PDF, PNG, JPG - Máx 5MB)</label>
                    <input type="file" name="documento_media" class="form-control" accept=".pdf,.png,.jpg,.jpeg" required>
                </div>

                <div class="col-md-12 text-end mt-4">
                    <a href="listar/lista_documentos.php" class="btn btn-light border px-4 me-2">Cancelar</a>
                    <button type="submit" class="btn btn-success px-4">Salvar e Fazer Upload</button>
                </div>
            </div>

        </form>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>
