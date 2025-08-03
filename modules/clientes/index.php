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

// Obtener clientes
$stmt = $pdo->query('SELECT id, nombre, tipo_documento, numero_documento, domicilio, email, telefono, activo FROM clientes ORDER BY id');
$clientes = $stmt->fetchAll();
$tipos = [80 => 'CUIT', 86 => 'CUIL', 96 => 'DNI', 99 => 'CF'];
?>
<h2>Clientes</h2>
<a href="crear.php" class="btn btn-primary mb-3">Nuevo Cliente</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo Doc</th>
            <th>Nro Doc</th>
            <th>Domicilio</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Activo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $c): ?>
            <tr>
                <td><?php echo $c['id']; ?></td>
                <td><?php echo htmlspecialchars($c['nombre']); ?></td>
                <td><?php echo $tipos[$c['tipo_documento']] ?? $c['tipo_documento']; ?></td>
                <td><?php echo htmlspecialchars($c['numero_documento']); ?></td>
                <td><?php echo htmlspecialchars($c['domicilio']); ?></td>
                <td><?php echo htmlspecialchars($c['email']); ?></td>
                <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                <td><?php echo $c['activo'] ? 'Sí' : 'No'; ?></td>
                <td>
                    <a class="btn btn-sm btn-secondary" href="editar.php?id=<?php echo $c['id']; ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
require_once INCLUDES_PATH . '/footer.php';
