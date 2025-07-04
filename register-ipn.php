<?php
// register-ipn.php
// Register your IPN URL with Pesapal (run once or on admin panel)
require_once __DIR__ . '/pesapal_functions.php';

$access_token = get_pesapal_auth_token();
if (!$access_token) die('Could not authenticate to Pesapal');

$ipn_url = PESAPAL_IPN_URL;
$type = 'POST'; // or 'GET'

$payload = json_encode([
    'url' => $ipn_url,
    'ipn_notification_type' => $type
]);

$url = get_pesapal_base_url() . '/api/URLSetup/RegisterIPN';
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

$data = json_decode($response, true);
if ($httpCode !== 200 || !empty($data['error'])) {
    echo 'Failed to register IPN: ' . htmlspecialchars($response);
} else {
    echo 'IPN registered successfully! IPN ID: ' . htmlspecialchars($data['ipn_id'] ?? '');
}
