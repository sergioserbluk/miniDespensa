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

$desde = $_GET['desde'] ?? '';
$hasta = $_GET['hasta'] ?? '';
$cliente = trim($_GET['cliente'] ?? '');

$query = 'SELECT id, fecha, cliente_nombre, total FROM ventas WHERE 1=1';
$params = [];
if ($desde) {
    $query .= ' AND fecha >= ?';
    $params[] = $desde . ' 00:00:00';
}
if ($hasta) {
    $query .= ' AND fecha <= ?';
    $params[] = $hasta . ' 23:59:59';
}
if ($cliente !== '') {
    $query .= ' AND cliente_nombre LIKE ?';
    $params[] = '%' . $cliente . '%';
}
$query .= ' ORDER BY fecha DESC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$ventas = $stmt->fetchAll();
?>
<h2>Listado de Ventas</h2>
<form method="get" class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="desde" class="form-control" value="<?php echo htmlspecialchars($desde); ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="hasta" class="form-control" value="<?php echo htmlspecialchars($hasta); ?>">
    </div>
    <div class="col-md-4">
        <label class="form-label">Cliente</label>
        <input type="text" name="cliente" class="form-control" value="<?php echo htmlspecialchars($cliente); ?>">
    </div>
    <div class="col-md-2 align-self-end">
        <button type="submit" class="btn btn-primary w-100">Buscar</button>
    </div>
</form>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($ventas): ?>
            <?php foreach ($ventas as $v): ?>
            <tr>
                <td><?php echo $v['id']; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($v['fecha'])); ?></td>
                <td><?php echo htmlspecialchars($v['cliente_nombre']); ?></td>
                <td><?php echo number_format($v['total'], 2); ?></td>
            </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" class="text-center">No se encontraron ventas</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php
require_once INCLUDES_PATH . '/footer.php';
