<?php
$host = "localhost";
$user = "root";
$pass = ""; // Senha do Laragon é vazia por padrão
$dbname = "medtrack_db";

// Criar a ligação
$conn = mysqli_connect($host, $user, $pass, $dbname);

// Verificar se houve erro
if (!$conn) {
    die("Erro na ligação: " . mysqli_connect_error());
} else {
    // Apaga esta linha depois de testares, serve só para termos a certeza!
    echo "";
}
?>