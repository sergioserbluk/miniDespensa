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

$error = '';

// Obtener categorias para el select
$stmtCat = $pdo->query('SELECT id, nombre FROM categorias ORDER BY nombre');
$categorias = $stmtCat->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo = trim($_POST['codigo_barra']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion']);
    $categoria = $_POST['id_categoria'] ?: null;
    $unidad = trim($_POST['unidad_medida']);
    $stock = $_POST['stock_actual'] !== '' ? (float)$_POST['stock_actual'] : 0;
    $costo = $_POST['precio_costo'] !== '' ? (float)$_POST['precio_costo'] : 0;
    $utilidad = $_POST['utilidad'] !== '' ? (float)$_POST['utilidad'] : 0;
    $venta = $_POST['precio_venta'] !== '' ? (float)$_POST['precio_venta'] : 0;

    // Calcular precio de venta si se proporcionan costo y margen de utilidad
    if ($costo > 0 && $utilidad > 0) {
        $venta = round($costo * (1 + $utilidad / 100), 2);
    }
    $estado = isset($_POST['estado']) ? 1 : 0;

    $imagenRel = '';
    if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = PUBLIC_PATH . '/img/productos';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileName = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $targetPath = $uploadDir . '/' . $fileName;
        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            $imagenRel = 'img/productos/' . $fileName;
        }
    }

    if ($nombre) {
        $stmt = $pdo->prepare('INSERT INTO productos (codigo_barra, nombre, descripcion, id_categoria, unidad_medida, stock_actual, precio_costo, utilidad, precio_venta, estado, imagen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$codigo, $nombre, $descripcion, $categoria, $unidad, $stock, $costo, $utilidad, $venta, $estado, $imagenRel]);
        header('Location: index.php');
        exit;
    }
    $error = 'El nombre del producto es obligatorio';
}
?>
<h2>Nuevo Producto</h2>
<?php if (!empty($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Código de Barra</label>
        <input type="text" name="codigo_barra" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control"></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Categoría</label>
        <select name="id_categoria" class="form-select">
            <option value="">-- Sin categoría --</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Unidad de Medida</label>
        <input type="text" name="unidad_medida" class="form-control" value="unidad">
    </div>
    <div class="mb-3">
        <label class="form-label">Stock Inicial</label>
        <input type="number" step="0.01" name="stock_actual" class="form-control" value="0">
    </div>
    <div class="mb-3">
        <label class="form-label">Precio Costo</label>
        <input type="number" step="0.01" name="precio_costo" class="form-control" value="0">
    </div>
    <div class="mb-3">
        <label class="form-label">Utilidad %</label>
        <input type="number" step="0.01" name="utilidad" class="form-control" value="0">
    </div>
    <div class="mb-3">
        <label class="form-label">Precio Venta</label>
        <input type="number" step="0.01" name="precio_venta" class="form-control" value="0">
    </div>
    <div class="mb-3">
        <label class="form-label">Imagen</label>
        <input type="file" name="imagen" class="form-control">
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="estado" id="estado" checked>
        <label class="form-check-label" for="estado">Activo</label>
    </div>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="index.php" class="btn btn-secondary">Cancelar</a>
</form>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const costoInput = document.querySelector('input[name="precio_costo"]');
    const utilidadInput = document.querySelector('input[name="utilidad"]');
    const ventaInput = document.querySelector('input[name="precio_venta"]');

    function calcularVenta() {
        const costo = parseFloat(costoInput.value);
        const utilidad = parseFloat(utilidadInput.value);
        if (!isNaN(costo) && !isNaN(utilidad)) {
            const venta = costo * (1 + utilidad / 100);
            ventaInput.value = venta.toFixed(2);
        }
    }

    costoInput.addEventListener('input', calcularVenta);
    utilidadInput.addEventListener('input', calcularVenta);
});
</script>
<?php
require_once INCLUDES_PATH . '/footer.php';
