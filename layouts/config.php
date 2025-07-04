<?php
// Database configuration
require_once __DIR__ . '/../database/db_config.php';

// Global configuration
require_once __DIR__ . '/../inc/_global/config.php';

// Set timezone
date_default_timezone_set('Africa/Nairobi');

// Error reporting - show errors in development, hide in production
if (IS_LOCAL) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Session configuration must be set before session_start()
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    session_start();
}

// Database connection
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Database connection failed: ' . $e->getMessage());
    if (IS_LOCAL) {
        die('Database connection failed: ' . $e->getMessage());
    } else {
        die('Database connection error. Please try again later.');
    }
}

// Define constants
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'AK23StudioKits');
}

// CSRF Protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Helper function to check if user is logged in
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

// Helper function to check if user is admin
function is_admin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

// Redirect function
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

// Sanitize output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
