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

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: listar/listar_equipamentos.php");
    exit;
}

$id = intval($_GET['id']);

// Ligação à Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt"; $user = "1240896"; $pass = "campos_896"; $dbname = "db1240896"; $port = 10464;
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

// Query com INNER JOIN para trazer a localização real por extenso
$sql = "SELECT e.*, l.servico_departamento, l.sala_gabinete, l.edificio 
        FROM equipamentos e
        INNER JOIN localizaciones l ON e.localizacao_id = l.id 
        WHERE e.id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header("Location: listar/listar_equipamentos.php");
    exit;
}

$eq = mysqli_fetch_assoc($result);


$sql_doc = "SELECT id, tipo_documento, nome_documento, nome_ficheiro_caminho, data_documento, data_validade 
            FROM documentacao 
            WHERE equipamento_id = ? 
            ORDER BY data_documento DESC";

$stmt_doc = mysqli_prepare($conn, $sql_doc);
mysqli_stmt_bind_param($stmt_doc, "i", $id);
mysqli_stmt_execute($stmt_doc);
$result_doc = mysqli_stmt_get_result($stmt_doc);

$sql_forn = "SELECT f.nome_empresa, f.nif, f.contacto_telefonico, f.email, f.morada, f.website, f.pessoa_contacto, f.telefone_pessoa_contacto,f.observacoes, ef.tipo_fornecedor 
             FROM equipamento_fornecedor ef
             INNER JOIN fornecedores f ON ef.fornecedor_id = f.id
             WHERE ef.equipamento_id = ?
             ORDER BY ef.tipo_fornecedor ASC";

$stmt_forn = mysqli_prepare($conn, $sql_forn);
mysqli_stmt_bind_param($stmt_forn, "i", $id);
mysqli_stmt_execute($stmt_forn);
$result_forn = mysqli_stmt_get_result($stmt_forn);

$sql_garantia = "SELECT gc.*, f.nome_empresa AS entidade_responsavel_nome 
                 FROM garantias_contratos gc
                 LEFT JOIN fornecedores f ON gc.entidade_responsavel_id = f.id
                 WHERE gc.equipamento_id = ?";

$stmt_gar = mysqli_prepare($conn, $sql_garantia);
mysqli_stmt_bind_param($stmt_gar, "i", $id);
mysqli_stmt_execute($stmt_gar);
$result_gar = mysqli_stmt_get_result($stmt_gar);
$garantia = mysqli_fetch_assoc($result_gar); // Traz apenas 1 registo (devido ao UNIQUE no equipamento_id)
mysqli_stmt_close($stmt_gar);

$sql_comp = "SELECT id, codigo_componente, designacao_componente, numero_serie_componente, observacoes 
             FROM componentes_associados 
             WHERE equipamento_pai_id = ? 
             ORDER BY codigo_componente ASC";

