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

// Query para buscar todas as localizações disponíveis
$query_loc = "SELECT id, edificio, piso, servico_departamento, sala_gabinete 
              FROM localizaciones 
              ORDER BY edificio ASC, piso ASC, servico_departamento ASC";
$result_loc = mysqli_query($conn, $query_loc);

// Query para alimentar os Selects de Equipamento (usado no formulário de componentes)
$query_equip_select = "SELECT id, designacao, numero_serie, codigo_interno FROM equipamentos ORDER BY designacao ASC";
$result_equip_select = mysqli_query($conn, $query_equip_select);


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
    <title>Gestão de Equipamentos | Apoio ao Inventário Hospitalar</title>
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
                <a href="logout.php" class="btn btn-outline-light btn-sm px-3 fw-semibold">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Sair
                </a>
            </div>
        </div>
    </div>
</nav>
    <div class="container mt-4 mb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Gestão de Equipamentos Médicos</h2>
                <p class="text-muted mb-0">Registos, acompanhamentos e controlo do ciclo de vida dos dispositivos clínicos.</p>
            </div>
            <!-- Botão de Atalho para Scroll -->
            <a href="listar/listar_equipamentos.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-list me-1"></i> Ir para Lista</a>
        </div>

        <!-- FORMULÁRIO DE REGISTO (Módulo de Equipamentos) -->
        <div class="card card-custom p-4 mb-4">
            <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-custom-verde">
                <i class="fa-solid fa-circle-plus fs-4 me-2"></i>
                <h5 class="fw-bold mb-0">Inserir Novo Equipamento no Inventário</h5>
            </div>
            
            <form action="inserir/inserir_equipamento.php" method="POST">
                <div class="row g-3">
                    
                    <!-- Código Interno Único -->
                    <div class="col-12 col-md-3">
                        <label for="codigo_interno" class="form-label">Código de Inventário (Único)</label>
                        <input type="text" class="form-control" id="codigo_interno" name="codigo_interno" placeholder="Ex: EQ-VENT-001" required>
                    </div>

                    <!-- Designação / Nome -->
                    <div class="col-12 col-md-5">
                        <label for="designacao" class="form-label">Designação do Equipamento</label>
                        <input type="text" class="form-control" id="designacao" name="designacao" placeholder="Ex: Ventilador Pulmonar Neonatal" required>
                    </div>

                    <!-- Categoria Controlada (Enum) -->
                    <div class="col-12 col-md-4">
                        <label for="categoria" class="form-label">Categoria Prática</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <option value="" selected disabled>Escolha uma opção...</option>
                            <option value="Monitorização">Monitorização</option>
                            <option value="Suporte de vida">Suporte de vida</option>
                            <option value="Terapia">Terapia</option>
                            <option value="Diagnóstico">Diagnóstico</option>
                            <option value="Laboratório">Laboratório</option>
                            <option value="Esterilização">Esterilização</option>
                            <option value="Reabilitação">Reabilitação</option>
                        </select>
                    </div>

                    <!-- Marca -->
                    <div class="col-12 col-md-3">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" placeholder="Ex: Puritan Bennett" required>
                    </div>

                    <!-- Modelo -->
                    <div class="col-12 col-md-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" placeholder="Ex: PB980" required>
                    </div>

                    <!-- Número de Série -->
                    <div class="col-12 col-md-3">
                        <label for="numero_serie" class="form-label">Número de Série (S/N)</label>
                        <input type="text" class="form-control" id="numero_serie" name="numero_serie" placeholder="Ex: SN-9948-XYZ" required>
                    </div>

                    <!-- Fabricante -->
                    <div class="col-12 col-md-3">
                        <label for="fabricante" class="form-label">Fabricante Oficial</label>
                        <input type="text" class="form-control" id="fabricante" name="fabricante" placeholder="Ex: Medtronic" required>
                    </div>

                    <!-- Data de Aquisição -->
                    <div class="col-12 col-md-3">
                        <label for="data_aquisicao" class="form-label">Data de Aquisição</label>
                        <input type="date" class="form-control" id="data_aquisicao" name="data_aquisicao" required>
                    </div>

                    <!-- Ano de Fabrico -->
                    <div class="col-12 col-md-2">
                        <label for="ano_fabrico" class="form-label">Ano de Fabrico</label>
                        <input type="number" class="form-control" id="ano_fabrico" name="ano_fabrico" min="1990" max="2026" placeholder="2025" required>
                    </div>

                    <!-- Custo de Aquisição -->
                    <div class="col-12 col-md-3">
                        <label for="custo_aquisicao" class="form-label">Custo de Aquisição (€)</label>
                        <input type="number" step="0.01" class="form-control" id="custo_aquisicao" name="custo_aquisicao" placeholder="0.00" required>
                    </div>

                    <!-- Tipo de Entrada (Enum) -->
                    <div class="col-12 col-md-4">
                        <label for="tipo_entrada" class="form-label">Tipo de Entrada</label>
                        <select class="form-select" id="tipo_entrada" name="tipo_entrada" required>
                            <option value="" selected disabled>Escolha uma opção...</option>
                            <option value="Compra">Compra</option>
                            <option value="Doação">Doação</option>
                            <option value="Aluguer">Aluguer</option>
                            <option value="Empréstimo">Empréstimo</option>
                        </select>
                    </div>

                    <!-- Localização Associada (Chave Estrangeira Dinâmica) -->
                    <div class="col-12 col-md-4">
    <label for="localizacao_id" class="form-label">Localização Hospitalar</label>
    <select class="form-select" id="localizacao_id" name="localizacao_id" required>
        <option value="" selected disabled>Selecione a Sala/Serviço...</option>
        
        <?php

        if ($conn) {
            // 2. Query para buscar todas as localizações disponíveis
            $query_loc = "SELECT id, edificio, piso, servico_departamento, sala_gabinete 
                          FROM localizaciones 
                          ORDER BY edificio ASC, piso ASC, servico_departamento ASC";
            
            $result_loc = mysqli_query($conn, $query_loc);

            // 3. Correr o loop para criar as opções dinamicamente
            if ($result_loc && mysqli_num_rows($result_loc) > 0) {
                while ($loc = mysqli_fetch_assoc($result_loc)) {
                    // Monta o texto de exibição juntando as informações da localização
                    $texto_exibicao = htmlspecialchars(
                        $loc['edificio'] . " - " . $loc['piso'] . " - " . 
                        $loc['servico_departamento'] . " (" . $loc['sala_gabinete'] . ")",
                        ENT_QUOTES, 'UTF-8'
                    );
                    
                    // O valor enviado para o banco de dados será sempre o 'id' numérico
                    echo "<option value='" . $loc['id'] . "'>" . $texto_exibicao . "</option>";
                }
            }
            
            // Fechar a ligação local
            mysqli_close($conn);
        }
        ?>
    </select>
