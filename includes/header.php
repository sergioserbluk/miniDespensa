<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>MiniDespensa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
    <a class="navbar-brand" href="<?php echo BASE_URL . 'index.php'; ?>">MiniDespensa</a>
    <div class="ms-auto text-white">
        <?php echo $_SESSION['nombre']; ?> (<?php echo $_SESSION['rol']; ?>)
    </div>
</nav>
<div class="container my-4">

