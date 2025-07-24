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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM clientes WHERE id = ?');
$stmt->execute([$id]);
$cliente = $stmt->fetch();
if (!$cliente) {
    echo '<div class="alert alert-danger">Cliente no encontrado</div>';
    require_once INCLUDES_PATH . '/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    if ($nombre) {
        $stmt = $pdo->prepare('UPDATE clientes SET nombre=?, telefono=?, direccion=?, activo=? WHERE id=?');
        $stmt->execute([$nombre, $telefono, $direccion, $activo, $id]);
        header('Location: index.php');
        exit;
    }
    $error = 'El nombre es obligatorio';
}
?>
<h2>Editar Cliente</h2>
<?php if (!empty($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($cliente['nombre']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Dirección</label>
        <input type="text" name="direccion" class="form-control" value="<?php echo htmlspecialchars($cliente['direccion']); ?>">
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="activo" id="activo" <?php echo $cliente['activo'] ? 'checked' : ''; ?>>
        <label class="form-check-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php
require_once INCLUDES_PATH . '/footer.php';
