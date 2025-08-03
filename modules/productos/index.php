<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/menu.php';

// Obtener productos disponibles
// La estructura de la base define precio_venta, stock_actual y un campo
// "estado" que indica si el producto está habilitado (1) o no.
$stmt = $pdo->query('SELECT id, nombre, precio_venta, stock_actual, imagen FROM productos WHERE estado = 1 ORDER BY id');
$productos = $stmt->fetchAll();
?>
<h2>Listado de Productos</h2>
<a href="crear.php" class="btn btn-primary mb-3">Nuevo Producto</a>
<?php if (count($productos) > 0): ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($productos as $p): ?>
            <tr>
                <td>
                    <?php
                    if (!empty($p['imagen'])) {
                        $thumbRel  = str_replace('originales', 'thumbs', $p['imagen']);
                        $thumbPath = PUBLIC_PATH . '/' . $thumbRel;
                        if (file_exists($thumbPath)) {
                            echo '<img src="' . BASE_URL . $thumbRel . '" width="50" class="img-thumbnail">';
                        }
                    }
                    ?>
                </td>
                <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                <td><?php echo number_format($p['precio_venta'], 2); ?></td>
                <td><?php echo $p['stock_actual']; ?></td>
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

