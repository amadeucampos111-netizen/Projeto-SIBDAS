<?php
session_start();

// 1. Configurações da Base de Dados
$host = "localhost";
$user = "root";
$pass = ""; 
$dbname = "medtrack_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// 2. Query para ler todos os fornecedores por ordem alfabética
$sql_tabela = "SELECT * FROM fornecedores ORDER BY nome_empresa ASC";
$result_tabela = mysqli_query($conn, $sql_tabela);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fornecedores | Apoio ao Inventário Hospitalar</title>
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
                <i class="fa-solid fa-square-heart me-2"></i> MedTrack
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-3">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-pie me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="gestao_equip.php"><i class="fa-solid fa-microscope me-1"></i> Equipamentos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="localizacao.php"><i class="fa-solid fa-hospital-user me-1"></i> Localizações</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fa-solid fa-truck-medical me-1"></i> Fornecedores</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="documentacao.php"><i class="fa-solid fa-file-invoice me-1"></i> Documentação</a>
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
    <div class="card p-4 mb-4 shadow-sm border-0 rounded-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Gestão de Fornecedores de Equipamentos</h2>
                <p class="text-muted mb-0">Registos, acompanhamentos e controlo de fornecedores.</p>
            </div>
            <!-- Botão de Atalho para Scroll -->
            <a href="#listagem" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-list me-1"></i> Ir para Lista</a>
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

<div class="container mt-5 mb-5">

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

    <div class="card card-custom p-4" id="listagem">
        <div class="border-bottom pb-2 mb-3 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-truck-field fs-4 me-2 text-success"></i>
                <h5 class="fw-bold mb-0 text-dark">Fornecedores & Parceiros de Assistência</h5>
            </div>
            <span class="badge bg-success text-white">Contactos Ativos</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Empresa / Entidade</th>
                        <th>NIF</th>
                        <th>Contactos Gerais</th>
                        <th>Pessoa de Contacto</th>
                        <th>Observações / SLAs</th>
                        <th class="text-center" style="width: 120px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // 3. Validar se existem fornecedores registados
                    if (mysqli_num_rows($result_tabela) > 0):
                        
                        // 4. Correr o loop para listar cada fornecedor
                        while ($row = mysqli_fetch_assoc($result_tabela)): 
                    ?>
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['nome_empresa'], ENT_QUOTES, 'UTF-8'); ?></div>
                                    <?php if (!empty($row['website'])): ?>
                                        <small class="text-muted">
                                            <i class="fa-solid fa-globe me-1 text-primary"></i>
                                            <a href="http://<?php echo str_replace(['http://', 'https://'], '', $row['website']); ?>" target="_blank" class="text-decoration-none">
                                                <?php echo htmlspecialchars($row['website'], ENT_QUOTES, 'UTF-8'); ?>
                                            </a>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <span class="badge bg-light text-secondary border fw-semibold">
                                        <?php echo htmlspecialchars($row['nif'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="small mb-1">
                                        <i class="fa-solid fa-envelope text-muted me-1"></i> 
                                        <a href="mailto:<?php echo $row['email']; ?>" class="text-decoration-none"><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></a>
                                    </div>
                                    <?php if (!empty($row['contacto_telefonico'])): ?>
                                        <div class="small text-secondary">
                                            <i class="fa-solid fa-phone text-muted me-1"></i> 
                                            <?php echo htmlspecialchars($row['contacto_telefonico'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <?php if (!empty($row['pessoa_contacto'])): ?>
                                        <div class="fw-semibold text-dark small"><?php echo htmlspecialchars($row['pessoa_contacto'], ENT_QUOTES, 'UTF-8'); ?></div>
                                        <?php if (!empty($row['telefone_pessoa_contacto'])): ?>
                                            <small class="text-muted">
                                                <i class="fa-solid fa-mobile-screen text-muted me-1"></i> 
                                                <?php echo htmlspecialchars($row['telefone_pessoa_contacto'], ENT_QUOTES, 'UTF-8'); ?>
                                            </small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small"><em>Não especificado</em></span>
                                    <?php endif; ?>
                                </td>
                                
                                <td>
                                    <div class="text-muted text-obs" title="<?php echo htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo !empty($row['observacoes']) ? htmlspecialchars($row['observacoes'], ENT_QUOTES, 'UTF-8') : 'Sem observações.'; ?>
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <a href="editar/editar_fornecedor.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-primary" title="Editar Fornecedor">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                        <a href="eliminar/eliminar_fornecedor.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-danger" title="Apagar Fornecedor" onclick="return confirm('Tem a certeza que deseja eliminar este fornecedor? Verifique se existem equipamentos vinculados a ele para evitar inconsistências.');">
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
                            <td colspan="6" class="text-center p-5 text-muted">
                                <i class="fa-solid fa-truck-ramp-box fs-2 d-block mb-2 text-secondary"></i>
                                Nenhum fornecedor ou parceiro técnico foi registado até ao momento.
                            </td>
                        </tr>
                    <?php 
                    endif; 
                    
                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="card p-4 mb-4 shadow-sm border-0 rounded-3">
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
                    $conn = mysqli_connect("localhost", "root", "", "medtrack_db");
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
</div>
<?php if(isset($conn)) mysqli_close($conn); ?>
</body>
</html>