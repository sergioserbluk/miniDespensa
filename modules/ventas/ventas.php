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

// Obtener productos activos para el selector
$stmtProd = $pdo->query('SELECT id, nombre, precio_venta FROM productos WHERE estado = 1 ORDER BY nombre');
$productos = $stmtProd->fetchAll();

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST['cliente']);
    $prodIds = $_POST['producto'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];
    $precios = $_POST['precio'] ?? [];

    if ($prodIds) {
        $total = 0;
        foreach ($prodIds as $idx => $pid) {
            $cant = isset($cantidades[$idx]) ? (float)$cantidades[$idx] : 0;
            $prec = isset($precios[$idx]) ? (float)$precios[$idx] : 0;
            $total += $cant * $prec;
        }

        try {
            $pdo->beginTransaction();
            $stmtVenta = $pdo->prepare('INSERT INTO ventas (fecha, tipo, total, usuario_id, cliente_nombre) VALUES (NOW(), ?, ?, ?, ?)');
            $tipo = 'B';
            $stmtVenta->execute([$tipo, $total, $_SESSION['id'], $cliente]);
            $ventaId = $pdo->lastInsertId();
            $stmtDet = $pdo->prepare('INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)');
            foreach ($prodIds as $idx => $pid) {
                $cant = isset($cantidades[$idx]) ? (float)$cantidades[$idx] : 0;
                $prec = isset($precios[$idx]) ? (float)$precios[$idx] : 0;
                if ($pid && $cant > 0) {
                    $stmtDet->execute([$ventaId, $pid, $cant, $prec]);
                }
            }
            $pdo->commit();
            $mensaje = 'Venta registrada correctamente';
        } catch (Exception $e) {
            $pdo->rollBack();
            $mensaje = 'Error al registrar la venta: ' . $e->getMessage();
        }
    } else {
        $mensaje = 'Debe agregar al menos un producto';
    }
}
?>
<h2>Nueva Venta</h2>
<?php if ($mensaje): ?>
<div class="alert alert-info"><?php echo htmlspecialchars($mensaje); ?></div>
<?php endif; ?>
<form method="post" id="formVenta">
    <div class="mb-3">
        <label class="form-label">Cliente</label>
        <select name="cliente" class="form-select">
            <option value="Consumidor Final">Consumidor Final</option>
        </select>
    </div>
    <table class="table" id="tablaProductos">
        <thead>
            <tr>
                <th>Producto</th>
                <th style="width:120px;">Cantidad</th>
                <th style="width:120px;">Precio</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-end fw-bold">Total</td>
                <td id="totalVenta">0.00</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    <button type="button" class="btn btn-secondary" id="agregarFila">Agregar Producto</button>
    <button type="submit" class="btn btn-primary">Confirmar Venta</button>
</form>
<script>
const productos = <?php echo json_encode($productos); ?>;
function crearFila() {
    const tbody = document.querySelector('#tablaProductos tbody');
    const tr = document.createElement('tr');

    const tdProd = document.createElement('td');
    const select = document.createElement('select');
    select.name = 'producto[]';
    select.classList.add('form-select');
    productos.forEach(p => {
        const opt = document.createElement('option');
        opt.value = p.id;
        opt.textContent = p.nombre;
        opt.dataset.precio = p.precio_venta;
        select.appendChild(opt);
    });
    tdProd.appendChild(select);
    tr.appendChild(tdProd);

    const tdCant = document.createElement('td');
    const inputCant = document.createElement('input');
    inputCant.type = 'number';
    inputCant.step = '0.01';
    inputCant.name = 'cantidad[]';
    inputCant.classList.add('form-control');
    inputCant.value = 1;
    tdCant.appendChild(inputCant);
    tr.appendChild(tdCant);

    const tdPrecio = document.createElement('td');
    const inputPrec = document.createElement('input');
    inputPrec.type = 'number';
    inputPrec.step = '0.01';
    inputPrec.name = 'precio[]';
    inputPrec.classList.add('form-control');
    inputPrec.value = select.options[select.selectedIndex].dataset.precio;
    tdPrecio.appendChild(inputPrec);
    tr.appendChild(tdPrecio);

    const tdSub = document.createElement('td');
    tdSub.classList.add('subtotal');
    tdSub.textContent = (inputCant.value * inputPrec.value).toFixed(2);
    tr.appendChild(tdSub);

    const tdAcc = document.createElement('td');
    const btnDel = document.createElement('button');
    btnDel.type = 'button';
    btnDel.className = 'btn btn-sm btn-danger';
    btnDel.textContent = 'Quitar';
    btnDel.addEventListener('click', () => {
        tr.remove();
        calcularTotal();
    });
    tdAcc.appendChild(btnDel);
    tr.appendChild(tdAcc);

    select.addEventListener('change', () => {
        inputPrec.value = select.options[select.selectedIndex].dataset.precio;
        calcularSubtotal(tr);
    });
    inputCant.addEventListener('input', () => calcularSubtotal(tr));
    inputPrec.addEventListener('input', () => calcularSubtotal(tr));

    tbody.appendChild(tr);
    calcularTotal();
}
function calcularSubtotal(tr) {
    const cant = parseFloat(tr.querySelector('input[name="cantidad[]"]').value) || 0;
    const precio = parseFloat(tr.querySelector('input[name="precio[]"]').value) || 0;
    const sub = cant * precio;
    tr.querySelector('.subtotal').textContent = sub.toFixed(2);
    calcularTotal();
}
function calcularTotal() {
    let total = 0;
    document.querySelectorAll('#tablaProductos tbody tr').forEach(tr => {
        const sub = parseFloat(tr.querySelector('.subtotal').textContent) || 0;
        total += sub;
    });
    document.getElementById('totalVenta').textContent = total.toFixed(2);
}
document.getElementById('agregarFila').addEventListener('click', crearFila);
// crear primera fila por defecto
crearFila();
</script>
<?php
require_once INCLUDES_PATH . '/footer.php';

