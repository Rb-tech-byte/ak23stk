<?php
require_once __DIR__ . '/database/db_config.php';

// EvMak credentials
define('EVMAK_USERNAME', 'your_evmak_username');
define('EVMAK_HASH_SECRET', 'your_evmak_secret');
define('SITE_URL', 'https://yourdomain.com');

$mysqli = get_db_connection();

// Get callback data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    http_response_code(400);
    die("Invalid data");
}

// Validate callback hash
function validate_evmak_callback($data) {
    $expected_hash = md5(EVMAK_USERNAME . '|' . $data['TransID'] . '|' . $data['Amount']);
    return $expected_hash === $data['Hash'];
}

if (!validate_evmak_callback($data)) {
    http_response_code(403);
    die("Invalid callback hash");
}

// Find order by reference
$stmt = $mysqli->prepare("SELECT o.*, u.phone, u.is_guest, u.temp_password 
                         FROM orders o 
                         JOIN users u ON o.user_id = u.id 
                         WHERE o.evmak_reference = ?");
$stmt->bind_param('s', $data['ThirdPartyReference']);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    http_response_code(404);
    die("Order not found");
}

// Process successful payment
if ($data['ResultType'] && $data['TransactionStatus'] == 'Success') {
    // Update order status
    $stmt = $mysqli->prepare("UPDATE orders SET 
        status = 'paid',
        paid_at = NOW()
        WHERE id = ?");
    $stmt->bind_param('i', $order['id']);
    $stmt->execute();
    $stmt->close();
    
    // Record payment
    $stmt = $mysqli->prepare("INSERT INTO payments (
        user_id, order_id, amount, phone, payment_method, 
        evmak_transaction_id, evmak_status, status, paid_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, 'completed', NOW())");
    $stmt->bind_param('iidssss', 
        $order['user_id'],
        $order['id'],
        $data['Amount'] / 100, // convert to TZS
        $data['mobileNo'],
        'EvMak',
        $data['TransID'],
        $data['TransactionStatus']
    );
    $stmt->execute();
    $stmt->close();
    
    // Send download link via SMS
    $download_url = SITE_URL . "/download.php?token=" . $order['download_token'];
    $message = "Your download is ready: $download_url\n";
    
    if ($order['is_guest']) {
        $message .= "Login with mobile: {$order['phone']} and password: {$order['temp_password']}";
    }
    
    // Implement your SMS sending function here
    send_sms($order['phone'], $message);
}

// Respond to EvMak
header('Content-Type: application/json');
echo json_encode(['Status' => 'Success']);

function send_sms($phone, $message) {
    // Implement your SMS gateway integration here
    // This is just a placeholder
    error_log("SMS to $phone: $message");
    return true;
}
?>