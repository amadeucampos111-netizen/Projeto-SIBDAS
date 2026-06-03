<?php
session_start();

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn = mysqli_connect("localhost", "root", "", "medtrack_db");

    $sql = "DELETE FROM garantias_contratos WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['msg_sucesso'] = "O registo de garantia/contrato foi removido do sistema.";
        } else {
            $_SESSION['msg_erro'] = "Erro técnico ao tentar apagar: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
} else {
    $_SESSION['msg_erro'] = "Pedido inválido.";
}

header("Location: ../lista_garantias.php");
exit;