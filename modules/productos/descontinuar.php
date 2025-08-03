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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT id, nombre, estado FROM productos WHERE id = ?');
$stmt->execute([$id]);
$producto = $stmt->fetch();
if (!$producto) {
    echo '<div class="alert alert-danger">Producto no encontrado</div>';
    require_once INCLUDES_PATH . '/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevoEstado = $producto['estado'] ? 0 : 1; // descontinuar o reactivar
    $stmt = $pdo->prepare('UPDATE productos SET estado = ? WHERE id = ?');
    $stmt->execute([$nuevoEstado, $id]);
    header('Location: index.php');
    exit;
}
?>
<h2><?php echo $producto['estado'] ? 'Descontinuar' : 'Reactivar'; ?> Producto</h2>
<!-- Botón que dispara el modal -->
<button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#confirmModal">
    <?php echo $producto['estado'] ? 'Descontinuar' : 'Reactivar'; ?>
</button>
<a href="index.php" class="btn btn-secondary">Cancelar</a>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmModalLabel">Confirmar</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ¿Seguro que desea <?php echo $producto['estado'] ? 'descontinuar' : 'reactivar'; ?> el producto "<?php echo htmlspecialchars($producto['nombre']); ?>"?
      </div>
      <div class="modal-footer">
        <form method="post">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Aceptar</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php
require_once INCLUDES_PATH . '/footer.php';
