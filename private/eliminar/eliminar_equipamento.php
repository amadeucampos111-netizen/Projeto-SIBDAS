<?php
session_start();

// 1. Verificar se o ID foi passado na URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $id = intval($_GET['id']); // Garante que o ID é estritamente um número inteiro

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

    // 3. Preparar a Query de Eliminação (Segurança contra SQL Injection)
    $sql = "DELETE FROM equipamentos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensagem_sucesso'] = "Equipamento removido do inventário com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Não foi possível eliminar o equipamento devido a restrições de dados.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a eliminação.";
    }

    mysqli_close($conn);
} else {
    $_SESSION['mensagem_erro'] = "ID de equipamento inválido ou não fornecido.";
}

// 4. Redirecionar sempre de volta para a página principal de equipamentos
header("Location: ../listar/listar_equipamentos.php");
exit;
?>