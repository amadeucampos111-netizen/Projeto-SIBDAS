<?php
session_start();

// Proteção de Sessão contra acessos diretos
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.html?erro=restrito");
    exit;
}

// 1. Verificar se os parâmetros obrigatórios foram passados (via GET ou POST)
$equipamento_id  = isset($_REQUEST['equipamento_id']) ? intval($_REQUEST['equipamento_id']) : 0;
$fornecedor_id   = isset($_REQUEST['fornecedor_id']) ? intval($_REQUEST['fornecedor_id']) : 0;
$tipo_fornecedor = isset($_REQUEST['tipo']) ? trim($_REQUEST['tipo']) : '';

if ($equipamento_id <= 0 || $fornecedor_id <= 0 || empty($tipo_fornecedor)) {
    header("Location: ../fornecedores.php");
    exit;
}

// 2. Ligação à Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt"; $user = "1240896"; $pass = "campos_896"; $dbname = "db1240896"; $port = 10464;
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

$associacao = null;

// ==========================================
// PASSO 1: PROCURAR NOMES REAIS PARA EXIBIR NA CONFIRMAÇÃO (GET)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql_busca = "SELECT e.designacao AS equipamento_nome, f.nome_empresa AS fornecedor_nome 
                  FROM equipamento_fornecedor ef
                  JOIN equipamentos e ON ef.equipamento_id = e.id
                  JOIN fornecedores f ON ef.fornecedor_id = f.id
                  WHERE ef.equipamento_id = ? AND ef.fornecedor_id = ? AND ef.tipo_fornecedor = ?";
    
    $stmt_busca = mysqli_prepare($conn, $sql_busca);
    if ($stmt_busca) {
        mysqli_stmt_bind_param($stmt_busca, "iis", $equipamento_id, $fornecedor_id, $tipo_fornecedor);
        mysqli_stmt_execute($stmt_busca);
        $resultado = mysqli_stmt_get_result($stmt_busca);
        $associacao = mysqli_fetch_assoc($resultado);
        mysqli_stmt_close($stmt_busca);
    }

    // Se a associação não for encontrada ou já tiver sido apagada
    if (!$associacao) {
        header("Location: ../fornecedores.php?id=" . $equipamento_id . "&msg=erro");
        mysqli_close($conn);
        exit;
    }
}

// ==========================================
// PASSO 2: SE O UTILIZADOR CONFIRMOU (POST) -> EXECUTAR DELETE
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_eliminar'])) {
    
    $sql = "DELETE FROM equipamento_fornecedor 
            WHERE equipamento_id = ? AND fornecedor_id = ? AND tipo_fornecedor = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "iis", $equipamento_id, $fornecedor_id, $tipo_fornecedor);
        
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            // Sucesso: Redireciona com flag de sucesso
            header("Location: ../fornecedores.php?id=" . $equipamento_id . "&msg=assoc_removida");
            exit;
        } else {
            mysqli_stmt_close($stmt);
        }
    }
    
    mysqli_close($conn);
    header("Location: ../fornecedores.php?id=" . $equipamento_id . "&msg=erro");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Remoção de Vínculo</title>
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
                        <i class="fa-solid fa-link-slash fa-4x text-danger"></i>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-3">Remover Associação?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja desvincular este fornecedor do equipamento? Os dados de ambos continuarão intactos, apenas a relação comercial entre eles será eliminada.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Equipamento Técnico:</strong> <?php echo htmlspecialchars($associacao['equipamento_nome']); ?></div>
                        <div class="mb-1"><strong>Fornecedor / Parceiro:</strong> <?php echo htmlspecialchars($associacao['fornecedor_nome']); ?></div>
                        <div><strong>Tipo de Vínculo (Função):</strong> <span class="badge bg-warning text-dark fw-bold"><?php echo htmlspecialchars($tipo_fornecedor); ?></span></div>
                    </div>

                    <form action="eliminar_associacao_fornecedor.php" method="POST">
                        <input type="hidden" name="equipamento_id" value="<?php echo $equipamento_id; ?>">
                        <input type="hidden" name="fornecedor_id" value="<?php echo $fornecedor_id; ?>">
                        <input type="hidden" name="tipo" value="<?php echo htmlspecialchars($tipo_fornecedor); ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../fornecedores.php?id=<?php echo $equipamento_id; ?>" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-1"></i> Não, Cancelar
                            </a>
                            
                            <button type="submit" name="confirmar_eliminar" class="btn btn-danger px-4 fw-semibold">
                                <i class="fa-solid fa-trash me-1"></i> Sim, Remover
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