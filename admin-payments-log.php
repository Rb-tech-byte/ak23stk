<?php
// admin-payments-log.php
// Admin view: List all payments
session_start();
require_once __DIR__ . '/database/db_config.php';

// Only allow admin (add your own admin check logic here)
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    die('Access denied. Admins only.');
}

// Fetch payments with order and user info
$stmt = $pdo->query("SELECT p.*, o.product_id, o.pesapal_merchant_reference, o.pesapal_tracking_id, u.email as user_email FROM payments p LEFT JOIN orders o ON p.order_id = o.id LEFT JOIN users u ON p.user_id = u.id ORDER BY p.paid_at DESC LIMIT 100");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>All Payments Log</title>
    <link rel=\"stylesheet\" href=\"assets/css/4download-style.css\">
</head>
<body>
    <div class=\"container mt-5\">
        <h2>All Payments Log</h2>
        <table class=\"table table-bordered\">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Order</th>
                    <th>Product</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Pesapal Tracking</th>
                    <th>Pesapal Ref</th>
                    <th>Status</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= htmlspecialchars($payment['id']) ?></td>
                    <td><?= htmlspecialchars($payment['user_id']) ?></td>
                    <td><?= htmlspecialchars($payment['user_email']) ?></td>
                    <td><?= htmlspecialchars($payment['order_id']) ?></td>
                    <td><?= htmlspecialchars($payment['product_id']) ?></td>
                    <td><?= htmlspecialchars($payment['amount']) ?></td>
                    <td><?= htmlspecialchars($payment['payment_method']) ?></td>
                    <td><?= htmlspecialchars($payment['pesapal_transaction_id']) ?></td>
                    <td><?= htmlspecialchars($payment['pesapal_merchant_reference']) ?></td>
                    <td><?= htmlspecialchars($payment['status']) ?></td>
                    <td><?= htmlspecialchars($payment['paid_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
