<?php
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/inc/_global/config.php';
require_once __DIR__ . '/inc/backend/config.php';
include __DIR__ . '/inc/_global/views/head_start.php';
include __DIR__ . '/inc/_global/views/head_end.php';
include __DIR__ . '/inc/_global/views/page_start.php';

if (isset($_POST['save_sms'])) {
    $twilio_sid = trim($_POST['twilio_sid']);
    $twilio_token = trim($_POST['twilio_token']);
    $twilio_from = trim($_POST['twilio_from']);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='twilio_sid'")->execute([$twilio_sid]);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='twilio_token'")->execute([$twilio_token]);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='twilio_from'")->execute([$twilio_from]);
    $msg = 'Twilio SMS settings updated.';
}
function get_setting($name, $pdo) {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name=?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn();
}
$twilio_sid = get_setting('twilio_sid', $pdo);
$twilio_token = get_setting('twilio_token', $pdo);
$twilio_from = get_setting('twilio_from', $pdo);

// Helper: Send SMS via Twilio
function send_sms($to, $message, $sid, $token, $from) {
    $url = "https://api.twilio.com/2010-04-01/Accounts/$sid/Messages.json";
    $data = http_build_query([
        'To' => $to,
        'From' => $from,
        'Body' => $message
    ]);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$sid:$token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $response = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) return false;
    return $response;
}
?>
<div class="content">
    <h2 class="content-heading">Twilio SMS Settings</h2>
    <?php if (!empty($msg)): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Twilio Account SID</label>
            <input type="text" name="twilio_sid" class="form-control" value="<?php echo htmlspecialchars($twilio_sid); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Twilio Auth Token</label>
            <input type="text" name="twilio_token" class="form-control" value="<?php echo htmlspecialchars($twilio_token); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Twilio From Number (e.g. +1234567890)</label>
            <input type="text" name="twilio_from" class="form-control" value="<?php echo htmlspecialchars($twilio_from); ?>" required>
        </div>
        <button type="submit" name="save_sms" class="btn btn-primary">Save Settings</button>
    </form>
</div>
<?php include __DIR__ . '/inc/_global/views/page_end.php'; ?>
