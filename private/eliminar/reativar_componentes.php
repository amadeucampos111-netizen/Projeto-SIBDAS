<?php
session_start();

if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.html?erro=restrito");
    exit;
}

$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";
$port = 10464;

$conn = mysqli_connect($host, $user, $pass, $dbname, $port);
if (!$conn) { 
    die("Falha na ligação: " . mysqli_connect_error()); 
}

$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['mensagem_erro'] = "ID de componente inválido ou não fornecido.";
    header("Location: ../listar/lista_componentes_inativos.php");
    exit;
}

$componente = null;

// ==========================================
// PASSO 1: VERIFICAR SE O COMPONENTE EXISTE EXCLUSIVAMENTE NO HISTÓRICO INATIVO
// ==========================================
$sql_busca = "SELECT c.designacao_componente, c.numero_serie_componente, e.designacao AS equipamento_pai 
              FROM componentes_associados c 
              LEFT JOIN equipamentos e ON c.equipamento_pai_id = e.id 
              WHERE c.id = ? AND c.estado = 'Inativo'";

$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $componente = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);
}

if (!$componente) {
    $_SESSION['mensagem_erro'] = "O componente não foi encontrado no histórico ou já se encontra ativo.";
    header("Location: ../listar/lista_componentes_inativos.php");
    exit;
}

// ==========================================
// PASSO 2: EXECUTAR REATIVAÇÃO (MUDAR ESTADO PARA 'ATIVO') APÓS SUBMISSÃO POST
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_reativacao'])) {
    
    $sql_update = "UPDATE componentes_associados SET estado = 'Ativo' WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);

    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "i", $id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['mensagem_sucesso'] = "O componente '" . $componente['designacao_componente'] . "' foi restaurado e vinculado de volta com sucesso!";
            mysqli_close($conn);
            // Redireciona de volta para a listagem principal operacional (Ativos)
            header("Location: ../listar/lista_componentes.php");
            exit;
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar restaurar o componente.";
        }
        mysqli_stmt_close($stmt_update);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao processar a reativação.";
    }

    mysqli_close($conn);
    header("Location: ../listar/lista_componentes_inativos.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Restauro de Componente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="card shadow-sm border-0 rounded-3 text-center p-4">
                <div class="card-body">
                    <div class="text-success mb-3">
                        <i class="fa-solid fa-arrows-rotate fa-4x"></i>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-3">Reativar Componente Hospitalar?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja restaurar este componente? Ele voltará a constar imediatamente na árvore de arquitetura técnica do equipamento médico principal configurado.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Componente:</strong> <?php echo htmlspecialchars($componente['designacao_componente']); ?></div>
                        <div class="mb-1"><strong>Nº Série / Lote:</strong> <?php echo htmlspecialchars($componente['numero_serie_componente'] ?? 'N/D'); ?></div>
                        <div><strong>Equipamento de Vínculo:</strong> <span class="text-success fw-semibold"><?php echo htmlspecialchars($componente['equipamento_pai'] ?? 'Nenhum'); ?></span></div>
                    </div>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../listar/lista_componentes_inativos.php" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-1"></i> Não, Cancelar
                            </a>
                            
                            <button type="submit" name="confirmar_reativacao" class="btn btn-success px-4 fw-semibold">
                                <i class="fa-solid fa-check me-1"></i> Sim, Reativar
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