</div>

                    <!-- Estado Atual (Enum Controlada) -->
                    <div class="col-12 col-md-4">
                        <label for="estado_atual" class="form-label">Estado Técnico Atual</label>
                        <select class="form-select" id="estado_atual" name="estado_atual" required>
                            <option value="" selected disabled>Escolha...</option>
                            <option value="Ativo">Ativo (Operacional)</option>
                            <option value="Em manutenção">Em manutenção</option>
                            <option value="Inativo">Inativo</option>
                            <option value="Em calibração">Em calibração</option>
                            <option value="Em quarentena">Em quarentena</option>
                            <option value="Abatido">Abatido (Fora de Serviço)</option>
                        </select>
                    </div>

                    <!-- Criticidade (Enum Controlada) -->
                    <div class="col-12 col-md-4">
                        <label for="criticidade" class="form-label">Grau de Criticidade</label>
                        <select class="form-select" id="criticidade" name="criticidade" required>
                            <option value="" selected disabled>Escolha...</option>
                            <option value="Baixa">Baixa</option>
                            <option value="Média">Média</option>
                            <option value="Alta">Alta</option>
                            <option value="Suporte de vida">Suporte de vida</option>
                        </select>
                    </div>

                    <!-- Observações Gerais -->
                    <div class="col-12">
                        <label for="observacoes" class="form-label">Observações Técnicas / Notas Adicionais</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2" placeholder="Detalhes de software, acessórios incluídos ou restrições especiais..."></textarea>
                    </div>

                </div>

                <!-- Botões de Ação -->
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar Campos</button>
                    <button type="submit" class="btn btn-outline-secondary px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Gravar Equipamento
                    </button>
                </div>
            </form>
        </div>
        <div class="card p-4 mb-4 shadow-sm border-0 rounded-3 bg-white">
            <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-primary">
                <i class="fa-solid fa-puzzle-piece fs-4 me-2"></i>
                <h5 class="fw-bold mb-0 text-dark">Adicionar Componente</h5>
            </div>
            
            <form action="inserir/inserir_componente.php" method="POST">
                <div class="row g-3">
                    
                    <div class="col-12 col-md-5">
                        <label for="equipamento_pai_id" class="form-label fw-semibold">Equipamento Médico Principal (Vínculo)</label>
                        <select class="form-select" id="equipamento_pai_id" name="equipamento_pai_id" required>
                            <option value="" selected disabled>Selecione o dispositivo detentor...</option>
                            <?php
                            if ($result_equip_select && mysqli_num_rows($result_equip_select) > 0) {
                                mysqli_data_seek($result_equip_select, 0); // Reposiciona o ponteiro do array
                                while ($eq = mysqli_fetch_assoc($result_equip_select)) {
                                    echo "<option value='{$eq['id']}'>" . htmlspecialchars($eq['designacao']) . " (S/N: " . htmlspecialchars($eq['numero_serie']) . " | Inv: " . htmlspecialchars($eq['codigo_interno']) . ")</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="designacao_componente" class="form-label fw-semibold">Nome do Componente</label>
                        <input type="text" class="form-control" id="designacao_componente" name="designacao_componente" placeholder="Ex: Sensor de Fluxo O2, Bateria Interna, Elétrodo" required>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="codigo_componente" class="form-label fw-semibold">Código do Componente</label>
                        <input type="text" class="form-control" id="codigo_componente" name="codigo_componente" placeholder="Ex: ACC-MON-01" required>
                    </div>

                    <div class="col-12 col-md-3">
                        <label for="numero_serie_componente" class="form-label fw-semibold">Nº de Série do componente</label>
                        <input type="text" class="form-control" id="numero_serie_componente" name="numero_serie_componente" placeholder="Ex: SN-123456" required>
                    </div>

                    <div class="col-12 col-md-9">
                        <label for="observacoes" class="form-label fw-semibold">Observações</label>
                        <input type="text" class="form-control" id="observacoes" name="observacoes" placeholder="Ex: Validade até Dezembro de 2026. Revisado na última preventiva.">
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar</button>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="fa-solid fa-plus me-1"></i> Adicionar Componente
                    </button>
                </div>
            </form>
        </div>


    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>