<?php
session_start();

// Si el usuario NO inició sesión, mostrar login
if (!isset($_SESSION['usuario'])) {
    require_once __DIR__ . '/../auth/login_modal.php';
    exit;
}

// Si está logueado, incluir cabecera, menú y contenido
require_once __DIR__ . '/../includes/header.php'; // __DIR__ es la ruta del directorio actual
require_once __DIR__ . '/../includes/menu.php';

// Mostrar el dashboard (o podrías redirigir a otra vista si querés)
//require_once __DIR__ . '/../modules/dashboard/index.php';
echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['nombre']) . "</h1>";

require_once __DIR__ . '/../includes/footer.php';

