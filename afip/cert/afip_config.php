<?php
// afip/afip_config.php
// Configuración de AFIP para el proyecto MiniDespensa
// usando la librería oficial de AFIP para PHP
// y el usuario de prueba 20111111112

require_once __DIR__ . '/../../config/config.php';
require_once VENDOR_PATH . '/autoload.php';

$afip = new Afip([
    'CUIT' => 20111111112,
    'production' => false,
    'cert' => AFIP_CERT_PATH . '/certificate.crt',
    'key'  => AFIP_CERT_PATH . '/private.key',
    'ta_folder' => AFIP_CERT_PATH . '/tmp' // opcional para almacenar el ticket de acceso
]);