$stmt_comp = mysqli_prepare($conn, $sql_comp);
mysqli_stmt_bind_param($stmt_comp, "i", $id);
mysqli_stmt_execute($stmt_comp);
$result_comp = mysqli_stmt_get_result($stmt_comp);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Ficha Técnica - <?php echo htmlspecialchars($eq['codigo_interno']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Titillium Web', sans-serif; background-color: #f8f9fa; }
        .label-detalhe { font-weight: 700; color: #495057; font-size: 0.85rem; text-transform: uppercase; }
        .valor-detalhe { background-color: #ffffff; padding: 10px; border-radius: 6px; border-left: 4px solid #289224; box-shadow:  0 2px 5px rgba(0,0,0,0.02); }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <!-- Cabeçalho da Ficha -->
                <div class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-success mb-1"><?php echo htmlspecialchars($eq['categoria']); ?></span>
                        <h3 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-file-medical text-success me-2"></i>
                            <?php echo htmlspecialchars($eq['designacao']); ?>
                        </h3>
                        <small class="text-muted">Código de Inventário: <strong><?php echo htmlspecialchars($eq['codigo_interno']); ?></strong></small>
                    </div>
                    <a href="listar/listar_equipamentos.php" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Voltar à Lista</a>
                </div>

                <!-- Grelha de Informações -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="label-detalhe">Marca</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['marca']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Modelo</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['modelo']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Nº de Série (S/N)</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['numero_serie']); ?></div>
                    </div>

                    <div class="col-md-4">
                        <div class="label-detalhe">Fabricante Oficial</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['fabricante']); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Ano de Fabrico</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['ano_fabrico']); ?></div>
                    </div>
                    <div class="col-md-3">
                        <div class="label-detalhe">Tipo de Entrada</div>
                        <div class="valor-detalhe"><?php echo htmlspecialchars($eq['tipo_entrada']); ?></div>
                    </div>

                    <div class="col-md-4">
                        <div class="label-detalhe">Data de Aquisição</div>
                        <div class="valor-detalhe"><?php echo date('d/m/Y', strtotime($eq['data_aquisicao'])); ?></div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Custo de Aquisição</div>
                        <div class="valor-detalhe"><?php echo number_format($eq['custo_aquisicao'], 2, ',', ' '); ?> €</div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Grau de Criticidade</div>
                        <div class="valor-detalhe"><i class="fa-solid fa-triangle-exclamation text-danger me-1"></i> <?php echo htmlspecialchars($eq['criticidade']); ?></div>
                    </div>

                    <div class="col-md-8">
                        <div class="label-detalhe">Localização Hospitalar Alocada</div>
                        <div class="valor-detalhe">
                            <i class="fa-solid fa-hospital text-muted me-1"></i> 
                            <?php echo htmlspecialchars($eq['edificio'] . " — " . $eq['servico_departamento'] . " — " . $eq['sala_gabinete']); ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="label-detalhe">Estado Funcional Atual</div>
                        <div class="valor-detalhe fw-bold text-success"><?php echo htmlspecialchars($eq['estado_atual']); ?></div>
                    </div>


                    <div class="col-12 mt-4">
                        <div class="label-detalhe">Observações Técnicas e Histórico</div>
                        <div class="p-3 bg-light rounded border" style="min-height: 80px;">
                            <?php echo nl2br(htmlspecialchars($eq['observacoes'] ?: "Nenhuma observação técnica registada para este dispositivo.")); ?>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="border-top pt-3">
                            <div class="label-detalhe mb-2"><i class="fa-solid fa-truck-medical text-info me-1"></i> Entidades Comerciais & Fornecedores Associados</div>
                            
                            <div class="row g-2">
                                <?php if (mysqli_num_rows($result_forn) > 0): ?>
                                    <?php while ($forn = mysqli_fetch_assoc($result_forn)): 
                                        // Definir cores para os badges com base no ENUM
                                        $badge_color = "bg-secondary";
                                        if ($forn['tipo_fornecedor'] === 'Fabricante') $badge_color = "bg-dark";
                                        if ($forn['tipo_fornecedor'] === 'Distribuidor ou fornecedor comercial') $badge_color = "bg-primary";
                                        if ($forn['tipo_fornecedor'] === 'Empresa de assistência técnica') $badge_color = "bg-warning text-dark";
                                        if ($forn['tipo_fornecedor'] === 'Fornecedor de consumíveis ou acessórios') $badge_color = "bg-info text-dark";
                                    ?>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-white rounded border border-start border-3 border-info shadow-sm h-100">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($forn['nome_empresa']); ?></h6>
                                                    <span class="badge <?php echo $badge_color; ?> badge-tipo"><?php echo htmlspecialchars($forn['tipo_fornecedor']); ?></span>
                                                </div>
                                                <div class="small text-muted">
                                                    <?php if(!empty($forn['contacto_telefonico'])): ?>
                                                        <div><i class="fa-solid fa-phone me-1"></i> <?php echo htmlspecialchars($forn['contacto_telefonico']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($forn['nif'])): ?>
                                                        <div><i class="fa-solid fa-id-card me-1"></i> <?php echo htmlspecialchars($forn['nif']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($forn['email'])): ?>
                                                        <div><i class="fa-solid fa-envelope me-1"></i> <?php echo htmlspecialchars($forn['email']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($forn['morada'])): ?>
                                                        <div><i class="fa-solid fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($forn['morada']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($forn['website'])): ?>
                                                        <div><i class="fa-solid fa-globe me-1"></i> <?php echo htmlspecialchars($forn['website']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($forn['pessoa_contacto'])): ?>
                                                        <div><i class="fa-solid fa-user me-1"></i> <?php echo htmlspecialchars($forn['pessoa_contacto']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($forn['telefone_pessoa_contacto'])): ?>
                                                        <div><i class="fa-solid fa-phone me-1"></i> <?php echo htmlspecialchars($forn['telefone_pessoa_contacto']); ?></div>
                                                    <?php endif; ?>
                                                    <?php if(!empty($forn['observacoes'])): ?>
                                                        <div><i class="fa-solid fa-sticky-note me-1"></i> <?php echo htmlspecialchars($forn['observacoes']); ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="p-3 bg-light rounded text-center text-muted border border-dashed">
                                            <i class="fa-solid fa-handshake-slash me-1"></i> Nenhuma entidade comercial ou de assistência técnica foi vinculada a este equipamento.
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="border-top pt-3">
                            <div class="label-detalhe mb-2"><i class="fa-solid fa-file-shield text-primary me-1"></i> Estado da Garantia e Contrato de Manutenção</div>
                            
                            <?php if ($garantia): 
                                $hoje = date('Y-m-d');
                                $tem_garantia = !empty($garantia['data_fim_garantia']);
                                $expirada = ($tem_garantia && $garantia['data_fim_garantia'] < $hoje);
                                
                                // Define a cor do painel esquerdo com base no estado da garantia
                                $cor_borda_garantia = $expirada ? "border-danger" : "border-primary";
                            ?>
                                <div class="card-cobertura p-3 border-start border-3 <?php echo $cor_borda_garantia; ?> shadow-sm">
                                    <div class="row g-3">
                                        <div class="col-md-6 border-end">
                                            <span class="fw-bold text-secondary small d-block mb-1"><i class="fa-solid fa-shield-halved me-1"></i> Garantia do Fabricante:</span>
                                            <?php if ($tem_garantia): ?>
                                                <div class="fs-5 fw-bold text-dark">
                                                    Até <?php echo date('d/m/Y', strtotime($garantia['data_fim_garantia'])); ?>
                                                </div>
                                                <small class="text-muted d-block">Início: <?php echo date('d/m/Y', strtotime($garantia['data_inicio_garantia'])); ?></small>
                                                <?php echo $expirada ? "<span class='badge bg-danger mt-1'>Garantia Expirada</span>" : "<span class='badge bg-success mt-1'>Garantia Válida / Ativa</span>"; ?>
                                            <?php else: ?>
                                                <span class="text-muted"><em>Não especificada ou sem garantia base.</em></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-md-6 ps-md-4">
                                            <span class="fw-bold text-secondary small d-block mb-1"><i class="fa-solid fa-screwdriver-wrench me-1"></i> Contrato de Assistência Técnica:</span>
                                            <?php if ($garantia['tem_contrato_manutencao']): ?>
                                                <div class="fs-6 fw-bold text-primary mb-1">
                                                    Modalidade: <?php echo htmlspecialchars($garantia['tipo_contrato'] ?: 'Não definido'); ?>
                                                </div>
                                                <div class="small text-muted">
                                                    <strong>Periodicidade:</strong> <?php echo htmlspecialchars($garantia['periodicidade'] ?: 'Conforme pedido'); ?><br>
                                                    <strong>Responsável:</strong> <?php echo htmlspecialchars($garantia['entidade_responsavel_nome'] ?: 'Gestão Interna (Eng. Clínica)'); ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="badge bg-light text-secondary border mt-1">Sem Contrato Ativo</span>
                                                <small class="d-block text-muted mt-1">Intervenções preventivas/corretivas dependem de adjudicação isolada.</small>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (!empty($garantia['observacoes'])): ?>
                                            <div class="col-12 border-top pt-2 mt-2">
                                                <small class="fw-bold text-muted d-block">Cláusulas ou Observações do Contrato:</small>
                                                <p class="mb-0 text-secondary bg-light p-2 rounded border style-obs" style="font-size: 0.85rem;">
                                                    <?php echo nl2br(htmlspecialchars($garantia['observacoes'])); ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="p-3 bg-light rounded text-center text-muted border border-dashed">
                                    <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i> Nenhuma apólice de garantia ou contrato de manutenção programada foi registado para este dispositivo clínico.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="border-top pt-3">
                            <div class="label-detalhe mb-2"><i class="fa-solid fa-puzzle-piece text-warning me-1"></i> Componentes, Módulos e Acessórios Integrados</div>
                            
                            <div class="table-responsive bg-white rounded border">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 140px;">Cód. Componente</th>
                                            <th>Designação / Acessório</th>
                                            <th>Número de Série (S/N)</th>
                                            <th>Observações Técnicas / Notas</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result_comp) > 0): ?>
                                            <?php while ($comp = mysqli_fetch_assoc($result_comp)): ?>
                                                <tr>
                                                    <td><code class="text-dark fw-bold"><?php echo htmlspecialchars($comp['codigo_componente']); ?></code></td>
                                                    <td class="fw-semibold text-dark">
                                                        <i class="fa-solid fa-circle-dot text-warning me-1" style="font-size: 0.7rem;"></i>
                                                        <?php echo htmlspecialchars($comp['designacao_componente']); ?>
                                                    </td>
                                                    <td>
                                                        <?php echo !empty($comp['numero_serie_componente']) ? htmlspecialchars($comp['numero_serie_componente']) : '<span class="text-muted"><em>N/A</em></span>'; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?php echo !empty($comp['observacoes']) ? htmlspecialchars($comp['observacoes']) : '—'; ?>
                                                        </small>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">
                                                    <i class="fa-solid fa-cubes-solid me-1"></i> Não existem subcomponentes ou acessórios modulares mapeados para este ativo.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <div class="border-top pt-3">
                            <div class="label-detalhe mb-2"><i class="fa-solid fa-folder-open text-success me-1"></i> Documentação Técnica Associada</div>
                            
                            <div class="table-responsive bg-white rounded border">
                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.9rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tipo de Documento</th>
                                            <th>Nome Descritivo</th>
                                            <th>Caminho no PC / Rede Interna</th>
                                            <th>Data Doc.</th>
                                            <th>Validade</th>
                                            <th class="text-center" style="width: 50px;">Ação</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (mysqli_num_rows($result_doc) > 0): ?>
                                            <?php while ($doc = mysqli_fetch_assoc($result_doc)): 
                                                $classe_validade = "";
                                                if (!empty($doc['data_validade']) && $doc['data_validade'] < date('Y-m-d')) {
                                                    $classe_validade = "text-danger fw-bold";
                                                }
                                            ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-light text-dark border">
                                                            <i class="fa-solid fa-file-lines me-1 text-secondary"></i>
                                                            <?php echo htmlspecialchars($doc['tipo_documento']); ?>
                                                        </span>
                                                    </td>
                                                    <td class="fw-semibold text-dark"><?php echo htmlspecialchars($doc['nome_documento']); ?></td>
                                                    <td>
                                                        <span class="caminho-local text-muted" title="<?php echo htmlspecialchars($doc['nome_ficheiro_caminho']); ?>">
                                                            <?php echo htmlspecialchars($doc['nome_ficheiro_caminho']); ?>
                                                        </span>
                                                    </td>
                                                    <td><small><?php echo date('d/m/Y', strtotime($doc['data_documento'])); ?></small></td>
                                                    <td class="<?php echo $classe_validade; ?>">
                                                        <small>
                                                            <?php 
                                                            if (!empty($doc['data_validade'])) {
                                                                echo date('d/m/Y', strtotime($doc['data_validade']));
                                                                if ($doc['data_validade'] < date('Y-m-d')) {
                                                                    echo " <span class='badge bg-danger text-white' style='font-size:0.55rem; padding: 2px 4px;'>Expirado</span>";
                                                                }
                                                            } else {
                                                                echo "<em>Permanente</em>";
                                                            }
                                                            ?>
                                                        </small>
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-link btn-sm text-secondary p-0" title="Copiar Caminho"
                                                                onclick="navigator.clipboard.writeText('<?php echo addslashes($doc['nome_ficheiro_caminho']); ?>'); alert('Caminho copiado para a área de transferência!');">
                                                            <i class="fa-solid fa-copy fs-6"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-3">
                                                    <i class="fa-solid fa-folder-minus me-1"></i> Não existem manuais ou contratos indexados para este equipamento.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>

            </div>
            
        </div>
    </div>
</div>

</body>
</html>