<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/menu.php';

// Obtener productos disponibles
$stmt = $pdo->query('SELECT id, nombre, precio, stock FROM productos WHERE disponible = 1 ORDER BY id');
$productos = $stmt->fetchAll();
?>
<h2>Listado de Productos</h2>
<a href="crear.php" class="btn btn-primary mb-3">Nuevo Producto</a>
<?php if (count($productos) > 0): ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td><?php echo number_format($p['precio'], 2); ?></td>
                <td><?php echo $p['stock']; ?></td>
                <td>
                    <a class="btn btn-sm btn-secondary" href="editar.php?id=<?php echo $p['id']; ?>">Editar</a>
                    <a class="btn btn-sm btn-warning" href="descontinuar.php?id=<?php echo $p['id']; ?>">Descontinuar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else: ?>
<div class="alert alert-info">No hay productos disponibles.</div>
<?php endif; ?>
<?php
require_once INCLUDES_PATH . '/footer.php';

