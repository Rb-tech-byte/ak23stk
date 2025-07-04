<?php
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/inc/_global/config.php';
require_once __DIR__ . '/inc/backend/config.php';
include __DIR__ . '/inc/_global/views/head_start.php';
include __DIR__ . '/inc/_global/views/head_end.php';
include __DIR__ . '/inc/_global/views/page_start.php';

if (isset($_POST['save_smtp'])) {
    $host = trim($_POST['smtp_host']);
    $port = trim($_POST['smtp_port']);
    $user = trim($_POST['smtp_user']);
    $pass = trim($_POST['smtp_pass']);
    $encryption = trim($_POST['smtp_encryption']);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='smtp_host'")->execute([$host]);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='smtp_port'")->execute([$port]);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='smtp_user'")->execute([$user]);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='smtp_pass'")->execute([$pass]);
    $pdo->prepare("UPDATE settings SET value=? WHERE name='smtp_encryption'")->execute([$encryption]);
    $msg = 'SMTP settings updated.';
}
function get_setting($name, $pdo) {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name=?");
    $stmt->execute([$name]);
    return $stmt->fetchColumn();
}
$smtp_host = get_setting('smtp_host', $pdo);
$smtp_port = get_setting('smtp_port', $pdo);
$smtp_user = get_setting('smtp_user', $pdo);
$smtp_pass = get_setting('smtp_pass', $pdo);
$smtp_encryption = get_setting('smtp_encryption', $pdo);

// Helper: Send system email using PHP's mail()
function send_system_mail($to, $subject, $body, $headers = '') {
    // You can customize headers further, e.g. add From, Reply-To, etc.
    return mail($to, $subject, $body, $headers);
}
?>
<div class="content">
    <h2 class="content-heading">Mail SMTP Settings</h2>
    <?php if(isset($msg)): ?><div class="alert alert-success"><?php echo htmlspecialchars($msg); ?></div><?php endif; ?>
    <div class="block">
        <div class="block-header block-header-default"><h3 class="block-title">SMTP Config</h3></div>
        <div class="block-content">
            <form method="post">
                <div class="row">
                    <div class="col-md-2"><input type="text" name="smtp_host" class="form-control" placeholder="Host" value="<?php echo htmlspecialchars($smtp_host); ?>" required></div>
                    <div class="col-md-1"><input type="text" name="smtp_port" class="form-control" placeholder="Port" value="<?php echo htmlspecialchars($smtp_port); ?>" required></div>
                    <div class="col-md-2"><input type="text" name="smtp_user" class="form-control" placeholder="User" value="<?php echo htmlspecialchars($smtp_user); ?>" required></div>
                    <div class="col-md-2"><input type="password" name="smtp_pass" class="form-control" placeholder="Password" value="<?php echo htmlspecialchars($smtp_pass); ?>" required></div>
                    <div class="col-md-2">
                        <select name="smtp_encryption" class="form-control">
                            <option value="">None</option>
                            <option value="ssl" <?php if($smtp_encryption=="ssl") echo 'selected'; ?>>SSL</option>
                            <option value="tls" <?php if($smtp_encryption=="tls") echo 'selected'; ?>>TLS</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" name="save_smtp" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
include $one->inc_footer;
?>
