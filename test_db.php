<?php
// Test database connection
require_once 'database/db_config.php';

header('Content-Type: text/plain');

try {
    $pdo = get_db_connection();
    echo "✅ Database connection successful!\n";
    
    // Test a simple query
    $stmt = $pdo->query('SELECT 1 as test');
    $result = $stmt->fetch();
    echo "✅ Test query executed successfully. Result: " . $result['test'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    echo "DSN: mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET . "\n";
    
    // Check if MySQL is running
    $mysqlRunning = @fsockopen('127.0.0.1', 3306, $errno, $errstr, 5);
    if ($mysqlRunning === false) {
        echo "❌ MySQL server is not running or not accessible\n";
    } else {
        echo "ℹ️ MySQL server is running\n";
        fclose($mysqlRunning);
    }
}
