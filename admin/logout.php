<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/helper.php';

session_destroy();
header("Location: " . ADMIN_URL . "login.php");
exit;
