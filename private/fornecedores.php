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

// 1. Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// 2. Query para ler todos os fornecedores por ordem alfabética
$sql_tabela = "SELECT * FROM fornecedores ORDER BY nome_empresa ASC";
$result_tabela = mysqli_query($conn, $sql_tabela);

// 3. Query para ler as associações atuais (Para alimentar a tabela de remoção de vínculos)
$sql_assoc = "SELECT ef.equipamento_id, ef.fornecedor_id, ef.tipo_fornecedor, 
                     e.designacao AS equipamento_nome, e.numero_serie,
                     f.nome_empresa AS fornecedor_nome
              FROM equipamento_fornecedor ef
              INNER JOIN equipamentos e ON ef.equipamento_id = e.id
              INNER JOIN fornecedores f ON ef.fornecedor_id = f.id
              ORDER BY e.designacao ASC, ef.tipo_fornecedor ASC";
$result_assoc = mysqli_query($conn, $sql_assoc);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores | Apoio ao Inventário Hospitalar</title>
    <link rel="shortcut icon" href="../assets/img/hosp_icon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/admin1240896.css">
     
    
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-custom-verde shadow-sm">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
            <img src="../assets/img/hosp_icon_branco.png" alt="Logo" width="150" height="100" class="d-inline-block align-text-top me-2">
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
    
    <div class="container mt-5 mb-5 card p-4 mb-4 shadow-sm border-0 rounded-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Gestão de Fornecedores de Equipamentos</h2>
                <p class="text-muted mb-0">Registos, acompanhamentos e controlo de fornecedores.</p>
            </div>
            <!-- Botão de Atalho para Scroll -->
            <a href="listar/lista_fornecedores.php" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-list me-1"></i> Ir para Lista</a>
        </div>
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-success">
        <i class="fa-solid fa-truck-field fs-4 me-2"></i>
        <h5 class="fw-bold mb-0 text-dark">Registar Novo Fornecedor</h5>
    </div>
    
    <form action="inserir/inserir_fornecedor.php" method="POST">
        <div class="row g-3">
            
            <div class="col-12 col-md-8">
                <label for="nome_empresa" class="form-label fw-semibold">Nome da Empresa / Entidade</label>
                <input type="text" class="form-control" id="nome_empresa" name="nome_empresa" placeholder="Ex: Siemens Healthineers Portugal" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="nif" class="form-label fw-semibold">NIF (Contribuinte)</label>
                <input type="text" class="form-control" id="nif" name="nif" placeholder="Ex: 500123456" required>
            </div>

            <div class="col-12 col-md-4">
                <label for="contacto_telefonico" class="form-label fw-semibold">Contacto Telefónico</label>
                <input type="text" class="form-control" id="contacto_telefonico" name="contacto_telefonico" placeholder="Ex: +351 210 000 000">
            </div>

            <div class="col-12 col-md-4">
                <label for="email" class="form-label fw-semibold">Email de Contacto</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ex: assistencia@empresa.pt" required>
            </div>

            <div class="col-12">
                <label for="morada" class="form-label fw-semibold">Morada Oficial</label>
                <input type="text" class="form-control" id="morada" name="morada" placeholder="Ex: Avenida da República, Nº 10, Lisboa">
            </div>

            <div class="col-12">
                <label for="website" class="form-label fw-semibold">Website</label>
                <input type="text" class="form-control" id="website" name="website" placeholder="Ex: www.empresa.pt">
            </div>

            <div class="col-12">
                <label for="pessoa_contacto" class="form-label fw-semibold">Pessoa de Contacto</label>
                <input type="text" class="form-control" id="pessoa_contacto" name="pessoa_contacto" placeholder="Ex: Eng. Carlos Silva">
            </div>

            <div class="col-12 col-md-4">
                <label for="telefone_pessoa_contacto" class="form-label fw-semibold">Telefone / Telemóvel pessoal de contacto</label>
                <input type="text" class="form-control" id="telefone_pessoa_contacto" name="telefone_pessoa_contacto" placeholder="Ex: +351 910 000 000">
            </div>


            <div class="col-12">
                <label for="observacoes" class="form-label fw-semibold">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Ex: Tempo de resposta de 24h. Cobertura total de peças sob garantia contratual."></textarea>
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar</button>
            <button type="submit" class="btn btn-success px-4 fw-semibold text-white">
                <i class="fa-solid fa-floppy-disk me-1"></i> Gravar Fornecedor
            </button>
        </div>
    </form>
</div>



