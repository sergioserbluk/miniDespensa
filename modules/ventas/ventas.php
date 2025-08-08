<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
if (!isset($_SESSION['usuario']) || !in_array($_SESSION['rol'], ['admin', 'vendedor'])) {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

require_once BASE_PATH . '/config/db.php';
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/menu.php';

// Obtener productos activos para el selector
$stmtProd = $pdo->query('SELECT id, nombre, precio_venta FROM productos WHERE estado = 1 ORDER BY nombre');
$productos = $stmtProd->fetchAll();
$mapProductos = [];
foreach ($productos as $p) {
    $mapProductos[$p['id']] = $p['nombre'];
}

$mensaje = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = trim($_POST['cliente']);
    $prodIds = $_POST['producto'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];
    $precios = $_POST['precio'] ?? [];

    $detallesVenta = [];

    if ($prodIds) {
        $total = 0;
        foreach ($prodIds as $idx => $pid) {
            $cant = isset($cantidades[$idx]) ? (float)$cantidades[$idx] : 0;
            $prec = isset($precios[$idx]) ? (float)$precios[$idx] : 0;
            $total += $cant * $prec;
            $detallesVenta[] = [
                'nombre'   => $mapProductos[$pid] ?? $pid,
                'cantidad' => $cant,
                'precio'   => $prec,
                'subtotal' => $cant * $prec
            ];
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
            $mostrarModal = true;
            $fechaVenta = date('d/m/Y H:i');
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
<a href="listado_ventas.php" class="btn btn-secondary mb-3">Listado de Ventas</a>
<?php if ($mensaje && empty($mostrarModal)): ?>
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
<?php if (!empty($mostrarModal)): ?>
<!-- Modal de factura -->
<div class="modal fade" id="facturaModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Factura</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="facturaContenido">
        <p><strong>Fecha:</strong> <?php echo $fechaVenta; ?></p>
        <p><strong>Cliente:</strong> <?php echo htmlspecialchars($cliente); ?></p>
        <table class="table">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>Precio</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($detallesVenta as $d): ?>
            <tr>
              <td><?php echo htmlspecialchars($d['nombre']); ?></td>
              <td><?php echo $d['cantidad']; ?></td>
              <td><?php echo number_format($d['precio'],2); ?></td>
              <td><?php echo number_format($d['subtotal'],2); ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="3" class="text-end fw-bold">Total</td>
              <td><?php echo number_format($total,2); ?></td>
            </tr>
          </tfoot>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="imprimirFactura()">Imprimir Factura</button>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var facturaModal = new bootstrap.Modal(document.getElementById('facturaModal'));
    facturaModal.show();
});
function imprimirFactura() {
    const contenido = document.getElementById('facturaContenido').innerHTML;
    const ventImp = window.open('', '', 'width=800,height=600');
    ventImp.document.write('<html><head><title>Factura</title>');
    ventImp.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">');
    ventImp.document.write('</head><body>');
    ventImp.document.write(contenido);
    ventImp.document.write('</body></html>');
    ventImp.document.close();
    ventImp.focus();
    ventImp.print();
    ventImp.close();
    window.location.href = 'index.php';
}
</script>
<?php endif; ?>
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

