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

// Recuperar o ID enviado
$id = isset($_GET['id']) ? intval($_GET['id']) : intval($_POST['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['mensagem_erro'] = "ID de localização inválido ou não fornecido.";
    header("Location: ../listar/lista_localizacoes_inativas.php");
    exit;
}

$localizacao = null;

// ==========================================
// PASSO 1: PROCURAR A LOCALIZAÇÃO EXCLUSIVAMENTE NO ARQUIVO INATIVO
// ==========================================
$sql_busca = "SELECT edificio, piso, servico_departamento, sala_gabinete FROM localizaciones WHERE id = ? AND estado = 'Inativo'";
$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $localizacao = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);
}

// Se o registo não for elegível para restauro, volta atrás
if (!$localizacao) {
    $_SESSION['mensagem_erro'] = "Localização não encontrada no arquivo histórico ou já se encontra ativa.";
    header("Location: ../listar/lista_localizacoes_inativas.php");
    exit;
}

// ==========================================
// PASSO 2: REATIVAR (MUDAR ESTADO PARA 'ATIVO') APÓS POST
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_reativacao'])) {
    
    $sql_update = "UPDATE localizaciones SET estado = 'Ativo' WHERE id = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);

    if ($stmt_update) {
        mysqli_stmt_bind_param($stmt_update, "i", $id);
        
        if (mysqli_stmt_execute($stmt_update)) {
            $_SESSION['mensagem_sucesso'] = "A área '" . $localizacao['servico_departamento'] . "' foi restaurada e reativada com sucesso!";
            mysqli_close($conn);
            // Redireciona diretamente para a consola operacional de gestão
            header("Location: ../localizacao.php");
            exit;
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar restaurar a localização.";
        }
        mysqli_stmt_close($stmt_update);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao processar reativação.";
    }

    mysqli_close($conn);
    header("Location: ../listar/lista_localizacoes_inativas.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Reativação de Localização</title>
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
                    
                    <h4 class="fw-bold text-dark mb-3">Reativar Localização Hospitalar?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja restaurar esta localização? Ela voltará a ficar disponível para seleção imediata no mapeamento e vinculação de novos dispositivos e equipamentos médicos.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Edifício:</strong> <?php echo htmlspecialchars($localizacao['edificio']); ?></div>
                        <div class="mb-1"><strong>Piso:</strong> <?php echo htmlspecialchars($localizacao['piso']); ?></div>
                        <div class="mb-1"><strong>Serviço:</strong> <?php echo htmlspecialchars($localizacao['servico_departamento']); ?></div>
                        <div><strong>Sala/Gabinete:</strong> <?php echo htmlspecialchars($localizacao['sala_gabinete']); ?></div>
                    </div>

                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../listar/lista_localizacoes_inativas.php" class="btn btn-light px-4 border">
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