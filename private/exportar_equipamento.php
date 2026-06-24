<?php
session_start();

// 1. Verificação de Segurança
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../public/login.html?erro=restrito");
    exit;
}

// 2. Validação do ID
//Operação de Segurança. Garante que o parâmetro id vindo do URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ver_equipamento.php");
    exit;
}

$id = intval($_GET['id']);

// 3. Ligação à Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt"; $user = "1240896"; $pass = "campos_896"; $dbname = "db1240896"; $port = 10464;
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Falha na ligação à base de dados.");
}

// 4. Consulta dos dados principais do Equipamento
$sql = "SELECT e.*, l.servico_departamento, l.sala_gabinete, l.edificio 
        FROM equipamentos e
        INNER JOIN localizaciones l ON e.localizacao_id = l.id 
        WHERE e.id = ?";
        
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) !== 1) {
    header("Location: ver_equipamento.php");
    exit;
}

$eq = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

// 5. Configuração dos Headers HTTP para forçar o Download do CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Ficha_Tecnica_' . $eq['codigo_interno'] . '.csv"');

// Abrir o buffer de saída diretamente
$output = fopen('php://output', 'w'); //É um fluxo de escrita direta. Tudo o que for gravado aqui é enviado em tempo real para o download do utilizador

// Envia o BOM (Byte Order Mark) UTF-8 para o Excel não corromper acentos e símbolos (€, º, ª)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// --- SECÇÃO 1: DADOS GERAIS ---
fputcsv($output, ['--- FICHA TÉCNICA DO EQUIPAMENTO ---'], ';');
fputcsv($output, ['Campo', 'Valor'], ';');
fputcsv($output, ['Código de Inventário', $eq['codigo_interno']], ';');
fputcsv($output, ['Designação', $eq['designacao']], ';');
fputcsv($output, ['Categoria', $eq['categoria']], ';');
fputcsv($output, ['Marca', $eq['marca']], ';');
fputcsv($output, ['Modelo', $eq['modelo']], ';');
fputcsv($output, ['Nº de Série (S/N)', $eq['numero_serie']], ';');
fputcsv($output, ['Fabricante', $eq['fabricante']], ';');
fputcsv($output, ['Ano de Fabrico', $eq['ano_fabrico']], ';');
fputcsv($output, ['Tipo de Entrada', $eq['tipo_entrada']], ';');
fputcsv($output, ['Data de Aquisição', date('d/m/Y', strtotime($eq['data_aquisicao']))], ';');
fputcsv($output, ['Custo de Aquisição', number_format($eq['custo_aquisicao'], 2, ',', ' ') . ' €'], ';');
fputcsv($output, ['Grau de Criticidade', $eq['criticidade']], ';');
fputcsv($output, ['Estado Funcional Atual', $eq['estado_atual']], ';');
fputcsv($output, ['Localização', $eq['edificio'] . " - " . $eq['servico_departamento'] . " - " . $eq['sala_gabinete']], ';');
fputcsv($output, ['Observações Técnicas', $eq['observacoes'] ?: 'Nenhuma observação registada.'], ';');

// Espaço regulamentar no CSV
fputcsv($output, [], ';');
fputcsv($output, [], ';');

// --- SECÇÃO 2: GARANTIA E CONTRATOS ---
fputcsv($output, ['--- ESTADO DA GARANTIA E CONTRATO DE MANUTENÇÃO ---'], ';');

$sql_garantia = "SELECT gc.*, f.nome_empresa AS entidade_responsavel_nome 
                 FROM garantias_contratos gc
                 LEFT JOIN fornecedores f ON gc.entidade_responsavel_id = f.id
                 WHERE gc.equipamento_id = ?";

$stmt_gar = mysqli_prepare($conn, $sql_garantia);
mysqli_stmt_bind_param($stmt_gar, "i", $id);
mysqli_stmt_execute($stmt_gar);
$result_gar = mysqli_stmt_get_result($stmt_gar);
$garantia = mysqli_fetch_assoc($result_gar);
mysqli_stmt_close($stmt_gar);

if ($garantia) {
    $hoje = date('Y-m-d');
    $tem_garantia = !empty($garantia['data_fim_garantia']);
    $expirada = ($tem_garantia && $garantia['data_fim_garantia'] < $hoje);
    $status_garantia = $tem_garantia ? ($expirada ? 'Expirada' : 'Válida / Ativa') : 'Não Especificada';

    fputcsv($output, ['Campo de Cobertura', 'Detalhes'], ';');
    fputcsv($output, ['Garantia do Fabricante (Início)', !empty($garantia['data_inicio_garantia']) ? date('d/m/Y', strtotime($garantia['data_inicio_garantia'])) : 'N/A'], ';');
    fputcsv($output, ['Garantia do Fabricante (Fim)', $tem_garantia ? date('d/m/Y', strtotime($garantia['data_fim_garantia'])) : 'N/A'], ';');
    fputcsv($output, ['Estado da Garantia', $status_garantia], ';');
    fputcsv($output, ['Contrato de Assistência Ativo?', $garantia['tem_contrato_manutencao'] ? 'Sim' : 'Não'], ';');
    
    if ($garantia['tem_contrato_manutencao']) {
        fputcsv($output, ['Modalidade do Contrato', $garantia['tipo_contrato'] ?: 'Não definido'], ';');
        fputcsv($output, ['Periodicidade das Intervenções', $garantia['periodicidade'] ?: 'Conforme pedido'], ';');
        fputcsv($output, ['Entidade Responsável', $garantia['entidade_responsavel_nome'] ?: 'Gestão Interna (Eng. Clínica)'], ';');
    }
    fputcsv($output, ['Cláusulas / Obs. do Contrato', $garantia['observacoes'] ?: 'Nenhuma'], ';');
} else {
    fputcsv($output, ['Aviso', 'Nenhuma apólice de garantia ou contrato ativo registado.'], ';');
}

