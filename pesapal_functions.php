<?php
// pesapal_functions.php
define('PESAPAL_IPN_ID', '391a2ec4-9635-42bc-b7cb-dba75f9c110d');
define('PESAPAL_SANDBOX', true); // Set to false for production
define('PESAPAL_CONSUMER_KEY', 'ngW+UEcnDhltUc5fxPfrCD987xMh3Lx8');
define('PESAPAL_CONSUMER_SECRET', 'q27RChYs5UkypdcNYKzuUw460Dg=');
if (!defined('PESAPAL_CALLBACK_URL')) if (!defined('PESAPAL_CALLBACK_URL'))
 define('PESAPAL_CALLBACK_URL', '/payment/callback');
define('PESAPAL_IPN_URL', 'https://download.ak23studiokits.com/ipn');

/**
 * Get Pesapal Base URL (API 3.0)
 * @return string
 */
function get_pesapal_base_url() {
    return PESAPAL_SANDBOX 
        ? 'https://cybqa.pesapal.com/pesapalv3' 
        : 'https://pay.pesapal.com/v3';
}

/**
 * Get Pesapal Bearer Token (API 3.0)
 * @return string|null
 */
function get_pesapal_auth_token() {
    $url = get_pesapal_base_url() . '/api/Auth/RequestToken';
    $payload = json_encode([
        'consumer_key' => PESAPAL_CONSUMER_KEY,
        'consumer_secret' => PESAPAL_CONSUMER_SECRET
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !PESAPAL_SANDBOX);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Pesapal Auth Failed: HTTP $httpCode - $response");
        return null;
    }

    $data = json_decode($response, true);
    if (!empty($data['error'])) {
        error_log('Pesapal Auth Error: ' . json_encode($data['error']));
        return null;
    }
    return $data['token'] ?? null;
}
// Usage: $token = get_pesapal_auth_token();

/**
 * Submit Order to Pesapal (API 3.0)
 * @param string $access_token
 * @param array $order_data (must include 'notification_id')
 * @return array|null
 */
function submit_pesapal_order($access_token, $order_data) {
    // Ensure notification_id is set
    if (empty($order_data['notification_id'])) {
        $order_data['notification_id'] = PESAPAL_IPN_ID;
    }
    $url = get_pesapal_base_url() . '/api/Transactions/SubmitOrderRequest';
    $payload = json_encode($order_data);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !PESAPAL_SANDBOX);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Pesapal Order Failed: HTTP $httpCode - $response");
        return null;
    }

    $data = json_decode($response, true);
    if (!empty($data['error'])) {
        error_log('Pesapal Order Error: ' . json_encode($data['error']));
        return null;
    }
    return $data;
}
// Usage: $order = submit_pesapal_order($token, $orderData);

/**
 * Get Pesapal Transaction Status (API 3.0)
 * @param string $access_token
 * @param string $order_tracking_id
 * @return array|null
 */
function get_pesapal_transaction_status($access_token, $order_tracking_id) {
    $url = get_pesapal_base_url() . '/api/Transactions/GetTransactionStatus?orderTrackingId=' . urlencode($order_tracking_id);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !PESAPAL_SANDBOX);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("Pesapal GetTransactionStatus Failed: HTTP $httpCode - $response");
        return null;
    }

    $data = json_decode($response, true);
    if (!empty($data['error'])) {
        error_log('Pesapal GetTransactionStatus Error: ' . json_encode($data['error']));
        return null;
    }
    return $data;
}
// Usage: $status = get_pesapal_transaction_status($token, $orderTrackingId);

/**
 * Request a refund for a Pesapal transaction.
 * @param string $access_token
 * @param string $order_tracking_id
 * @param string $merchant_reference
 * @param float $amount
 * @param string $reason
 * @return array|null
 */
function pesapal_refund_request($access_token, $confirmation_code, $amount, $username, $remarks = 'Customer requested refund') {
    $url = get_pesapal_base_url() . '/api/Transactions/RefundRequest';
    $payload_array = [
        'confirmation_code' => $confirmation_code,
        'amount' => number_format((float)$amount, 2, '.', ''),
        'username' => $username,
        'remarks' => $remarks
    ];
    error_log('Pesapal refund payload: ' . json_encode($payload_array));
    $payload = json_encode($payload_array);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $access_token
    ]);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, !PESAPAL_SANDBOX);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    if (isset($data['error'])) {
        error_log('Pesapal refund error: ' . print_r($data, true));
        // Return the error response so the UI/admin can see it
        return $data;
    }
    return $data;
}
// Usage: $result = pesapal_refund_request($token, $order_tracking_id, $merchant_reference, $amount, $reason);