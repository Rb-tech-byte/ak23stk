<?php
header('Content-Type: application/json');

require_once 'EvMakPaymentController.php';

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
$required = ['product_id', 'amount', 'card_number', 'card_expiry', 'card_cvv', 'customer_email'];
foreach ($required as $field) {
    if (empty($input[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Process payment
$controller = new EvMakPaymentController();
$result = $controller->processPayment($input);

if ($result['status'] === 'success') {
    // On success, you would typically:
    // 1. Save order to database
    // 2. Send confirmation email
    // 3. Update product access for user
    
    // For now we'll just return the success response
    $response = [
        'success' => true,
        'message' => 'Payment processed successfully',
        'payment_id' => $result['payment_id'],
        'product_id' => $input['product_id']
    ];
} else {
    $response = [
        'success' => false,
        'message' => $result['message'] ?? 'Payment processing failed',
        'payment_id' => $result['payment_id'] ?? null
    ];
}

echo json_encode($response);