// Espaço regulamentar no CSV
fputcsv($output, [], ';');
fputcsv($output, [], ';');

// --- SECÇÃO 3: ENTIDADES COMERCIAIS & FORNECEDORES ---
fputcsv($output, ['--- ENTIDADES COMERCIAIS & FORNECEDORES ASSOCIADOS ---'], ';');

$sql_forn = "SELECT f.*, ef.tipo_fornecedor 
             FROM equipamento_fornecedor ef
             INNER JOIN fornecedores f ON ef.fornecedor_id = f.id
             WHERE ef.equipamento_id = ?
             ORDER BY ef.tipo_fornecedor ASC";

$stmt_forn = mysqli_prepare($conn, $sql_forn);
mysqli_stmt_bind_param($stmt_forn, "i", $id);
mysqli_stmt_execute($stmt_forn);
$result_forn = mysqli_stmt_get_result($stmt_forn);

if (mysqli_num_rows($result_forn) > 0) {
    fputcsv($output, ['Empresa', 'Tipo de Vínculo', 'NIF', 'Contacto Telefónico', 'Email', 'Pessoa de Contacto'], ';');
    while ($forn = mysqli_fetch_assoc($result_forn)) {
        fputcsv($output, [
            $forn['nome_empresa'],
            $forn['tipo_fornecedor'],
            $forn['nif'],
            $forn['contacto_telefonico'],
            $forn['email'],
            $forn['pessoa_contacto']
        ], ';');
    }
} else {
    fputcsv($output, ['Aviso', 'Nenhum fornecedor vinculado a este equipamento.'], ';');
}
mysqli_stmt_close($stmt_forn);

// Espaço regulamentar no CSV
fputcsv($output, [], ';');
fputcsv($output, [], ';');

// --- SECÇÃO 4: COMPONENTES E ACESSÓRIOS ---
fputcsv($output, ['--- COMPONENTES, MÓDULOS E ACESSÓRIOS INTEGRADOS ---'], ';');

$sql_comp = "SELECT codigo_componente, designacao_componente, numero_serie_componente, observacoes 
             FROM componentes_associados 
             WHERE equipamento_pai_id = ? 
             ORDER BY codigo_componente ASC";

$stmt_comp = mysqli_prepare($conn, $sql_comp);
mysqli_stmt_bind_param($stmt_comp, "i", $id);
mysqli_stmt_execute($stmt_comp);
$result_comp = mysqli_stmt_get_result($stmt_comp);

if (mysqli_num_rows($result_comp) > 0) {
    fputcsv($output, ['Cód. Componente', 'Designação / Acessório', 'Número de Série (S/N)', 'Observações Técnicas'], ';');
    while ($comp = mysqli_fetch_assoc($result_comp)) {
        fputcsv($output, [
            $comp['codigo_componente'],
            $comp['designacao_componente'],
            $comp['numero_serie_componente'] ?: 'N/A',
            $comp['observacoes'] ?: '—'
        ], ';');
    }
} else {
    fputcsv($output, ['Aviso', 'Não existem subcomponentes mapeados para este ativo.'], ';');
}
mysqli_stmt_close($stmt_comp);

// Espaço regulamentar no CSV
fputcsv($output, [], ';');
fputcsv($output, [], ';');

// --- SECÇÃO 5: DOCUMENTAÇÃO TÉCNICA ---
fputcsv($output, ['--- DOCUMENTAÇÃO TÉCNICA ASSOCIADA ---'], ';');

$sql_doc = "SELECT tipo_documento, nome_documento, nome_ficheiro_caminho, data_documento, data_validade 
            FROM documentacao 
            WHERE equipamento_id = ? 
            ORDER BY data_documento DESC";

$stmt_doc = mysqli_prepare($conn, $sql_doc);
mysqli_stmt_bind_param($stmt_doc, "i", $id);
mysqli_stmt_execute($stmt_doc);
$result_doc = mysqli_stmt_get_result($stmt_doc);

if (mysqli_num_rows($result_doc) > 0) {
    fputcsv($output, ['Tipo de Documento', 'Nome Descritivo', 'Caminho do Ficheiro', 'Data do Doc.', 'Validade', 'Estado'], ';');
    while ($doc = mysqli_fetch_assoc($result_doc)) {
        
        // Lógica para definir se o documento está caducado/expirado
        $estado_doc = "Permanente / Válido";
        if (!empty($doc['data_validade'])) {
            $estado_doc = ($doc['data_validade'] < date('Y-m-d')) ? "EXPIRADO" : "Válido";
        }

        fputcsv($output, [
            $doc['tipo_documento'],
            $doc['nome_documento'],
            $doc['nome_ficheiro_caminho'],
            date('d/m/Y', strtotime($doc['data_documento'])),
            !empty($doc['data_validade']) ? date('d/m/Y', strtotime($doc['data_validade'])) : 'Permanente',
            $estado_doc
        ], ';');
    }
} else {
    fputcsv($output, ['Aviso', 'Não existem manuais ou contratos indexados para este equipamento.'], ';');
}
mysqli_stmt_close($stmt_doc);

// 6. Fechar ponteiro de output e ligação à BD
fclose($output);
mysqli_close($conn);
exit;
?>