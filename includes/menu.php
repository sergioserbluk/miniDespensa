<div class="mb-4">
    <ul class="nav nav-pills">
        <?php if (in_array($_SESSION['rol'], ['admin', 'gerente'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="/modules/dashboard/">Dashboard</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin', 'compras'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="../modules/compras/">Compras</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin', 'vendedor'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="../modules/ventas/">Facturación</a>
            </li>
        <?php endif; ?>

        <?php if (in_array($_SESSION['rol'], ['admin'])): ?>
            <li class="nav-item">
                <a class="nav-link" href="../modules/usuarios/index.php">Usuarios</a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link text-danger" href="../auth/logout.php">Salir</a>
        </li>
    </ul>
</div>
