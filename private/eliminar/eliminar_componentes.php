<?php
// 1. Iniciar a sessão para podermos devolver a mensagem de feedback
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteção de Sessão contra acessos diretos
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    session_unset();
    session_destroy();
    header("Location: ../../public/login.php?erro=restrito");
    exit;
}

// Configurações da Base de Dados
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
    $_SESSION['mensagem_erro'] = "Componente inválido ou não especificado.";
    header("Location: ../listar/lista_componentes.php");
    exit();
}

$componente = null;

// ==========================================
// PASSO 1: PROCURAR O COMPONENTE E O SEU EQUIPA VINCULADO
// ==========================================
// Fazemos um JOIN com a tabela de equipamentos para contextualizar o aviso visual
$sql_busca = "SELECT c.designacao_componente, c.numero_serie_componente, e.designacao AS equipamento_pai 
              FROM componentes_associados c 
              LEFT JOIN equipamentos e ON c.equipamento_pai_id = e.id 
              WHERE c.id = ?";

$stmt_busca = mysqli_prepare($conn, $sql_busca);
if ($stmt_busca) {
    mysqli_stmt_bind_param($stmt_busca, "i", $id);
    mysqli_stmt_execute($stmt_busca);
    $resultado = mysqli_stmt_get_result($stmt_busca);
    $componente = mysqli_fetch_assoc($resultado);
    mysqli_stmt_close($stmt_busca);
}

// Se o componente já não constar no sistema, regressa à listagem principal
if (!$componente) {
    $_SESSION['mensagem_erro'] = "O componente indicado já não existe no sistema.";
    header("Location: ../listar/lista_componentes.php");
    exit();
}

// ==========================================
// PASSO 2: SE O UTILIZADOR CONFIRMOU A ELIMINAÇÃO (CLICOU EM "SIM")
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar_eliminar'])) {
    
    $sql_delete = "DELETE FROM componentes_associados WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);

    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['mensagem_sucesso'] = "Componente eliminado com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao eliminar o componente da base de dados.";
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['mensagem_erro'] = "Erro ao preparar a eliminação.";
    }

    mysqli_close($conn);
    header("Location: ../listar/lista_componentes.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>MedTrack | Confirmar Eliminação de Componente</title>
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
                        <i class="fa-solid fa-puzzle-piece fa-4x text-danger"></i>
                    </div>
                    
                    <h4 class="fw-bold text-dark mb-3">Eliminar Componente?</h4>
                    
                    <p class="text-muted mb-4">
                        Tem a certeza que deseja remover este componente? Esta ação irá desassociá-lo permanentemente da árvore técnica do equipamento de suporte.
                    </p>

                    <div class="bg-light p-3 rounded border text-start mb-4">
                        <div class="mb-1"><strong>Componente:</strong> <?php echo htmlspecialchars($componente['designacao_componente']); ?></div>
                        <div class="mb-1"><strong>Nº Série / Lote:</strong> <?php echo htmlspecialchars($componente['numero_serie_componente'] ?? 'N/D'); ?></div>
                        <div><strong>Equipamento Associado:</strong> <span class="text-primary fw-semibold"><?php echo htmlspecialchars($componente['equipamento_pai'] ?? 'Nenhum'); ?></span></div>
                    </div>

                    <form action="eliminar_componentes.php" method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        
                        <div class="d-flex justify-content-center gap-3">
                            <a href="../listar/lista_componentes.php" class="btn btn-light px-4 border">
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