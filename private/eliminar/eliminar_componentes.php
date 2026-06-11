<?php
// 1. Iniciar a sessão para podermos devolver a mensagem de feedback
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Verificar se o ID foi passado via URL (método GET)
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    // 3. Configurações da Base de Dados
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";
    $port = 10464;

    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);

    if ($conn) {
        // 4. Query de eliminação direta usando Prepared Statement (Segurança contra SQL Injection)
        $sql = "DELETE FROM componentes_associados WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $id);
            
            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['mensagem_sucesso'] = "Componente eliminado com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "Erro ao eliminar o componente da base de dados.";
            }
            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['mensagem_erro'] = "Erro ao preparar a eliminação.";
        }
        mysqli_close($conn);
    } else {
        $_SESSION['mensagem_erro'] = "Falha na ligação à base de dados.";
    }
} else {
    $_SESSION['mensagem_erro'] = "Componente inválido ou não especificado.";
}

// 5. Redirecionar imediatamente de volta para a listagem
header("Location: ../listar/lista_componentes.php");
exit();
?>