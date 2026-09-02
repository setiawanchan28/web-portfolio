<?php
/**
 * Application Core Configuration
 */

// Start session & output buffering if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

// Error reporting settings (Development mode)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setup
date_default_timezone_set('Asia/Jakarta');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');
define('DB_CHARSET', 'utf8mb4');

// Site URL Configuration (Support both portofolio and portfolio alias)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$basePath = preg_replace('#/(admin|modules|views|includes|config).*$#i', '', $scriptName);
$basePath = rtrim($basePath, '/') . '/';

define('BASE_URL', $protocol . $host . $basePath);
define('ADMIN_URL', BASE_URL . 'admin/');
define('UPLOAD_URL', BASE_URL . 'uploads/');

// File Upload Directories
define('ROOT_PATH', dirname(__DIR__) . '/');
define('UPLOAD_PATH', ROOT_PATH . 'uploads/');
