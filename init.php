<?php
/**
 * Initialize Application
 * 
 * This file initializes the application environment and sets up global settings
 */

// Prevent direct access
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Define root paths if not already defined
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . DS);
}

if (!defined('INCLUDES_PATH')) {
    define('INCLUDES_PATH', ROOT_PATH . 'includes' . DS);
}

try {
    // Prevent multiple initializations
    if (defined('INIT_LOADED')) {
        return;
    }
    
    define('INIT_LOADED', true);
    
    // Load configuration
    if (file_exists(__DIR__ . '/inc/_global/config.php')) {
        require_once __DIR__ . '/inc/_global/config.php';
    } else {
        throw new Exception('Configuration file not found');
    }
    
    // Initialize session management
    if (file_exists(__DIR__ . '/inc/_global/session.php')) {
        require_once __DIR__ . '/inc/_global/session.php';
    } else {
        throw new Exception('Session file not found');
    }
    
    // Define base URL and paths if not already defined by config
    if (!defined('BASE_URL')) {
        $scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = rtrim(str_replace('\\', '/', $scriptDir), '/');
        
        // Remove any double slashes that might occur
        $baseUrl = rtrim(preg_replace('#/+#', '/', "$scheme://$host$basePath"), '/') . '/';
        
        define('BASE_URL', $baseUrl);
    }
    
    // Define other URLs
    if (!defined('ASSETS_URL')) {
        define('ASSETS_URL', BASE_URL . 'assets/');
    }
    
    if (!defined('UPLOADS_URL')) {
        define('UPLOADS_URL', BASE_URL . 'uploads/');
    }

    // Initialize database connection if not already done
    if (!isset($pdo)) {
        try {
            // Database connection using constants from config
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                DB_HOST,
                DB_NAME
            );
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_STRINGIFY_FETCHES  => false
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            error_log('Database connection error: ' . $e->getMessage());
            
            if (defined('IS_DEVELOPMENT') && IS_DEVELOPMENT) {
                die('Database connection failed: ' . $e->getMessage());
            } else {
                // In production, show a generic error message
                if (!headers_sent()) {
                    header('HTTP/1.1 503 Service Unavailable');
                }
                die('We are experiencing technical difficulties. Please try again later.');
            }
        }
    }

    // Initialize $one object if not already set
    if (!isset($one)) {
        $one = new stdClass();
        $one->assets_folder = 'assets';
        $one->theme = 'amethyst'; // Default theme
        $one->title = 'AK23STK - Admin Dashboard';
        $one->description = 'AK23STK Admin Dashboard';
        $one->author = 'AK23STK';
        $one->robots = 'noindex, nofollow';
    }
    
    // Set error reporting based on environment
    if (!defined('IS_DEVELOPMENT')) {
        define('IS_DEVELOPMENT', ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1'));
    }
    
    // Error reporting settings
    if (IS_DEVELOPMENT) {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    } else {
        error_reporting(0);
        ini_set('display_errors', 0);
    }
    
    // Set default timezone
    date_default_timezone_set('Africa/Nairobi');
    
    // Load helper functions if not already loaded
    if (!function_exists('e')) {
        /**
         * Escape output to prevent XSS
         * 
         * @param string $string The string to escape
         * @param bool $strip_tags Whether to strip HTML tags
         * @return string The escaped string
         */
        function e($string, $strip_tags = false) {
            if ($strip_tags) {
                $string = strip_tags($string);
            }
            return htmlspecialchars($string, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
    }

    // Include helper functions if the file exists
    $functionsFile = __DIR__ . '/functions.php';
    if (file_exists($functionsFile)) {
        require_once $functionsFile;
    }
} catch (Exception $e) {
    error_log('Initialization error: ' . $e->getMessage());
    die('An error occurred during application initialization. Please try again later.');
} // End of INIT_LOADED check

// Check if user is logged in for protected pages
function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

// Check if user is admin
function requireAdmin() {
    requireLogin();
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        header('Location: index.php');
        exit();
    }
}

// Set default timezone
date_default_timezone_set('Africa/Nairobi');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
