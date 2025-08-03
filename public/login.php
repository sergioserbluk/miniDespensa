<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Si ya está logueado, redirigir al inicio
if (isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once AUTH_PATH . '/validar_login.php';
    exit;
}

// Mostrar formulario de login
require_once AUTH_PATH . '/login_modal.php';

