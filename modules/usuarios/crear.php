<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

require_once BASE_PATH . '/config/db.php';
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/menu.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario']);
    $nombre = trim($_POST['nombre']);
    $rol = $_POST['rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    $clave = trim($_POST['clave']);

    if ($usuario && $nombre && $rol && $clave) {
        $hash = password_hash($clave, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('INSERT INTO usuarios (usuario, clave, nombre, rol, activo) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$usuario, $hash, $nombre, $rol, $activo]);
        header('Location: index.php');
        exit;
    }
    $error = 'Todos los campos son obligatorios';
}
?>
<h2>Nuevo Usuario</h2>
<?php if (!empty($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Usuario</label>
        <input type="text" name="usuario" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Rol</label>
        <select name="rol" class="form-select">
            <option value="vendedor">vendedor</option>
            <option value="repositor">repositor</option>
            <option value="compras">compras</option>
            <option value="gerente">gerente</option>
            <option value="admin">admin</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Contraseña</label>
        <input type="password" name="clave" class="form-control" required>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="activo" id="activo" checked>
        <label class="form-check-label" for="activo">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
<?php
require_once INCLUDES_PATH . '/footer.php';

