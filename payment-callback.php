<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/pesapal_functions.php';

$tracking_id = $_GET['OrderTrackingId'] ?? '';
$merchant_ref = $_GET['OrderMerchantReference'] ?? '';

if (empty($tracking_id)) {
    header("Location: index.php");
    exit;
}

// Get access token
$access_token = get_pesapal_auth_token();
if (!$access_token) die("Payment verification failed");

// Get transaction status
$status = get_pesapal_transaction_status($access_token, $tracking_id);
if (!$status) die("Payment status check failed");

// Find order
$stmt = $pdo->prepare("SELECT o.*, u.email 
                      FROM orders o
                      JOIN users u ON o.user_id = u.id
                      WHERE pesapal_tracking_id = ?");
$stmt->execute([$tracking_id]);
$order = $stmt->fetch();

if ($order) {
    // Update payment status
    if ($status['payment_status_description'] === 'Completed') {
        $new_status = 'paid';
        $paid_at = date('Y-m-d H:i:s');
        
        // Record payment
        $stmt = $pdo->prepare("INSERT INTO payments (user_id, order_id, amount, payment_method, pesapal_transaction_id, pesapal_status, status, paid_at)
                              VALUES (?, ?, ?, 'Pesapal', ?, ?, 'completed', ?)");
        $stmt->execute([
            $order['user_id'],
            $order['id'],
            $order['total'],
            $tracking_id,
            $status['payment_status_description'],
            $paid_at
        ]);
        
        // Send download link
        $download_url = SITE_URL . '/download.php?token=' . $order['download_token'];
        $message = "Your download is ready: $download_url";
        // Implement SMS sending here
    }
    
    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$new_status ?? 'pending', $order['id']]);
}

// Redirect to download or dashboard
if ($status['payment_status_description'] === 'Completed') {
    $_SESSION['payment_success'] = true;
    header("Location: download.php?token=" . $order['download_token']);
} else {
    header("Location: dashboard.php?payment=pending");
}
exit;