<?php
session_start();
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] !== 'admin') {
    header('Location: /public/index.php');
    exit;
}

require_once __DIR__ . '/../../config/config.php';
require_once BASE_PATH . '/config/db.php';
require_once INCLUDES_PATH . '/header.php';
require_once INCLUDES_PATH . '/menu.php';

// Obtener usuarios
$stmt = $pdo->query('SELECT id, usuario, nombre, rol, activo FROM usuarios ORDER BY id');
$usuarios = $stmt->fetchAll();
?>
<h2>Gestión de Usuarios</h2>
<a href="crear.php" class="btn btn-primary mb-3">Nuevo Usuario</a>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Usuario</th>
            <th>Nombre</th>
            <th>Rol</th>
            <th>Activo</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['usuario']); ?></td>
                <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                <td><?php echo $u['rol']; ?></td>
                <td><?php echo $u['activo'] ? 'Sí' : 'No'; ?></td>
                <td>
                    <a class="btn btn-sm btn-secondary" href="editar.php?id=<?php echo $u['id']; ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
require_once INCLUDES_PATH . '/footer.php';

