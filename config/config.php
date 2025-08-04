<?php
// __DIR__ en este contexto es /minidespensa/config
define('BASE_PATH',__DIR__ . '/../'); // sube a /minidespensa
define('BASE_URL', 'http://sitio1.com/public/');

define('INCLUDES_PATH', BASE_PATH . '/includes'); //se 
define('PUBLIC_PATH', BASE_PATH . '/public');//se usa para redirecciones
define('MODULES_PATH', BASE_PATH . '/modules');//se usa para cargar modulos
define('AUTH_PATH', BASE_PATH . '/auth');//se usa para cargar modulos de autenticacion

// Rutas adicionales para dependencias externas y certificados de AFIP
define('VENDOR_PATH', BASE_PATH . '/vendor');
define('AFIP_CERT_PATH', BASE_PATH . '/afip/cert');

