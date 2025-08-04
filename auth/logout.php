<?php
session_start();
session_destroy();
require_once __DIR__ . '/../config/config.php';
header('Location: ' . PUBLIC_PATH . 'index.php');
exit;
