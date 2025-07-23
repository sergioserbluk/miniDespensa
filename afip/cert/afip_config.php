<?php
// afip/afip_config.php
// Configuración de AFIP para el proyecto MiniDespensa
// usando la librería oficial de AFIP para PHP
// y el usuario de prueba 20111111112

require_once __DIR__ . '/../vendor/autoload.php';

$afip = new Afip([
    'CUIT' => 20111111112,
    'production' => false,
    'cert' => __DIR__ . '/cert/certificate.crt',
    'key'  => __DIR__ . '/cert/private.key',
    'ta_folder' => __DIR__ . '/tmp' // opcional para almacenar el ticket de acceso
]);
