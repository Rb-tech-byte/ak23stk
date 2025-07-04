<?php
// =============================================
// Database Configuration & Core Settings
// =============================================

// Database credentials
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_USER')) define('DB_USER', 'root');  // Dedicated database user (not root)
if (!defined('DB_PASS')) define('DB_PASS', '');  // Use a strong password
if (!defined('DB_NAME')) define('DB_NAME', 'ak23skdb');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// Application constants
define('SITE_URL', 'http://localhost/ak23stk');
define('MAX_DOWNLOADS', 5);
define('DOWNLOAD_EXPIRY_DAYS', 3);
define('PESAPAL_CALLBACK_URL', SITE_URL . '/payment_callback.php');

// Environment detection
define('IS_LOCAL', in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']));

// Error reporting configuration
ini_set('display_errors', IS_LOCAL ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Create PDO connection with proper error handling
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_PERSISTENT         => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    $pdo->exec("SET time_zone = '+03:00'");  // Tanzania Time (EAT)
    
} catch (PDOException $e) {
    // Secure error logging
    error_log('['.date('Y-m-d H:i:s').'] PDO Connection Error: ' . $e->getMessage());
    
    // User-friendly messages
    if (IS_LOCAL) {
        die('<div class="alert alert-danger"><strong>Database Error:</strong> ' . 
            htmlspecialchars($e->getMessage()) . '</div>');
    } else {
        die('<div class="alert alert-danger">Database connection error. Please try again later.</div>');
    }
}

// Download Token Generator
function generate_download_token() {
    return bin2hex(random_bytes(32));
}

// Close connection automatically at script end
register_shutdown_function(function() use (&$pdo) {
    $pdo = null;
});