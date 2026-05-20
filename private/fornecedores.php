<?php

if (session_status() === PHP_SESSION_NONE) {
    // Configurações de cookies de sessão para mitigar ataques XSS e Session Hijacking
    session_start([
        'cookie_lifetime' => 0,
        'cookie_path' => '/',
        'cookie_secure' => false, // Altere para true se estiver a usar HTTPS
        'cookie_httponly' => true,  // Impede o JavaScript de aceder ao ID da sessão
        'cookie_samesite' => 'Strict'
    ]);
}

// Verifica se a variável de sessão está ativa
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in'])) {
    // Não autenticado -> Redireciona para o formulário de login (ajuste o caminho se necessário)
    header('Location: ../public/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
</body>
</html>