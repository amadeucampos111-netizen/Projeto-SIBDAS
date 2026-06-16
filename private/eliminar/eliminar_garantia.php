<?php
session_start();

// 1. Proteção de Sessão contra acessos diretos
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.html?erro=restrito");
    exit;
}

// 2. Ligação à Base de Dados
$conn = mysqli_connect("vsgate-s1.dei.isep.ipp.pt", "1240896", "campos_896", "db1240896", 10464);
if (!$conn) { 
    die("Falha na ligação: " . mysqli_connect_error()); 
}

// Recuperar o ID enviado (via GET no clique inicial ou via POST no formulário de confirmação)
$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['msg_erro'] = "Pedido inválido ou ID não fornecido.";
    header("Location: ../listar/lista_garantias.php");
    exit;
}

$contrato = null;

// ==========================================
// PASSO 1: PROCURAR INFORMAÇÃO DA GARANTIA PARA MOSTRAR NO ECRÃ
// ==========================================
$sql_busca = "SELECT gc.id, e.designacao, e.numero_serie, gc.data_inicio_garantia, gc.data_fim_garantia 
              FROM garantias_contratos gc 
              JOIN equipamentos e ON gc.equipamento_id = e.id 
              WHERE gc.id = ?";

$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $contrato = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);
}

// Se o registo não for encontrado no sistema, volta para trás
if (!$contrato) {
    $_SESSION['msg_erro'] = "O registo de garantia ou contrato não foi encontrado.";
    header("Location: ../listar/lista_garantias.php");
    exit;
}

// ==========================================
// PASSO 2: SE O UTILIZADOR CONFIRMOU O ARQUIVAMENTO (CLICOU EM "SIM")
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_arquivar'])) {
    
    // ALTERADO: Mudança de DELETE para UPDATE de estado
    $sql_update = "UPDATE garantias_contratos SET estado = 'Inativo' WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);

    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "i", $id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['msg_sucesso'] = "O registo de garantia/contrato foi movido para o arquivo com sucesso.";
            mysqli_close($conn);
            // Redireciona diretamente para a nova lista de inativos
            header("Location: ../listar/lista_garantias_inativas.php");
            exit;
        } else {
            $_SESSION['msg_erro'] = "Erro técnico ao tentar arquivar: " . mysqli_stmt_error($stmt_update);
        }
        mysqli_stmt_close($stmt_update);
    } else {
        $_SESSION['msg_erro'] = "Erro interno ao preparar o arquivamento.";
    }

    mysqli_close($conn);
    header("Location: ../listar/lista_garantias.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Arquivamento de Garantia</title>
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
                    
                    <h4 class="fw-bold text-dark mb-3">Arquivar Registo de Garantia?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja alterar o estado deste contrato para <strong>Inativo</strong>? O registo será retirado das listagens diárias operacionais e enviado para o arquivo histórico.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Equipamento:</strong> <?php echo htmlspecialchars($contrato['designacao']); ?> (S/N: <?php echo htmlspecialchars($contrato['numero_serie']); ?>)</div>
                        <div class="mb-1"><strong>Início Cobertura:</strong> <?php echo $contrato['data_inicio_garantia'] ? date('d/m/Y', strtotime($contrato['data_inicio_garantia'])) : 'N/D'; ?></div>
                        <div><strong>Fim Cobertura:</strong> <?php echo $contrato['data_fim_garantia'] ? date('d/m/Y', strtotime($contrato['data_fim_garantia'])) : 'N/D'; ?></div>
                    </div>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../listar/lista_garantias.php" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-1"></i> Não, Cancelar
                            </a>
                            
                            <button type="submit" name="confirmar_arquivar" class="btn btn-warning text-dark px-4 fw-semibold">
                                <i class="fa-solid fa-archive me-1"></i> Sim, Arquivar
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