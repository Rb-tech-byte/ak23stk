<?php
// payment-status.php
// Show user-friendly payment status after callback or manual check
session_start();
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/pesapal_functions.php';

$tracking_id = $_GET['OrderTrackingId'] ?? '';
$merchant_ref = $_GET['OrderMerchantReference'] ?? '';
$message = '';

if (!$tracking_id) {
    $message = 'No payment tracking ID provided.';
} else {
    $access_token = get_pesapal_auth_token();
    if (!$access_token) {
        $message = 'Could not authenticate to payment gateway.';
    } else {
        $status = get_pesapal_transaction_status($access_token, $tracking_id);
        if (!$status) {
            $message = 'Could not fetch payment status.';
        } else {
            $message = 'Payment status: ' . htmlspecialchars($status['payment_status_description'] ?? 'Unknown');
            if (!empty($status['description'])) {
                $message .= '<br>' . htmlspecialchars($status['description']);
            }
        }
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Status</title>
    <link rel="stylesheet" href="assets/css/4download-style.css">
</head>
<body>
    <div class="container mt-5">
        <div class="alert alert-info text-center">
            <?= $message ?>
        </div>
        <a href="index.php" class="btn btn-primary">Return Home</a>
    </div>
</body>
</html>
