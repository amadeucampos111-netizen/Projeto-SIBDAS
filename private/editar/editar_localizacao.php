<?php
session_start();

// Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die("Falha na ligação: " . mysqli_connect_error()); }

// Inicializar a variável para evitar o aviso de "undefined variable" no editor
$localizacao = null;

// ==========================================
// AÇÃO 1: RECUPERAR OS DADOS ATUAIS PARA O FORMULÁRIO (GET)
// ==========================================
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM localizaciones WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $localizacao = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['mensagem_erro'] = "Localização não encontrada.";
        header("Location: ../localizacao.php");
        exit;
    }
    mysqli_stmt_close($stmt);
} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../localizacao.php");
    exit;
}

// Security Check: Se não houver dados e não for POST, bloqueia a renderização do HTML
if (!$localizacao && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../localizacao.php");
    exit;
}

// ==========================================
// AÇÃO 2: PROCESSAR A ATUALIZAÇÃO (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id                   = intval($_POST['id']);
    $edificio             = trim($_POST['edificio']);
    $piso                 = trim($_POST['piso']);
    $servico_departamento = trim($_POST['servico_departamento']);
    $sala_gabinete        = trim($_POST['sala_gabinete']);

    // Validar se nenhum campo foi limpo maliciosamente
    if (empty($edificio) || empty($piso) || empty($servico_departamento) || empty($sala_gabinete)) {
        $erro = "Todos os campos são obrigatórios.";
    } else {
        $sql_update = "UPDATE localizaciones SET 
                        edificio=?, piso=?, servico_departamento=?, sala_gabinete=? 
                       WHERE id=?";
                       
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "ssssi", 
                $edificio, $piso, $servico_departamento, $sala_gabinete, $id
            );
            
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION['mensagem_sucesso'] = "Localização hospitalar atualizada com sucesso!";
                header("Location: ../localizacao.php");
                exit;
            } else {
                // Capturar o erro caso fira a regra UNIQUE KEY (duplicado)
                if (mysqli_errno($conn) == 1062) {
                    $erro = "Erro: Já existe uma localização registada exatamente com estes dados.";
                } else {
                    $erro = "Erro ao atualizar: " . mysqli_stmt_error($stmt_update);
                }
            }
            mysqli_stmt_close($stmt_update);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Localização | MedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
    
</head>
<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-map-location-dot me-2 text-primary"></i>Editar Localização Hospitalar
                    </h4>
                    <a href="../localizacao.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <div><?php echo $erro; ?></div>
                    </div>
                <?php endif; ?>

                <form action="editar_localizacao.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo isset($localizacao) ? $localizacao['id'] : $id; ?>">

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold">Edifício / Bloco</label>
                            <input type="text" class="form-control" name="edificio" 
                                   value="<?php echo htmlspecialchars($_POST['edificio'] ?? $localizacao['edificio'] ?? ''); ?>" required>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Piso / Andar</label>
                            <input type="text" class="form-control" name="piso" 
                                   value="<?php echo htmlspecialchars($_POST['piso'] ?? $localizacao['piso'] ?? ''); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Serviço / Departamento</label>
                            <input type="text" class="form-control" name="servico_departamento" 
                                   value="<?php echo htmlspecialchars($_POST['servico_departamento'] ?? $localizacao['servico_departamento'] ?? ''); ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sala / Gabinete / Box</label>
                            <input type="text" class="form-control" name="sala_gabinete" 
                                   value="<?php echo htmlspecialchars($_POST['sala_gabinete'] ?? $localizacao['sala_gabinete'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="../localizacao.php" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Guardar Alterações
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>
<?php mysqli_close($conn); ?>