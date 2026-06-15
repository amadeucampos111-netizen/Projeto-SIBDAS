<?php
session_start();

// 1. Proteção de Sessão
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.php?erro=restrito");
    exit;
}

// 2. Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);
if (!$conn) { 
    die("Falha na ligação: " . mysqli_connect_error()); 
}

// Recuperar o ID enviado (seja por GET para exibir a página, ou POST para confirmar)
$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['mensagem_erro'] = "ID de equipamento inválido ou não fornecido.";
    header("Location: ../listar/listar_equipamentos.php");
    exit;
}

$equipamento = null;

// ==========================================
// PASSO 1: PROCURAR O EQUIPAMENTO PARA MOSTRAR NA CONFIRMAÇÃO
// ==========================================
$sql_busca = "SELECT designacao, codigo_interno FROM equipamentos WHERE id = ?";
$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $equipamento = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);
}

// Se o equipamento não existir na base de dados, volta para a listagem
if (!$equipamento) {
    $_SESSION['mensagem_erro'] = "Equipamento não encontrado.";
    header("Location: ../listar/listar_equipamentos.php");
    exit;
}

// ==========================================
// PASSO 2: SE O UTILIZADOR CLICOU EM "SIM" (SUBMETEU O FORMULÁRIO)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_eliminar'])) {
    
    $sql_delete = "DELETE FROM equipamentos WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);

    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['mensagem_sucesso'] = "Equipamento removido do inventário com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Não foi possível eliminar o equipamento devido a restrições de dados (ex: registos associados noutras tabelas).";
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a eliminação.";
    }

    mysqli_close($conn);
    header("Location: ../listar/listar_equipamentos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Eliminação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card shadow-sm border-0 rounded-3 text-center p-4">
                <div class="card-body">
                    <div class="text-danger mb-3">
                        <i class="fa-solid fa-triangle-exclamation fa-4x animate__animated animate__pulse animate__infinite"></i>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-3">Eliminar Equipamento?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja remover permanentemente do inventário o seguinte equipamento? 
                        Esta ação <strong>não pode ser revertida</strong>.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Designação:</strong> <?php echo htmlspecialchars($equipamento['designacao']); ?></div>
                        <div><strong>Código Interno:</strong> <?php echo htmlspecialchars($equipamento['codigo_interno']); ?></div>
                    </div>

                    <form action="eliminar_equipamento.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../listar/listar_equipamentos.php" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-1"></i> Não, Cancelar
                            </a>
                            
                            <button type="submit" name="confirmar_eliminar" class="btn btn-danger px-4fw-semibold">
                                <i class="fa-solid fa-trash me-1"></i> Sim, Eliminar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
<?php mysqli_close($conn); ?>