<div class="container mt-5 mb-5 card p-4 mb-4 shadow-sm border-0 rounded-3">
    <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-primary">
        <i class="fa-solid fa-link fs-4 me-2"></i>
        <h5 class="fw-bold mb-0 text-dark">Associar Fornecedor/Função a Equipamento</h5>
    </div>
    
    <form action="associar_fornecedor_equipamentos.php" method="POST">
        <div class="row g-3">
            
            <div class="col-12 col-md-4">
                <label for="equipamento_id" class="form-label fw-semibold">Equipamento Médico</label>
                <select class="form-select" id="equipamento_id" name="equipamento_id" required>
                    <option value="" selected disabled>Selecione o Equipamento...</option>
                    <?php
                    if ($conn) {
                        $res_eq = mysqli_query($conn, "SELECT id, designacao, numero_serie FROM equipamentos ORDER BY designacao ASC");
                        while ($eq = mysqli_fetch_assoc($res_eq)) {
                            echo "<option value='{$eq['id']}'>" . htmlspecialchars($eq['designacao']) . " (S/N: " . htmlspecialchars($eq['numero_serie']) . ")</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label for="fornecedor_id" class="form-label fw-semibold">Fornecedor / Entidade</label>
                <select class="form-select" id="fornecedor_id" name="fornecedor_id" required>
                    <option value="" selected disabled>Selecione o Fornecedor...</option>
                    <?php
                    if ($conn) {
                        // Nota: Ajuste 'proveedores' para 'fornecedores' se alterou o nome da tabela no passo anterior
                        $res_forn = mysqli_query($conn, "SELECT id, nome_empresa FROM fornecedores ORDER BY nome_empresa ASC");
                        while ($forn = mysqli_fetch_assoc($res_forn)) {
                            echo "<option value='{$forn['id']}'>" . htmlspecialchars($forn['nome_empresa']) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label for="tipo_fornecedor" class="form-label fw-semibold">Classificação </label>
                <select class="form-select" id="tipo_fornecedor" name="tipo_fornecedor" required>
                    <option value="" selected disabled>Selecione o Tipo/Função...</option>
                    <option value="Fabricante">Fabricante</option>
                    <option value="Distribuidor ou fornecedor comercial">Distribuidor ou fornecedor comercial</option>
                    <option value="Empresa de assistência técnica">Empresa de assistência técnica</option>
                    <option value="Fornecedor de consumíveis ou acessórios">Fornecedor de consumíveis ou acessórios</option>
                </select>
            </div>

        </div>

        <div class="mt-4 d-flex justify-content-end gap-2">
            <button type="reset" class="btn btn-outline-secondary px-4 fw-semibold">Limpar</button>
            <button type="submit" class="btn btn-primary px-4 fw-semibold">
                <i class="fa-solid fa-plus me-1"></i> Criar Associação
            </button>
        </div>
    </form>

    <div class="card p-4 mb-4 shadow-sm border-0 rounded-3">
        <div class="border-bottom pb-2 mb-4 d-flex align-items-center text-danger">
            <i class="fa-solid fa-network-wired fs-4 me-2"></i>
            <h5 class="fw-bold mb-0 text-dark">Vínculos Ativos e Desassociação</h5>
        </div>
        
        <div class="table-responsive bg-white rounded border">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                <thead class="table-light">
                    <tr>
                        <th>Equipamento Principal</th>
                        <th>Nº de Série</th>
                        <th>Entidade / Parceiro</th>
                        <th>Classificação do Vínculo</th>
                        <th class="text-center" style="width: 120px;">Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result_assoc) > 0): ?>
                        <?php while ($assoc = mysqli_fetch_assoc($result_assoc)): 
                            $badge = "bg-secondary";
                            if ($assoc['tipo_fornecedor'] === 'Fabricante') $badge = "bg-dark";
                            if ($assoc['tipo_fornecedor'] === 'Distribuidor ou fornecedor comercial') $badge = "bg-primary";
                            if ($assoc['tipo_fornecedor'] === 'Empresa de assistência técnica') $badge = "bg-warning text-dark";
                            if ($assoc['tipo_fornecedor'] === 'Fornecedor de consumíveis ou acessórios') $badge = "bg-info text-dark";
                        ?>
                            <tr>
                                <td class="fw-semibold text-dark"><?php echo htmlspecialchars($assoc['equipamento_nome']); ?></td>
                                <td><small class="text-muted"><?php echo htmlspecialchars($assoc['numero_serie'] ?: '—'); ?></small></td>
                                <td><?php echo htmlspecialchars($assoc['fornecedor_nome']); ?></td>
                                <td><span class="badge <?php echo $badge; ?>" style="font-size: 0.75rem;"><?php echo htmlspecialchars($assoc['tipo_fornecedor']); ?></span></td>
                                <td class="text-center">
                                    <a href="eliminar/eliminar_associacao_fornecedor.php?equipamento_id=<?php echo $assoc['equipamento_id']; ?>&fornecedor_id=<?php echo $assoc['fornecedor_id']; ?>&tipo=<?php echo urlencode($assoc['tipo_fornecedor']); ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Tem a certeza de que deseja quebrar a associação desta entidade com este equipamento?');">
                                        <i class="fa-solid fa-link-slash"></i> Remover
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fa-solid fa-link-slash me-1"></i> Não existem associações ativas configuradas no sistema.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php if(isset($conn)) mysqli_close($conn); ?>
</body>
</html>