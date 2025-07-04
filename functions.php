<?php
// Generate EvMak hash: MD5(username|date)
function generate_evmak_hash() {
    $date = date('Ymd');
    return md5(EVMAK_USERNAME . '|' . $date);
}



// Create guest user
function create_guest_user($pdo, $mobile) {
    $temp_password = bin2hex(random_bytes(8));
    $email = 'guest_' . time() . '_' . $mobile . '@ak23studiokits.com';
    $name = 'Guest_' . substr($mobile, -4);
    
    $stmt = $pdo->prepare("INSERT INTO users (role_id, name, email, password, phone, is_guest, temp_password) 
                          VALUES (?, ?, ?, ?, ?, 1, ?)");
    $hashed_password = password_hash($temp_password, PASSWORD_DEFAULT);
    $stmt->execute([2, $name, $email, $hashed_password, $mobile, $temp_password]);
    
    return [
        'id' => $pdo->lastInsertId(),
        'temp_password' => $temp_password
    ];
}

// Send SMS function (placeholder)
function send_sms($phone, $message) {
    // Implement your SMS gateway here
    error_log("SMS to $phone: $message");
    return true;
}

// Get product details
function get_product($pdo, $product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    return $stmt->fetch();
}

// Login user
function login_user($user_id) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['last_activity'] = time();
}

// Auto-logout guests after 30 minutes
function check_session_timeout() {
    if (isset($_SESSION['user_id']) && isset($_SESSION['last_activity'])) {
        $inactive = 1800; // 30 minutes in seconds
        if (time() - $_SESSION['last_activity'] > $inactive) {
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit;
        }
        $_SESSION['last_activity'] = time();
    }
}