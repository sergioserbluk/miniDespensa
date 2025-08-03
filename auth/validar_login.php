<?php
session_start();
require_once __DIR__ . '/../config/config.php';
require_once BASE_PATH . '/config/db_login.php';

$usuario = trim($_POST['usuario'] ?? '');
$clave = trim($_POST['clave'] ?? '');

if ($usuario === '' || $clave === '') {
    header('Location: ' . BASE_URL . 'login.php?error=1');
    exit;
}

try {
    $stmt = $pdoLogin->prepare("SELECT id, usuario, nombre, clave, rol FROM usuarios WHERE usuario = ? AND activo = 1 LIMIT 1");
    $stmt->execute([$usuario]);
    $user = $stmt->fetch();

    if ($user && password_verify($clave, $user['clave'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['nombre'] = $user['nombre'];
        $_SESSION['rol'] = $user['rol'];
        header('Location: ' . BASE_URL . 'index.php');
    } else {
        header('Location: ' . BASE_URL . 'login.php?error=1');
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
