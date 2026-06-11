<?php
session_start();

// Configurações da Base de Dados
$host = "vsgate-s1.dei.isep.ipp.pt";
$user = "1240896";
$pass = "campos_896";
$dbname = "db1240896";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die("Falha na ligação: " . mysqli_connect_error()); }

// Inicializar a variável para evitar avisos de variável indefinida no editor
$fornecedor = null;

// ==========================================
// AÇÃO 1: RECUPERAR OS DADOS ATUAIS PARA O FORMULÁRIO (GET)
// ==========================================
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT * FROM fornecedores WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $fornecedor = mysqli_fetch_assoc($result);
    } else {
        $_SESSION['mensagem_erro'] = "Fornecedor não encontrado.";
        header("Location: ../listar/lista_fornecedores.php");
        exit;
    }
    mysqli_stmt_close($stmt);
} else if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../listar/lista_fornecedores.php");
    exit;
}

// Verificação de Segurança Extra
if (!$fornecedor && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../listar/lista_fornecedores.php");
    exit;
}

// ==========================================
// AÇÃO 2: PROCESSAR A ATUALIZAÇÃO (POST)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id                       = intval($_POST['id']);
    $nome_empresa              = trim($_POST['nome_empresa']);
    $nif                       = trim($_POST['nif']);
    $contacto_telefonico       = trim($_POST['contacto_telefonico']);
    $email                     = trim($_POST['email']);
    $morada                    = trim($_POST['morada']);
    $website                   = trim($_POST['website']);
    $pessoa_contacto           = trim($_POST['pessoa_contacto']);
    $telefone_pessoa_contacto  = trim($_POST['telefone_pessoa_contacto']);
    $observacoes               = trim($_POST['observacoes']);

    // Validar se os campos obrigatórios foram mantidos preenchidos
    if (empty($nome_empresa) || empty($nif) || empty($email)) {
        $erro = "Os campos Nome da Empresa, NIF e Email são obrigatórios.";
    } else {
        $sql_update = "UPDATE fornecedores SET 
                        nome_empresa=?, nif=?, contacto_telefonico=?, email=?, 
                        morada=?, website=?, pessoa_contacto=?, telefone_pessoa_contacto=?, 
                        observacoes=? 
                       WHERE id=?";
                       
        $stmt_update = mysqli_prepare($conn, $sql_update);
        
        if ($stmt_update) {
            mysqli_stmt_bind_param($stmt_update, "sssssssssi", 
                $nome_empresa, $nif, $contacto_telefonico, $email, 
                $morada, $website, $pessoa_contacto, $telefone_pessoa_contacto, 
                $observacoes, $id
            );
            
            if (mysqli_stmt_execute($stmt_update)) {
                $_SESSION['mensagem_sucesso'] = "Dados do fornecedor atualizados com sucesso!";
                header("Location: ../listar/lista_fornecedores.php");
                exit;
            } else {
                // Capturar erro se o utilizador tentar mudar o NIF para um que já existe noutro fornecedor
                if (mysqli_errno($conn) == 1062) {
                    $erro = "Erro: Já existe outro fornecedor registado com este NIF.";
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
    <title>Editar Fornecedor | MedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Titillium+Web:wght@400;600;700&display=swap" rel="stylesheet">
   
    
    <link rel="stylesheet" href="../assets/css/admin1240896.css">
    
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="card shadow-sm border-0 rounded-3 p-4">
                <div class="border-bottom pb-2 mb-4 d-flex justify-content-between align-items-center">
                    <h4 class="fw-bold text-dark mb-0">
                        <i class="fa-solid fa-truck-field me-2 text-primary"></i>Editar Ficha do Fornecedor
                    </h4>
                    <a href="../listar/lista_fornecedores.php" class="btn btn-outline-secondary btn-sm">
                        <i class="fa-solid fa-arrow-left me-1"></i> Voltar
                    </a>
                </div>

                <?php if (isset($erro)): ?>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <div><?php echo $erro; ?></div>
                    </div>
                <?php endif; ?>

                <form action="editar_fornecedor.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo isset($fornecedor) ? $fornecedor['id'] : $id; ?>">

                    <div class="row g-3">
                        
                        <div class="col-12 col-md-8">
                            <label class="form-label fw-semibold">Nome da Empresa / Entidade</label>
                            <input type="text" class="form-control" name="nome_empresa" 
                                   value="<?php echo htmlspecialchars($_POST['nome_empresa'] ?? $fornecedor['nome_empresa'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">NIF (Contribuinte)</label>
                            <input type="text" class="form-control" name="nif" 
                                   value="<?php echo htmlspecialchars($_POST['nif'] ?? $fornecedor['nif'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Contacto Telefónico Geral</label>
                            <input type="text" class="form-control" name="contacto_telefonico" 
                                   value="<?php echo htmlspecialchars($_POST['contacto_telefonico'] ?? $fornecedor['contacto_telefonico'] ?? ''); ?>">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Email de Contacto</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? $fornecedor['email'] ?? ''); ?>" required>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="text" class="form-control" name="website" 
                                   value="<?php echo htmlspecialchars($_POST['website'] ?? $fornecedor['website'] ?? ''); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Morada Oficial</label>
                            <input type="text" class="form-control" name="morada" 
                                   value="<?php echo htmlspecialchars($_POST['morada'] ?? $fornecedor['morada'] ?? ''); ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Pessoa de Contacto (Gestor de Conta / Técnico)</label>
                            <input type="text" class="form-control" name="pessoa_contacto" 
                                   value="<?php echo htmlspecialchars($_POST['pessoa_contacto'] ?? $fornecedor['pessoa_contacto'] ?? ''); ?>">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label fw-semibold">Telefone / Telemóvel da pessoa de contacto</label>
                            <input type="text" class="form-control" name="telefone_pessoa_contacto" 
                                   value="<?php echo htmlspecialchars($_POST['telefone_pessoa_contacto'] ?? $fornecedor['telefone_pessoa_contacto'] ?? ''); ?>">
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Observações / Acordos Técnicos</label>
                            <textarea class="form-control" name="observacoes" rows="4"><?php echo htmlspecialchars($_POST['observacoes'] ?? $fornecedor['observacoes'] ?? ''); ?></textarea>
                        </div>

                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <a href="../listar/lista_fornecedores.php" class="btn btn-light px-4">Cancelar</a>
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