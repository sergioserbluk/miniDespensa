<div class="mb-4">
    <ul class="nav nav-pills">
        <?php if (in_array($_SESSION['rol'], ['admin', 'gerente'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'dashboard/'; ?>">Dashboard</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin', 'compras'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'compras/'; ?>">Compras</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin', 'vendedor'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'ventas/'; ?>">Facturación</a>
            </li>
        <?php endif; ?>

        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'productos/'; ?>">Productos</a>
            </li>
        <?php endif; ?>

        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'clientes/'; ?>">Clientes</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo BASE_URL . 'usuarios/'; ?>">Usuarios</a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link text-danger" href="<?php echo AUTH_URL . '/logout.php'; ?>">Salir</a>
        </li>
    </ul>
</div>

