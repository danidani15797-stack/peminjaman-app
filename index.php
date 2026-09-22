<?php
require_once __DIR__ . '/includes/config.php';
header('Location: ' . (!empty($_SESSION['login']) ? 'dashboard.php' : 'login.php'));
exit;
