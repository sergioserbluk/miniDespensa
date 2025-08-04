<?php
// __DIR__ en este contexto es /minidespensa/config
// Normalizamos la ruta base sin la barra final
define('BASE_PATH', realpath(__DIR__ . '/..'));

// URL base del dominio y carpeta public para recursos web
define('BASE_HOST', 'http://sitio1.com');
define('BASE_URL', BASE_HOST . '/public/');

// Rutas internas del proyecto
define('INCLUDES_PATH', BASE_PATH . '/includes');
define('PUBLIC_PATH', BASE_PATH . '/public'); // se usa para archivos públicos
define('MODULES_PATH', BASE_PATH . '/modules'); // se usa para cargar módulos
define('AUTH_PATH', BASE_PATH . '/auth'); // módulos de autenticación

// Rutas adicionales para dependencias externas y certificados de AFIP
define('VENDOR_PATH', BASE_PATH . '/vendor');
define('AFIP_CERT_PATH', BASE_PATH . '/afip/cert');

