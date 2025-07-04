<?php
// refund.php - Admin refund endpoint for Pesapal
session_start();
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/pesapal_functions.php';

// Only allow admin (add your own admin check logic here)
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    die('Access denied. Admins only.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('POST only.');
}

$order_tracking_id = $_POST['order_tracking_id'] ?? '';
$merchant_reference = $_POST['merchant_reference'] ?? '';
$amount = $_POST['amount'] ?? '';
$reason = $_POST['reason'] ?? 'Customer requested refund';

if (!$order_tracking_id || !$merchant_reference || !$amount || !is_numeric($amount)) {
    http_response_code(400);
    die('Missing or invalid parameters.');
}

$access_token = get_pesapal_auth_token();
if (!$access_token) die('Could not authenticate to Pesapal');

// Fetch confirmation_code from Pesapal transaction status
$status = get_pesapal_transaction_status($access_token, $order_tracking_id);
if (empty($status['confirmation_code'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Could not retrieve confirmation code for this transaction.']);
    exit;
}
$confirmation_code = $status['confirmation_code'];

// Use admin email or name as username
$username = '';
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT name, email FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $username = $user['email'] ?? $user['name'] ?? 'admin';
} else {
    $username = 'admin';
}

$result = pesapal_refund_request($access_token, $confirmation_code, $amount, $username, $reason);

header('Content-Type: application/json');
if ($result) {
    echo json_encode(['success' => true, 'data' => $result]);
} else {
    echo json_encode(['success' => false, 'error' => 'Refund failed. Check logs for details.']);
}
