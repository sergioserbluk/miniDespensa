<?php
// config/db_login.php

$host = 'localhost';
$dbname = 'minidespensa';
$user = 'miniuser';
$pass = 'miniuser123';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdoLogin = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("❌ Error de conexión al sistema de login: " . $e->getMessage());
}
