<?php
// config/db.php

$host = 'localhost';
$db   = 'apoio_hospitalar';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Garante que a sanitização é feita nativamente pelo MySQL
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Em produção, nunca mostre o $e->getMessage() diretamente ao utilizador
     die("Erro na ligação à base de dados.");
}