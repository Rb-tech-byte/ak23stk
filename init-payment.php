<?php
session_start(); // Start session at the very top

require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/pesapal_functions.php';

// Verify database connection exists
if (!isset($pdo)) {
    die("Database connection not established");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_UNSAFE_RAW);
    
    // Validate inputs
    if (!$product_id || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid product or email");
    }
    
    // Validate Tanzanian phone number (255 followed by 9 digits)
    if (!preg_match('/^255\d{9}$/', $phone)) {
        die("Invalid phone number. Must be 255 followed by 9 digits");
    }
    
    try {
        // Get product details
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product) die("Product not found");
        
        // User handling (guest or registered)
        $user_id = $_SESSION['user_id'] ?? null;
        $is_guest = false;
        $temp_password = null;
        
        if (!$user_id) {
            // Generate guest credentials
            $temp_password = bin2hex(random_bytes(6));
            $username = 'Guest_' . time() . '_' . substr($phone, -4);
            $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO users (role_id, name, email, password, phone, is_guest, temp_password) 
                                   VALUES (2, ?, ?, ?, ?, 1, ?)");
            $stmt->execute([$username, $email, $hashed_password, $phone, $temp_password]);
            $user_id = $pdo->lastInsertId();
            $is_guest = true;
        }
        
        // Only generate order reference and token, do NOT create DB order yet
        $download_token = bin2hex(random_bytes(32));
        $download_expiry = date('Y-m-d H:i:s', time() + (DOWNLOAD_EXPIRY_DAYS * 86400));
        $order_reference = 'AK23-' . time() . '-' . $product_id;
        
        // Prepare Pesapal payment
        $access_token = get_pesapal_auth_token();
        if (!$access_token) die("Payment authentication failed");
        
        $order_data = [
            'id' => $order_reference,
            'currency' => 'TZS',
            'amount' => $product['price'],
            'description' => $product['name'],
            'callback_url' => PESAPAL_CALLBACK_URL,
            'notification_id' => PESAPAL_IPN_ID, // Always include notification_id
            'billing_address' => [
                'email_address' => $email,
                'phone_number' => $phone,
                'country_code' => 'TZ'
            ]
        ];
        
        $response = submit_pesapal_order($access_token, $order_data);
        if (!$response) {
            error_log('Pesapal submit order failed: ' . print_r($response, true));
            die("Payment initiation failed: Could not connect to Pesapal");
        }
        if (!isset($response['redirect_url'])) {
            error_log('Pesapal response missing keys: ' . print_r($response, true));
            die('Payment gateway error: Missing required details. Please try again.');
        }
        // Show Pesapal payment iframe
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Complete Payment</title><meta name="viewport" content="width=device-width, initial-scale=1"><style>body{margin:0;padding:0;}iframe{border:none;width:100vw;height:100vh;}</style></head><body>';
        echo '<iframe src="' . htmlspecialchars($response['redirect_url']) . '" allowfullscreen></iframe>';
        echo '</body></html>';
        exit;
        
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
}

// Non-POST requests redirect home
header("Location: index.php");
exit;