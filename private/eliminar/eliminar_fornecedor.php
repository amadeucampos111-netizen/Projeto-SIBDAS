<?php
session_start();

// 1. Verificar se o ID foi passado na URL e não está vazio
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $id = intval($_GET['id']); // Garante que o ID é tratado estritamente como um número inteiro

    // 2. Configurações da Base de Dados
    $host = "vsgate-s1.dei.isep.ipp.pt";
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";

    $conn = mysqli_connect($host, $user, $pass, $dbname);

    if (!$conn) {
        die("Falha na ligação: " . mysqli_connect_error());
    }

    // =================================================================
    // VERIFICAÇÃO DE SEGURANÇA: Existem equipamentos vinculados a este fornecedor?
    // =================================================================
    // Nota: Altere 'fornecedor_id' para o nome exato da coluna na sua tabela 'equipamento_fornecedor'
    $sql_check = "SELECT COUNT(*) as total FROM equipamento_fornecedor WHERE fornecedor_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    
    if ($stmt_check) {
        mysqli_stmt_bind_param($stmt_check, "i", $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        mysqli_stmt_close($stmt_check);

        if ($row_check['total'] > 0) {
            // Se houver equipamentos dependentes deste fornecedor, a eliminação é abortada
            $_SESSION['mensagem_erro'] = "Não é possível eliminar este fornecedor porque ele está associado a " . $row_check['total'] . " equipamento(s). Altere o fornecedor desses equipamentos antes de o remover.";
            header("Location: ../listar/lista_fornecedores.php");
            mysqli_close($conn);
            exit;
        }
    }

    // =================================================================
    // AÇÃO DE ELIMINAÇÃO (Executada apenas se passar na validação acima)
    // =================================================================
    $sql_delete = "DELETE FROM fornecedores WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);

    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['mensagem_sucesso'] = "Fornecedor removido do sistema com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar eliminar o fornecedor da base de dados.";
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a eliminação.";
    }

    mysqli_close($conn);
} else {
    $_SESSION['mensagem_erro'] = "ID de fornecedor inválido ou não fornecido.";
}

// 3. Redirecionar de volta para a listagem principal
header("Location: ../listar/lista_fornecedores.php");
exit;