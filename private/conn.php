<?php
$host = "vsgate-s1.dei.isep.ipp.pt";
$port   = 10464; 
$user = "1240896";
$pass = "campos_896"; 
$dbname = "db1240896";

// Criar a ligação
$conn = mysqli_connect($host, $user, $pass, $dbname, $port);

// Verificar se houve erro
if (!$conn) {
    die("Erro na ligação: " . mysqli_connect_error());
} else {
    // Apaga esta linha depois de testares, serve só para termos a certeza!
    echo "";
}
?>