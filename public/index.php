<?php
session_start();
require_once __DIR__ . '/../config/config.php';

// Si el usuario NO inició sesión, mostrar login
if (!isset($_SESSION['usuario'])) {
    require_once AUTH_PATH . '/login_modal.php';
    exit;
}

// Si está logueado, incluir cabecera, menú y contenido
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/menu.php';

// Mostrar el dashboard (o podrías redirigir a otra vista si querés)
//require_once __DIR__ . '/../modules/dashboard/index.php';
echo "<h1>Bienvenido, " . htmlspecialchars($_SESSION['nombre']) . "</h1>";

require_once INCLUDES_PATH . '/footer.php';

