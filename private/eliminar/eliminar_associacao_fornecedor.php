<?php
session_start();

// Verifica se os parâmetros obrigatórios foram passados
if (!isset($_GET['equipamento_id']) || !isset($_GET['fornecedor_id']) || !isset($_GET['tipo'])) {
    header("Location: ../fornecedores.php");
    exit;
}

$equipamento_id = intval($_GET['equipamento_id']);
$fornecedor_id = intval($_GET['fornecedor_id']);
$tipo_fornecedor = $_GET['tipo']; // String correspondente ao ENUM

// Ligação à Base de Dados
$host = "localhost"; $user = "root"; $pass = ""; $dbname = "medtrack_db";
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// Query para eliminar estritamente a associação na tabela intermédia
$sql = "DELETE FROM equipamento_fornecedor 
        WHERE equipamento_id = ? AND fornecedor_id = ? AND tipo_fornecedor = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "iis", $equipamento_id, $fornecedor_id, $tipo_fornecedor);

if (mysqli_stmt_execute($stmt)) {
    // Sucesso: Redireciona de volta para a ficha técnica com uma flag de sucesso
    header("Location: ../fornecedores.php?id=" . $equipamento_id . "&msg=assoc_removida");
} else {
    // Erro no SQL
    header("Location: ../fornecedores.php?id=" . $equipamento_id . "&msg=erro");
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
exit;

?>