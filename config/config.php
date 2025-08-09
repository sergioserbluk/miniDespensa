<?php
// Ruta absoluta al directorio raíz del proyecto
define('BASE_PATH', realpath(__DIR__ . '/..'));

// URL base del dominio (DocumentRoot apunta a /public)
define('BASE_HOST', 'http://sitio1.com/');
define('BASE_URL', BASE_HOST); // No agregues /public

// Rutas internas para inclusión (NO usar en href)
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('MODULES_PATH', BASE_PATH . '/modules');
define('AUTH_PATH', BASE_PATH . '/auth');
define('PUBLIC_PATH', BASE_PATH . '/public');

// Rutas web para los archivos públicos (usar en href)
define('INCLUDES_URL', BASE_URL . 'includes'); // solo si /public/includes existe
define('AUTH_URL', BASE_URL . 'auth');         // solo si /public/auth existe

// Rutas adicionales para dependencias externas y certificados de AFIP
define('VENDOR_PATH', BASE_PATH . '/vendor');
define('AFIP_CERT_PATH', BASE_PATH . '/afip/cert');
?>
