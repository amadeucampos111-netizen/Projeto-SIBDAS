<?php
// 1. Iniciar a sessão para mensagens de feedback
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);
if (!$conn) {
    die("Erro na ligação à base de dados: " . mysqli_connect_error());
}

// ==========================================
// MODO POST: PROCESSAR A ATUALIZAÇÃO
// ==========================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $equipamento_pai_id = isset($_POST['equipamento_pai_id']) ? intval($_POST['equipamento_pai_id']) : 0;
    $codigo_componente = trim($_POST['codigo_componente'] ?? '');
    $designacao_componente = trim($_POST['designacao_componente'] ?? '');
    $numero_serie_componente = trim($_POST['numero_serie_componente'] ?? '');
    $observacoes = trim($_POST['observacoes'] ?? '');

    // Validação básica dos campos obrigatórios
    if ($id === 0 || $equipamento_pai_id === 0 || empty($designacao_componente)) {
        $_SESSION['mensagem_erro'] = "Por favor, preencha todos os campos obrigatórios.";
        header("Location: ../listar/lista_componentes.php");
        exit();
    }

    // Query de atualização com Prepared Statement
    $sql_update = "UPDATE componentes_associados 
                   SET equipamento_pai_id = ?, codigo_componente = ?, designacao_componente = ?, numero_serie_componente = ?, observacoes = ? 
                   WHERE id = ?";
                   
    $stmt = mysqli_prepare($conn, $sql_update);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issssi", $equipamento_pai_id, $codigo_componente, $designacao_componente, $numero_serie_componente, $observacoes, $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensagem_sucesso'] = "Componente atualizado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao atualizar na base de dados: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_erro'] = "Falha ao preparar a consulta de atualização.";
    }

    mysqli_close($conn);
    // Redireciona de volta para a tua listagem principal
    header("Location: ../listar/lista_componentes.php");
    exit();
}

// ==========================================
// MODO GET: MOSTRAR O FORMULÁRIO DE EDIÇÃO
// ==========================================
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    $_SESSION['mensagem_erro'] = "Componente não especificado para edição.";
    header("Location: ../listar/lista_componentes.php");
    exit();
}

// Procurar os dados do componente atual
$sql_select = "SELECT * FROM componentes_associados WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql_select);
$componente = null;

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $componente = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
}

// Se o componente não existir na BD, volta para trás
if (!$componente) {
    $_SESSION['mensagem_erro'] = "Componente não encontrado.";
    header("Location: ../listar/lista_componentes.php");
    exit();
}

// Procurar lista de equipamentos para preencher o Select do formulário
$query_equips = "SELECT id, designacao, codigo_interno FROM equipamentos ORDER BY designacao ASC";
$result_equips = mysqli_query($conn, $query_equips);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Componente | MedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/admin1240896.css" rel="stylesheet">
    <style>
        body { font-family: 'Titillium Web', sans-serif; background-color: #f8f9fa; }
        .card-custom { border: none; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); border-radius: 10px; }
    </style>
</head>
<body>

    <div class="container mt-5 mb-5" style="max-width: 800px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold text-dark mb-1">Modificar Componente</h2>
                <p class="text-muted mb-0">Altere as informações do sub-módulo associado abaixo.</p>
            </div>
            <a href="../listar/lista_componentes.php" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Cancelar e Voltar
            </a>
        </div>

        <div class="card card-custom p-4 bg-white">
            <form action="editar_componente.php" method="POST">
                
                <input type="hidden" name="id" value="<?php echo $componente['id']; ?>">

                <div class="row g-3">
                    
                    <div class="col-12 col-md-6">
                        <label for="equipamento_pai_id" class="form-label fw-semibold">Equipamento Médico Vinculado</label>
                        <select class="form-select" id="equipamento_pai_id" name="equipamento_pai_id" required>
                            <option value="" disabled>Selecione um equipamento...</option>
                            <?php 
                            if ($result_equips && mysqli_num_rows($result_equips) > 0) {
                                while ($eq = mysqli_fetch_assoc($result_equips)) {
                                    $selected = ($eq['id'] == $componente['equipamento_pai_id']) ? 'selected' : '';
                                    echo "<option value='{$eq['id']}' {$selected}>" . htmlspecialchars($eq['designacao']) . " (Inv: {$eq['codigo_interno']})</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12 col-md-6">
                        <label for="codigo_componente" class="form-label fw-semibold">Código do Componente</label>
                        <input type="text" class="form-control" id="codigo_componente" name="codigo_componente" 
                               value="<?php echo htmlspecialchars($componente['codigo_componente']); ?>">
                    </div>

                    <div class="col-12 col-md-7">
                        <label for="designacao_componente" class="form-label fw-semibold">Designação do Componente</label>
                        <input type="text" class="form-control" id="designacao_componente" name="designacao_componente" 
                               value="<?php echo htmlspecialchars($componente['designacao_componente']); ?>" required>
                    </div>

                    <div class="col-12 col-md-5">
                        <label for="numero_serie_componente" class="form-label fw-semibold">Número de Série</label>
                        <input type="text" class="form-control" id="numero_serie_componente" name="numero_serie_componente" 
                               value="<?php echo htmlspecialchars($componente['numero_serie_componente']); ?>">
                    </div>

                    <div class="col-12">
                        <label for="observacoes" class="form-label fw-semibold">Observações / Notas Técnicas</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($componente['observacoes']); ?></textarea>
                    </div>

                </div>

                <div class="mt-4 d-flex justify-content-end gap-2">
                    <a href="../listar/lista_componentes.php" class="btn btn-outline-secondary px-4">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Gravar Alterações
                    </button>
                </div>

            </form>
        </div>
    </div>

</body>
</html>
<?php 
mysqli_close($conn); 
?>