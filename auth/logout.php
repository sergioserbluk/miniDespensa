<?php
session_start();
session_destroy();
header('Location: ' . BASE_URL . 'public/index.php');
exit;
