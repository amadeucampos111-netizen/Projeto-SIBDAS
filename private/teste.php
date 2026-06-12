<?php
$senha = 'PasseSegura123!';
$novo_hash = password_hash($senha, PASSWORD_DEFAULT);

echo "<h3>Gerador de Credenciais</h3>";
echo "Palavra-passe: <strong>" . htmlspecialchars($senha) . "</strong><br>";
echo "Novo Código Hash (Copia isto tudo):<br>";
echo "<textarea rows='3' style='width:100%; font-family:monospace; margin-top:10px;' readonly>" . $novo_hash . "</textarea>";
?>