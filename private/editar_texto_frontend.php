<?php
$host = "vsgate-s1.dei.isep.ipp.pt";
$port = 10464;
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";

// Efetuar a ligação à base de dados
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

// Verificar se a ligação falhou
if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

$mensagem = "";

// 1. PROCESSAR O FORMULÁRIO QUANDO FOR SUBMETIDO (MÉTODO POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Iniciar uma transação no MySQLi
        $conn->begin_transaction();

        // Preparar a Query de Atualização usando MySQLi (com placeholders '?')
        $stmt = $conn->prepare("UPDATE textos_interface SET conteudo = ? WHERE chave = ?");

        // Percorrer todos os campos enviados pelo formulário
        foreach ($_POST['textos'] as $chave => $conteudo) {
            // "ss" indica que ambos os parâmetros são strings
            $stmt->bind_param("ss", $conteudo, $chave);
            $stmt->execute();
        }
        $stmt->close(); // Fechar o statement de update

        // Confirmar as alterações na Base de Dados
        $conn->commit();
        $mensagem = "<div class='alert alert-success'>Textos atualizados com sucesso no MedTrack!</div>";
        
    } catch (Exception $e) {
        // Reverter alterações em caso de erro
        $conn->rollback();
        $mensagem = "<div class='alert alert-danger'>Erro ao atualizar: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// 2. CARREGAR OS TEXTOS ATUAIS DA BASE DE DADOS (Para mostrar nos inputs ao abrir a página)
$textos = [];
$result = $conn->query('SELECT chave, conteudo FROM textos_interface');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $textos[$row['chave']] = $row['conteudo'];
    }
}

// 3. FUNÇÃO AUXILIAR PARA EXIBIR OS TEXTOS COM SEGURANÇA
function exibir_texto($chave, $texto_padrao) {
    global $textos;
    return isset($textos[$chave]) ? htmlspecialchars($textos[$chave], ENT_QUOTES, 'UTF-8') : $texto_padrao;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Textos no Frontend - MedTrack</title>
    <link rel="shortcut icon" href="../assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
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

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2>📝 Administrar Textos do Frontend</h2>
            </div>

            <?php echo $mensagem; ?>

            <div class="card p-4 admin-card shadow-sm">
                <form action="editar_texto_frontend.php" method="POST">

                    <h5 class="text-primary fw-bold mb-3 border-bottom pb-2">Secção Hero (Topo da Página)</h5>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Título Principal</label>
                        <input type="text" name="textos[hero_titulo]" class="form-control form-control-lg" 
                               value="<?php echo exibir_texto('hero_titulo', 'Gestão Inteligente de Equipamentos Médicos'); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Subtítulo / Descrição Curta</label>
                        <textarea name="textos[hero_subtitulo]" class="form-control" rows="3" required><?php echo exibir_texto('hero_subtitulo', 'Sistema web avançado para inventário hospitalar...'); ?></textarea>
                    </div>


                    <h5 class="text-primary fw-bold mb-3 mt-5 border-bottom pb-2">Secção "Sobre o Sistema"</h5>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Título da Secção</label>
                        <input type="text" name="textos[sobre_titulo]" class="form-control" 
                               value="<?php echo exibir_texto('sobre_titulo', 'Sobre o Sistema'); ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Texto Descritivo Longo</label>
                        <textarea name="textos[sobre_descricao]" class="form-control" rows="5" required><?php echo exibir_texto('sobre_descricao', 'O MedTrack é uma solução web moderna...'); ?></textarea>
                    </div>


                    <div class="mt-5 border-top pt-3 d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold shadow-sm">
                            💾 Guardar Alterações
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