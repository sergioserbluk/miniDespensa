<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: /public/index.php');
    exit;
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/menu.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT id, usuario, nombre, rol, activo FROM usuarios WHERE id = ?');
$stmt->execute([$id]);
$usuario = $stmt->fetch();
if (!$usuario) {
    echo '<div class="alert alert-danger">Usuario no encontrado</div>';
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $rol = $_POST['rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    if ($nombre && $rol) {
        $stmt = $pdo->prepare('UPDATE usuarios SET nombre=?, rol=?, activo=? WHERE id=?');
        $stmt->execute([$nombre, $rol, $activo, $id]);
        header('Location: index.php');
        exit;
    }
    $error = 'Nombre y rol son obligatorios';
}
?>
<h2>Editar Usuario</h2>
<?php if (!empty($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input type="text" class="form-control" value="<?php echo htmlspecialchars($usuario['usuario']); ?>" disabled>
    </div>
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Rol</label>
        <select name="rol" class="form-select">
            <?php
            $roles = ['vendedor','repositor','compras','gerente','admin'];
            foreach ($roles as $r) {
                $selected = $usuario['rol'] === $r ? 'selected' : '';
                echo "<option value='$r' $selected>$r</option>";
            }
            ?>
        </select>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="activo" id="activo" <?php echo $usuario['activo'] ? 'checked' : ''; ?>>
        <label class="form-check-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php
require_once __DIR__ . '/../../includes/footer.php';

