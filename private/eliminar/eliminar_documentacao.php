<?php
session_start();

// 1. Proteção de Sessão contra acessos diretos
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
    die("Falha na ligação à base de dados: " . mysqli_connect_error()); 
}

// Recuperar o ID enviado (via GET no clique inicial ou via POST no formulário de confirmação)
$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['mensagem_erro'] = "ID de documentação inválido ou não fornecido.";
    header("Location: ../listar/lista_documentos.php");
    exit;
}

$documento = null;

// ==========================================
// PASSO 1: PROCURAR O DOCUMENTO PARA MOSTRAR OS DETALHES
// ==========================================
$sql_busca = "SELECT nome_documento, tipo_documento FROM documentacao WHERE id = ?";
$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $documento = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);
}

// Se o documento já não existir na base de dados, regressa imediatamente à lista
if (!$documento) {
    $_SESSION['mensagem_erro'] = "O documento indicado já não existia no sistema.";
    header("Location: ../listar/lista_documentos.php");
    exit;
}

// ==========================================
// PASSO 2: SE O UTILIZADOR CONFIRMOU A ELIMINAÇÃO (CLICOU EM "SIM")
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_eliminar'])) {
    
    $sql_delete = "DELETE FROM documentacao WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);

    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            // Verifica se alguma linha foi efetivamente afetada
            if (mysqli_stmt_affected_rows($stmt_delete) > 0) {
                $_SESSION['mensagem_sucesso'] = "O registo do documento foi removido do sistema com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "O documento indicado já não existia no sistema.";
            }
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar eliminar o registo da base de dados: " . mysqli_stmt_error($stmt_delete);
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a eliminação dos dados.";
    }

    mysqli_close($conn);
    header("Location: ../listar/lista_documentos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Eliminação de Documento</title>
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
                        <i class="fa-solid fa-file-circle-xmark fa-4x"></i>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-3">Eliminar Documento?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja apagar este registo documental do arquivo técnico? 
                        Esta ação removerá permanentemente as referências e metadados do sistema de forma <strong>irreversível</strong>.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Título / Nome do Ficheiro:</strong> <?php echo htmlspecialchars($documento['nome_documento']); ?></div>
                        <div><strong>Tipo de Documento:</strong> <span class="badge bg-secondary"><?php echo htmlspecialchars($documento['tipo_documento'] ?? 'Não especificado'); ?></span></div>
                    </div>

                    <form action="eliminar_documentacao.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../listar/lista_documentos.php" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-1"></i> Não, Cancelar
                            </a>
                            
                            <button type="submit" name="confirmar_eliminar" class="btn btn-danger px-4 fw-semibold">
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