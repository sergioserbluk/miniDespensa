<?php
session_start();
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['rol'], ['admin', 'vendedor'])) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/menu.php';
?>
<h2>Ventas</h2>
<a href="ventas.php" class="btn btn-primary mb-3">Nueva Venta</a>
<?php
require_once INCLUDES_PATH . '/footer.php';
