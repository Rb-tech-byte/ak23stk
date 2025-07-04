<?php
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/pesapal_functions.php';

// Get IPN data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    http_response_code(400);
    exit;
}

// Verify required fields
$required = ['OrderTrackingId', 'OrderNotificationType', 'OrderMerchantReference'];
foreach ($required as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        exit;
    }
}

// Get access token
$access_token = get_pesapal_auth_token();
if (!$access_token) {
    http_response_code(500);
    exit;
}

// Verify transaction status
$status = get_pesapal_transaction_status($access_token, $data['OrderTrackingId']);
if (!$status || $status['payment_status_description'] !== 'Completed') {
    http_response_code(400);
    exit;
}

// Find order
$stmt = $pdo->prepare("SELECT * FROM orders WHERE pesapal_tracking_id = ?");
$stmt->execute([$data['OrderTrackingId']]);
$order = $stmt->fetch();

if ($order && $order['status'] !== 'paid') {
    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid' WHERE id = ?");
    $stmt->execute([$order['id']]);
    
    // Record payment
    $stmt = $pdo->prepare("INSERT INTO payments (user_id, order_id, amount, payment_method, pesapal_transaction_id, pesapal_status, status, paid_at)
                          VALUES (?, ?, ?, 'Pesapal', ?, ?, 'completed', NOW())");
    $stmt->execute([
        $order['user_id'],
        $order['id'],
        $order['total'],
        $data['OrderTrackingId'],
        $status['payment_status_description']
    ]);
    
    // Send download link
    $download_url = SITE_URL . '/download.php?token=' . $order['download_token'];
    $message = "Your download is ready: $download_url";
    // Implement SMS sending here
}

http_response_code(200);
echo json_encode(['status' => 'success']);