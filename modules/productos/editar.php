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

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare('SELECT * FROM productos WHERE id = ?');
$stmt->execute([$id]);
$producto = $stmt->fetch();
if (!$producto) {
    echo '<div class="alert alert-danger">Producto no encontrado</div>';
    require_once INCLUDES_PATH . '/footer.php';
    exit;
}

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

    if ($costo > 0 && $utilidad > 0) {
        $venta = round($costo * (1 + $utilidad / 100), 2);
    }

    $estado = isset($_POST['estado']) ? 1 : 0;
    $imagenRel = $producto['imagen'];

    if (!empty($_FILES['imagen']['name']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        if (!empty($producto['imagen'])) {
            $origOld = PUBLIC_PATH . '/' . $producto['imagen'];
            $thumbOld = str_replace('originales', 'thumbs', $origOld);
            if (file_exists($origOld)) {
                unlink($origOld);
            }
            if (file_exists($thumbOld)) {
                unlink($thumbOld);
            }
        }

        $origDir  = PUBLIC_PATH . '/uploads/productos/originales';
        $thumbDir = PUBLIC_PATH . '/uploads/productos/thumbs';
        if (!is_dir($origDir)) {
            mkdir($origDir, 0777, true);
        }
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0777, true);
        }

        $fileName   = uniqid() . '_' . basename($_FILES['imagen']['name']);
        $targetPath = $origDir . '/' . $fileName;

        if (move_uploaded_file($_FILES['imagen']['tmp_name'], $targetPath)) {
            $thumbPath = $thumbDir . '/' . $fileName;
            [$width, $height, $type] = getimagesize($targetPath);
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $src = imagecreatefromjpeg($targetPath);
                    break;
                case IMAGETYPE_PNG:
                    $src = imagecreatefrompng($targetPath);
                    break;
                case IMAGETYPE_GIF:
                    $src = imagecreatefromgif($targetPath);
                    break;
                default:
                    $src = null;
            }
            if ($src) {
                $newWidth  = 150;
                $ratio     = $height / $width;
                $newHeight = (int) round($newWidth * $ratio);
                $thumbImg  = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($thumbImg, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                switch ($type) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($thumbImg, $thumbPath, 90);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($thumbImg, $thumbPath);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($thumbImg, $thumbPath);
                        break;
                }
                imagedestroy($thumbImg);
                imagedestroy($src);
            }
            $imagenRel = 'uploads/productos/originales/' . $fileName;
        }
    }

    if ($nombre) {
        $stmt = $pdo->prepare('UPDATE productos SET codigo_barra=?, nombre=?, descripcion=?, id_categoria=?, unidad_medida=?, stock_actual=?, precio_costo=?, utilidad=?, precio_venta=?, estado=?, imagen=? WHERE id=?');
        $stmt->execute([$codigo, $nombre, $descripcion, $categoria, $unidad, $stock, $costo, $utilidad, $venta, $estado, $imagenRel, $id]);
        header('Location: index.php');
        exit;
    }
    $error = 'El nombre del producto es obligatorio';
}
?>
<h2>Editar Producto</h2>
<?php if (!empty($error)) echo '<div class="alert alert-danger">'.$error.'</div>'; ?>
<form method="post" enctype="multipart/form-data">
    <div class="mb-3">
        <label class="form-label">Código de Barra</label>
        <input type="text" name="codigo_barra" class="form-control" value="<?php echo htmlspecialchars($producto['codigo_barra']); ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Descripción</label>
        <textarea name="descripcion" class="form-control"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
    </div>
    <div class="mb-3">
        <label class="form-label">Categoría</label>
        <select name="id_categoria" class="form-select">
            <option value="">-- Sin categoría --</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?php echo $c['id']; ?>" <?php echo $producto['id_categoria'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['nombre']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Unidad de Medida</label>
        <input type="text" name="unidad_medida" class="form-control" value="<?php echo htmlspecialchars($producto['unidad_medida']); ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Stock Actual</label>
        <input type="number" step="0.01" name="stock_actual" class="form-control" value="<?php echo $producto['stock_actual']; ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Precio Costo</label>
        <input type="number" step="0.01" name="precio_costo" class="form-control" value="<?php echo $producto['precio_costo']; ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Utilidad %</label>
        <input type="number" step="0.01" name="utilidad" class="form-control" value="<?php echo $producto['utilidad']; ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Precio Venta</label>
        <input type="number" step="0.01" name="precio_venta" class="form-control" value="<?php echo $producto['precio_venta']; ?>">
    </div>
    <div class="mb-3">
        <label class="form-label">Imagen</label>
        <input type="file" name="imagen" class="form-control">
        <?php
        if (!empty($producto['imagen'])) {
            $thumbRel  = str_replace('originales', 'thumbs', $producto['imagen']);
            $thumbPath = PUBLIC_PATH . '/' . $thumbRel;
            if (file_exists($thumbPath)) {
                echo '<img src="' . BASE_URL . $thumbRel . '" width="50" class="img-thumbnail mt-2">';
            }
        }
        ?>
    </div>
    <div class="form-check mb-3">
        <input class="form-check-input" type="checkbox" name="estado" id="estado" <?php echo $producto['estado'] ? 'checked' : ''; ?>>
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
