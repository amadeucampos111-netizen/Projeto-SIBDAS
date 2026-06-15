

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
    die("Falha na ligação: " . mysqli_connect_error()); 
}

// Recuperar o ID enviado (via GET no clique inicial ou via POST no formulário de confirmação)
$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['mensagem_erro'] = "ID de fornecedor inválido ou não fornecido.";
    header("Location: ../listar/lista_fornecedores.php");
    exit;
}

$sql_check = "SELECT COUNT(*) as total FROM equipamento_fornecedor WHERE fornecedor_id = ?";

$stmt_check = mysqli_prepare($conn, $sql_check);



if ($stmt_check) {

    mysqli_stmt_bind_param($stmt_check, "i", $id);

    mysqli_stmt_execute($stmt_check);

    $result_check = mysqli_stmt_get_result($stmt_check);

    $row_check = mysqli_fetch_assoc($result_check);

    mysqli_stmt_close($stmt_check);



    if ($row_check['total'] > 0) {

        // Bloqueia imediatamente se houver dependências

        $_SESSION['mensagem_erro'] = "Não é possível eliminar este fornecedor porque ele está associado a " . $row_check['total'] . " equipamento(s). Altere o fornecedor desses equipamentos antes de o remover.";

        mysqli_close($conn);

        header("Location: ../listar/lista_fornecedores.php");

        exit;

    }

}

$fornecedor = null;

// ==========================================
// PASSO 1: PROCURAR O FORNECEDOR PARA EXIBIR NOS DETALHES
// ==========================================
$sql_busca = "SELECT nome_empresa, nif FROM fornecedores WHERE id = ?";
$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $fornecedor = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);

}

// Se o fornecedor não existir na base de dados, volta para a listagem
if (!$fornecedor) {
    $_SESSION['mensagem_erro'] = "Fornecedor não encontrado.";
    header("Location: ../listar/lista_fornecedores.php");
    exit;
}

// ==========================================
// PASSO 2: SE O UTILIZADOR CONFIRMOU A INATIVAÇÃO (CLICOU EM "SIM")
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_inativar'])) {
    
    // ALTERADO: Query mudada de DELETE para UPDATE de estado
    $sql_update = "UPDATE fornecedores SET estado = 'Inativo' WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);

    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "i", $id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['mensagem_sucesso'] = "Fornecedor movido para o arquivo de inativos com sucesso!";
            mysqli_close($conn);
            // Redireciona diretamente para a lista de fornecedores inativos
            header("Location: ../listar/lista_fornecedores_inativos.php");
            exit;
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar alterar o estado do fornecedor.";
        }
        mysqli_stmt_close($stmt_update);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a desativação.";
    }

    mysqli_close($conn);
    header("Location: ../listar/lista_fornecedores.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Desativação de Fornecedor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card shadow-sm border-0 rounded-3 text-center p-4">
                <div class="card-body">
                    <div class="text-warning mb-3">
                        <i class="fa-solid fa-box-archive fa-4x"></i>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-3">Arquivar Fornecedor?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja alterar o estado desta entidade comercial para <strong>Inativo</strong>? 
                        O registo será removido das listagens operacionais diárias e enviado para a lista de inativos.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Nome da Empresa / Entidade:</strong> <?php echo htmlspecialchars($fornecedor['nome_empresa']); ?></div>
                        <div><strong>NIF / Identificação Fiscal:</strong> <?php echo htmlspecialchars($fornecedor['nif'] ?? 'N/D'); ?></div>
                    </div>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../listar/lista_fornecedores.php" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-1"></i> Não, Cancelar
                            </a>
                            
                            <button type="submit" name="confirmar_inativar" class="btn btn-warning text-dark px-4 fw-semibold">
                                <i class="fa-solid fa-archive me-1"></i> Sim, Inativar
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