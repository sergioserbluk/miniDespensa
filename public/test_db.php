<?php
$conexion = new mysqli("localhost", "miniuser", "miniuser123", "minidespensa");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
// si la conexion es exitosa, muestro un mensaje con el nombre de la base de datos

echo "¡Conexión exitosa a la base de datos!" . $conexion->host_info . "\n" . "Base de datos: minidespensa" . "\n";
?>

