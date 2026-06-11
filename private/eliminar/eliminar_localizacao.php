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
    $port = 10464;

    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);

    if (!$conn) {
        die("Falha na ligação: " . mysqli_connect_error());
    }

    // =================================================================
    // VERIFICAÇÃO DE SEGURANÇA: Existem equipamentos nesta localização?
    // =================================================================
    $sql_check = "SELECT COUNT(*) as total FROM equipamentos WHERE localizacao_id = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    
    if ($stmt_check) {
        mysqli_stmt_bind_param($stmt_check, "i", $id);
        mysqli_stmt_execute($stmt_check);
        $result_check = mysqli_stmt_get_result($stmt_check);
        $row_check = mysqli_fetch_assoc($result_check);
        mysqli_stmt_close($stmt_check);

        if ($row_check['total'] > 0) {
            // Se existirem equipamentos associados, impede a eliminação
            $_SESSION['mensagem_erro'] = "Não é possível eliminar esta localização porque existem " . $row_check['total'] . " equipamento(s) associado(s) a ela. Transfira os equipamentos primeiro.";
            header("Location: ../localizacao.php");
            mysqli_close($conn);
            exit;
        }
    }

    // =================================================================
    // AÇÃO DE ELIMINAÇÃO (Apenas se passar a validação acima)
    // =================================================================
    $sql_delete = "DELETE FROM localizaciones WHERE id = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);

    if ($stmt_delete) {
        mysqli_stmt_bind_param($stmt_delete, "i", $id);
        
        if (mysqli_stmt_execute($stmt_delete)) {
            $_SESSION['mensagem_sucesso'] = "Localização hospitalar removida do sistema com sucesso!";
        } else {
            $_SESSION['mensagem_erro'] = "Erro técnico ao tentar eliminar a localização da base de dados.";
        }
        mysqli_stmt_close($stmt_delete);
    } else {
        $_SESSION['mensagem_erro'] = "Erro interno ao preparar a eliminação.";
    }

    mysqli_close($conn);
} else {
    $_SESSION['mensagem_erro'] = "ID de localização inválido ou não fornecido.";
}

// 3. Redirecionar de volta para a página de listagem
header("Location: ../localizacao.php");
exit;