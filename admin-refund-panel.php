<?php
// admin-refund-panel.php
// Simple admin interface to request Pesapal refunds
session_start();
require_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/pesapal_functions.php';
require_once __DIR__ . '/inc/_global/config.php';
require_once __DIR__ . '/inc/backend_boxed/config.php';

// Only allow admin (add your own admin check logic here)
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    die('Access denied. Admins only.');
}

// Fetch last 20 paid orders for refund action
$stmt = $pdo->query("SELECT * FROM orders WHERE status = 'paid' ORDER BY updated_at DESC LIMIT 20");
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<?php
require_once __DIR__ . '/inc/_global/views/head_start.php';
require_once __DIR__ . '/inc/_global/views/head_end.php';
?>
<!-- Optionally include custom style if needed for this page -->
<link rel="stylesheet" href="assets/css/4download-style.css">
    <div class="container mt-5">
        <h2>Refund a Pesapal Payment</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Merchant Ref</th>
                    <th>Tracking ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Refund</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($orders)): ?>
    <tr><td colspan="7" class="text-center">No paid orders found.</td></tr>
<?php else: ?>
<?php foreach ($orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['pesapal_merchant_reference'] ?? '') ?></td>
                    <td><?= htmlspecialchars($order['pesapal_tracking_id'] ?? '') ?></td>
                    <td>
                        <?php
                        // Optionally fetch user info if you want to show name/email
                        $username = 'admin';
                        if (!empty($order['user_id'])) {
                            $userStmt = $pdo->prepare('SELECT name, email FROM users WHERE id = ?');
                            $userStmt->execute([$order['user_id']]);
                            $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                            if ($user) {
                                echo htmlspecialchars($user['name'] ?? '') . ' <br><small>' . htmlspecialchars($user['email'] ?? '') . '</small>';
                                $username = $user['email'] ?? $user['name'] ?? 'admin';
                            } else {
                                echo htmlspecialchars($order['user_id']);
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td><?= htmlspecialchars($order['total'] ?? '') ?></td>
                    <td><?= htmlspecialchars($order['status'] ?? '') ?></td>
                    <td>
                        <?php
                        // Fetch confirmation_code for this order
                        $confirmation_code = '';
                        if (!empty($order['pesapal_tracking_id'])) {
                            $access_token = get_pesapal_auth_token();
                            if ($access_token) {
                                $status = get_pesapal_transaction_status($access_token, $order['pesapal_tracking_id']);
                                error_log('RefundPanel: Transaction status for ' . $order['pesapal_tracking_id'] . ': ' . json_encode($status));
                                if (!empty($status['confirmation_code'])) {
                                    $confirmation_code = $status['confirmation_code'];
                                }
                            }
                        }
                        ?>
                        <form method="post" action="refund.php" onsubmit="return confirm('Refund this payment?');">
                            <input type="hidden" name="confirmation_code" value="<?= htmlspecialchars($confirmation_code) ?>">
                            <input type="number" step="0.01" name="amount" value="<?= htmlspecialchars($order['total'] ?? '') ?>" required>
                            <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
                            <input type="text" name="remarks" placeholder="Reason" value="Customer requested refund" required>
                            <?php if (empty($confirmation_code)): ?>
                                <span class="text-danger small">No confirmation code found</span>
                            <?php else: ?>
                                <button type="submit" class="btn btn-danger btn-sm">Refund</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
<?php endif; ?>
            </tbody>
        </table>
    </div>
<?php
require_once __DIR__ . '/inc/_global/views/footer_start.php';
require_once __DIR__ . '/inc/_global/views/footer_end.php';
?>
