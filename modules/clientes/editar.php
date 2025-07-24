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
    $tipoDoc = isset($_POST['tipo_documento']) ? (int)$_POST['tipo_documento'] : 99;
    $numeroDoc = trim($_POST['numero_documento']);
    $domicilio = trim($_POST['domicilio']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $activo = isset($_POST['activo']) ? 1 : 0;
    if ($nombre) {
        $stmt = $pdo->prepare('UPDATE clientes SET nombre=?, tipo_documento=?, numero_documento=?, domicilio=?, email=?, telefono=?, activo=? WHERE id=?');
        $stmt->execute([
            $nombre,
            $tipoDoc,
            $numeroDoc !== '' ? $numeroDoc : null,
            $domicilio,
            $email,
            $telefono,
            $activo,
            $id
        ]);
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
        <label class="form-label">Tipo Documento</label>
        <select name="tipo_documento" class="form-select">
            <option value="80" <?php echo $cliente['tipo_documento']==80?'selected':''; ?>>CUIT</option>
            <option value="86" <?php echo $cliente['tipo_documento']==86?'selected':''; ?>>CUIL</option>
            <option value="96" <?php echo $cliente['tipo_documento']==96?'selected':''; ?>>DNI</option>
            <option value="99" <?php echo $cliente['tipo_documento']==99?'selected':''; ?>>Consumidor Final</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Número Documento</label>
        <input type="text" name="numero_documento" class="form-control" value="<?php echo htmlspecialchars($cliente['numero_documento']); ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Domicilio</label>
        <input type="text" name="domicilio" class="form-control" value="<?php echo htmlspecialchars($cliente['domicilio']); ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($cliente['email']); ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="<?php echo htmlspecialchars($cliente['telefono']); ?>">
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
