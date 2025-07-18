<div class="mb-4">
    <ul class="nav nav-pills">
        <?php if (in_array($_SESSION['rol'], ['admin', 'gerente'])): ?>
            <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL . 'modules/dashboard/'; ?>">Dashboard</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin', 'compras'])): ?>
            <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL . 'modules/compras/'; ?>">Compras</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin', 'vendedor'])): ?>
            <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL . 'modules/ventas/'; ?>">Facturación</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin'])): ?>
            <li class="nav-item">
            <a class="nav-link" href="<?php echo BASE_URL . 'modules/usuarios/index.php'; ?>">Usuarios</a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
    <a class="nav-link text-danger" href="<?php echo BASE_URL . 'auth/logout.php'; ?>">Salir</a>
        </li>
    </ul>
</div>
