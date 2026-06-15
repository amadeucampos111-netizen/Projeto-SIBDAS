<?php


$host = "vsgate-s1.dei.isep.ipp.pt";
$port = 10464;
    $user = "1240896";
    $pass = "campos_896";
    $dbname = "db1240896";

    $conn = mysqli_connect($host, $user, $pass, $dbname, $port);

if (!$conn) {
    die("Falha na ligação: " . mysqli_connect_error());
}

// Procura todos os textos da base de dados
$stmt = mysqli_query($conn, 'SELECT chave, conteudo FROM textos_interface');
$textos = [];

while ($row = mysqli_fetch_assoc($stmt)) {
    $textos[$row['chave']] = $row['conteudo'];
}

// Função auxiliar para evitar erros caso a chave não exista na BD
function exibir_texto($chave, $texto_padrao) {
    global $textos;
    return isset($textos[$chave]) ? htmlspecialchars($textos[$chave], ENT_QUOTES, 'UTF-8') : $texto_padrao;
}
?>