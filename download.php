<?php
require_once __DIR__ . '/database/db_config.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Invalid token");
}

// Find order
$stmt = $pdo->prepare("SELECT o.*, p.files 
                      FROM orders o
                      JOIN products p ON o.product_id = p.id
                      WHERE o.download_token = ? 
                      AND o.download_expiry > NOW() 
                      AND o.status = 'paid'");
$stmt->execute([$token]);
$order = $stmt->fetch();

if (!$order) {
    die("Invalid or expired download link. Payment not verified.");
}

// ... rest of download logic remains the same ...