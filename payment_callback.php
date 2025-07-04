<?php
// payment_callback.php - Handles Pesapal payment return/callback
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/pesapal_functions.php';

$orderTrackingId = $_GET['OrderTrackingId'] ?? '';
$merchantReference = $_GET['OrderMerchantReference'] ?? '';

if (!$orderTrackingId || !$merchantReference) {
    die('Missing parameters.');
}

// Get access token
$access_token = get_pesapal_auth_token();
if (!$access_token) {
    die('Could not authenticate to Pesapal');
}

// Get transaction status from Pesapal
$status = get_pesapal_transaction_status($access_token, $orderTrackingId);
error_log('Callback: Pesapal transaction status: ' . json_encode($status));

// Optionally, update your orders table with the latest status and confirmation code
if ($status && isset($status['status']) && ($status['status'] === 'COMPLETED' || $status['status'] === 'PAID')) {
    $confirmation_code = $status['confirmation_code'] ?? null;
    // Add pesapal_confirmation_code column if not exists in your orders table
    $stmt = $pdo->prepare("UPDATE orders SET status = 'paid', pesapal_confirmation_code = ? WHERE pesapal_tracking_id = ? AND pesapal_merchant_reference = ?");
    $stmt->execute([$confirmation_code, $orderTrackingId, $merchantReference]);
    echo "Payment confirmed. Thank you!";
} else {
    echo "Payment not completed or could not retrieve status.";
}
