<?php
// Test configuration and database connection
require_once 'init.php';

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration Test</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .section { margin-bottom: 30px; border-bottom: 1px solid #dee2e6; padding-bottom: 20px; }
    </style>
</head>
<body>
    <h1>AK23STK Configuration Test</h1>
    
    <div class="section">
        <h2>1. Basic Information</h2>
        <ul>
            <li>PHP Version: <?php echo PHP_VERSION; ?></li>
            <li>Server Software: <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></li>
            <li>Server Name: <?php echo $_SERVER['SERVER_NAME'] ?? 'N/A'; ?></li>
            <li>Document Root: <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'N/A'; ?></li>
        </ul>
    </div>

    <div class="section">
        <h2>2. Path Configuration</h2>
        <ul>
            <li>ROOT_PATH: <?php echo defined('ROOT_PATH') ? ROOT_PATH : '<span class="error">Not defined</span>'; ?></li>
            <li>INCLUDES_PATH: <?php echo defined('INCLUDES_PATH') ? INCLUDES_PATH : '<span class="error">Not defined</span>'; ?></li>
            <li>BASE_URL: <?php echo defined('BASE_URL') ? BASE_URL : '<span class="error">Not defined</span>'; ?></li>
        </ul>
    </div>

    <div class="section">
        <h2>3. Database Connection</h2>
        <?php
        try {
            if (!isset($pdo)) {
                echo '<p class="error">❌ Database connection not initialized</p>';
            } else {
                $stmt = $pdo->query('SELECT DATABASE() as db, VERSION() as version');
                $dbInfo = $stmt->fetch();
                
                echo '<p class="success">✅ Database connection successful!</p>';
                echo '<ul>';
                echo '<li>Database: ' . htmlspecialchars($dbInfo['db']) . '</li>';
                echo '<li>MySQL Version: ' . htmlspecialchars($dbInfo['version']) . '</li>';
                echo '<li>PDO Driver: ' . $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) . '</li>';
                echo '<li>PDO Client Version: ' . $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION) . '</li>';
                echo '</ul>';
                
                // Test a simple query
                $stmt = $pdo->query('SELECT COUNT(*) as count FROM products');
                $result = $stmt->fetch();
                echo '<p>Total Products: ' . $result['count'] . '</p>';
            }
        } catch (PDOException $e) {
            echo '<div class="error">';
            echo '<p>❌ Database Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }
        ?>
    </div>

    <div class="section">
        <h2>4. PHP Configuration</h2>
        <ul>
            <li>display_errors: <?php echo ini_get('display_errors') ? 'On' : 'Off'; ?></li>
            <li>error_reporting: <?php echo ini_get('error_reporting'); ?></li>
            <li>log_errors: <?php echo ini_get('log_errors') ? 'On' : 'Off'; ?></li>
            <li>error_log: <?php echo ini_get('error_log') ?: 'Not set'; ?></li>
            <li>max_execution_time: <?php echo ini_get('max_execution_time'); ?> seconds</li>
            <li>memory_limit: <?php echo ini_get('memory_limit'); ?></li>
            <li>upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?></li>
            <li>post_max_size: <?php echo ini_get('post_max_size'); ?></li>
        </ul>
    </div>

    <div class="section">
        <h2>5. Session Information</h2>
        <h3>Session Status</h3>
        <ul>
            <li>Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive'; ?></li>
            <li>Session ID: <?php echo session_id(); ?></li>
            <li>Session Name: <?php echo session_name(); ?></li>
            <li>Session Save Path: <?php echo session_save_path(); ?></li>
            <li>Session Cookie Params: <?php echo json_encode(session_get_cookie_params(), JSON_PRETTY_PRINT); ?></li>
        </ul>
        
        <h3>Session Data</h3>
        <pre><?php echo !empty($_SESSION) ? htmlspecialchars(print_r($_SESSION, true)) : 'No session data'; ?></pre>
    </div>

    <div class="section">
        <h2>6. Environment Variables</h2>
        <h3>$_SERVER Variables</h3>
        <pre><?php 
        $serverVars = $_SERVER;
        // Remove sensitive information
        unset($serverVars['HTTP_COOKIE']);
        unset($serverVars['PHP_AUTH_PW']);
        unset($serverVars['MYSQL_ROOT_PASSWORD']);
        echo htmlspecialchars(print_r($serverVars, true)); 
        ?></pre>
    </div>

    <div class="section">
        <h2>7. File Permissions</h2>
        <ul>
            <li>Root Directory (<?php echo ROOT_PATH; ?>): 
                <?php 
                $rootWritable = is_writable(ROOT_PATH);
                echo $rootWritable ? 
                    '<span class="success">✅ Writable</span>' : 
                    '<span class="error">❌ Not writable</span>'; 
                ?>
            </li>
            <li>Uploads Directory (<?php echo ROOT_PATH . 'uploads/'; ?>): 
                <?php 
                $uploadsDir = ROOT_PATH . 'uploads/';
                $uploadWritable = is_dir($uploadsDir) && is_writable($uploadsDir);
                echo $uploadWritable ? 
                    '<span class="success">✅ Writable</span>' : 
                    '<span class="error">❌ Not writable or directory does not exist</span>'; 
                ?>
            </li>
            <li>Logs Directory (<?php echo ROOT_PATH . 'logs/'; ?>): 
                <?php 
                $logsDir = ROOT_PATH . 'logs/';
                $logsWritable = is_dir($logsDir) && is_writable($logsDir);
                echo $logsWritable ? 
                    '<span class="success">✅ Writable</span>' : 
                    '<span class="error">❌ Not writable or directory does not exist</span>'; 
                ?>
            </li>
        </ul>
    </div>
</body>
</html>
