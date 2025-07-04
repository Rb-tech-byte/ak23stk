<?php
// admin-refunds-log.php
// Admin view: List all refund requests and results
session_start();
require_once __DIR__ . '/database/db_config.php';

// Only allow admin (add your own admin check logic here)
if (!isset($_SESSION['user_id']) || empty($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    http_response_code(403);
    die('Access denied. Admins only.');
}

// Fetch refund logs (if you have a refunds table, otherwise show recent payments with refund attempts)
$stmt = $pdo->query("SELECT * FROM payments WHERE status = 'refunded' OR pesapal_status = 'Refunded' ORDER BY paid_at DESC LIMIT 100");
$refunds = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <title>Refunds Log</title>
    <link rel=\"stylesheet\" href=\"assets/css/4download-style.css\">
</head>
<body>
    <div class=\"container mt-5\">
        <h2>Refunds Log</h2>
        <table class=\"table table-bordered\">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Order</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Pesapal Tracking</th>
                    <th>Status</th>
                    <th>Paid At</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($refunds as $refund): ?>
                <tr>
                    <td><?= htmlspecialchars($refund['id']) ?></td>
                    <td><?= htmlspecialchars($refund['user_id']) ?></td>
                    <td><?= htmlspecialchars($refund['order_id']) ?></td>
                    <td><?= htmlspecialchars($refund['amount']) ?></td>
                    <td><?= htmlspecialchars($refund['payment_method']) ?></td>
                    <td><?= htmlspecialchars($refund['pesapal_transaction_id']) ?></td>
                    <td><?= htmlspecialchars($refund['status']) ?></td>
                    <td><?= htmlspecialchars($refund['paid_at']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
