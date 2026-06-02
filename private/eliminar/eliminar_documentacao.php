<?php
session_start();

// 1. Verificar se o ID foi passado na URL e se não está vazio
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $id = intval($_GET['id']); // Garante que o ID é tratado estritamente como um número inteiro

    // 2. Configurações da Base de Dados
    $host = "localhost";
    $user = "root";
    $pass = ""; 
    $dbname = "medtrack_db";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Falha na ligação à base de dados: " . mysqli_connect_error());
    }

    // 3. Preparar a Query de Eliminação (Protegida contra SQL Injection)
    $sql = "DELETE FROM documentacao WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        
        if (mysqli_stmt_execute($stmt)) {
            // Verifica se alguma linha foi efetivamente apagada (se o ID existia)
            if (mysqli_stmt_affected_rows($stmt) > 0) {
                $_SESSION['mensagem_sucesso'] = "O registo do documento foi removido do sistema com sucesso!";
            } else {
                $_SESSION['mensagem_erro'] = "O documento indicado já não existia no sistema.";
            }
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar eliminar o registo da base de dados: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a eliminação dos dados.";
    }

    mysqli_close($conn);
} else {
    $_SESSION['mensagem_erro'] = "ID de documentação inválido ou não fornecido.";
}

// 4. Redirecionar de volta para o Arquivo de Documentação (Tabela de Listagem)
header("Location: ../documentacao.php");
exit;