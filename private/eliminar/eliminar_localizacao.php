<?php
session_start();

// 1. Proteção de Sessão contra acessos diretos
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.html?erro=restrito");
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
    $_SESSION['mensagem_erro'] = "ID de localização inválido ou não fornecido.";
    header("Location: ../localizacao.php");
    exit;
}

// =================================================================
// VERIFICAÇÃO DE SEGURANÇA: Existem equipamentos nesta localização?
// =================================================================
// Mantém-se a regra de negócio: mesmo para inativar, não deve haver equipamentos vinculados ativos
$sql_check = "SELECT COUNT(*) as total FROM equipamentos WHERE localizacao_id = ?";
$stmt_check = mysqli_prepare($conn, $sql_check);

if ($stmt_check) {
    mysqli_stmt_bind_param($stmt_check, "i", $id);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $row_check = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);

    if ($row_check['total'] > 0) {
        $_SESSION['mensagem_erro'] = "Não é possível arquivar esta localização porque existem " . $row_check['total'] . " equipamento(s) associado(s) a ela. Transfira os equipamentos primeiro.";
        mysqli_close($conn);
        header("Location: ../localizacao.php");
        exit;
    }
}

$localizacao = null;

// ==========================================
// PASSO 1: PROCURAR A LOCALIZAÇÃO COMPLETA PARA MOSTRAR OS DETALHES
// ==========================================
// Expandido para trazer todos os campos contextuais na visualização de confirmação
$sql_busca = "SELECT edificio, piso, servico_departamento, sala_gabinete FROM localizaciones WHERE id = ?";
$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $localizacao = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);
}

// Se a localização não existir na base de dados, regressa à lista
if (!$localizacao) {
    $_SESSION['mensagem_erro'] = "Localização hospitalar não encontrada.";
    header("Location: ../localizacao.php");
    exit;
}

// ==========================================
// PASSO 2: SE O UTILIZADOR CONFIRMOU O ARQUIVAMENTO (CLICOU EM "SIM")
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_arquivar'])) {
    
    // ALTERADO: Mudança radical de DELETE físico para UPDATE de estado lógico
    $sql_update = "UPDATE localizaciones SET estado = 'Inativo' WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);

    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "i", $id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['mensagem_sucesso'] = "A localização hospitalar foi movida para o arquivo histórico com sucesso!";
            mysqli_close($conn);
            // Redireciona diretamente para o arquivo conforme solicitado
            header("Location: ../listar/lista_localizacoes_inativas.php");
            exit;
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar arquivar a localização no sistema.";
        }
        mysqli_stmt_close($stmt_update);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar o arquivamento.";
    }

    mysqli_close($conn);
    header("Location: ../localizacao.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Arquivamento de Localização</title>
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
                    
                    <h4 class="fw-bold text-dark mb-3">Arquivar Localização Hospitalar?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja desativar esta área? O registo sairá das listagens diárias operacionais e será guardado de forma segura no histórico do sistema.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Edifício / Bloco:</strong> <?php echo htmlspecialchars($localizacao['edificio']); ?></div>
                        <div class="mb-1"><strong>Piso / Andar:</strong> <?php echo htmlspecialchars($localizacao['piso']); ?></div>
                        <div class="mb-1"><strong>Serviço / Departamento:</strong> <?php echo htmlspecialchars($localizacao['servico_departamento']); ?></div>
                        <div class="mb-1"><strong>Sala / Gabinete:</strong> <?php echo htmlspecialchars($localizacao['sala_gabinete']); ?></div>
                        <hr class="my-2">
                        <div><strong>Estado de Vínculos:</strong> <span class="text-success"><i class="fa-solid fa-circle-check me-1"></i>Livre (Pronto a Arquivar)</span></div>
                    </div>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../localizacao.php" class="btn btn-light px-4 border">
                                <i class="fa-solid fa-xmark me-1"></i> Não, Cancelar
                            </a>
                            
                            <button type="submit" name="confirmar_arquivar" class="btn btn-warning text-dark px-4 fw-semibold">
                                <i class="fa-solid fa-box-archive me-1"></i> Sim, Arquivar
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