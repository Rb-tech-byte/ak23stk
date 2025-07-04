<?php
session_start();
require_once __DIR__ . '/database/db_config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$payment = null;

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, u.name as customer_name, o.id as order_number FROM payments p LEFT JOIN users u ON p.user_id = u.id LEFT JOIN orders o ON p.order_id = o.id WHERE p.id = ? AND p.deleted_at IS NULL");
        $stmt->execute([$id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching payment details: " . $e->getMessage();
    }
}

if (!$payment) {
    $_SESSION['error_message'] = "Payment not found or has been deleted.";
    header('Location: admin_dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php 
    if (!isset($one)) {
        $one = new stdClass();
        $one->assets_folder = 'assets';
        $one->theme = 'default';
    }
    include 'inc/_global/views/head_end.php'; 
    ?>
    <title>Payment Details - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>Payment Details</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment #<?php echo $payment['id']; ?></h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <h5>Customer Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($payment['customer_name'] ?? 'N/A'); ?></p>
                        <p><strong>User ID:</strong> <?php echo htmlspecialchars($payment['user_id'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Payment Information</h5>
                        <p><strong>Amount:</strong> $<?php echo number_format($payment['amount'] ?? 0, 2); ?></p>
                        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($payment['payment_method'] ?? 'N/A'); ?></p>
                        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($payment['payment_status'] ?? 'N/A'); ?></p>
                        <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($payment['transaction_id'] ?? 'N/A'); ?></p>
                        <p><strong>Order:</strong> <?php echo $payment['order_number'] ? '#'.htmlspecialchars($payment['order_number']) : 'N/A'; ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($payment['created_at'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="edit_payment.php?id=<?php echo $payment['id']; ?>" class="btn btn-warning" data-toggle="tooltip" title="Edit Payment"><i class="bi bi-pencil"></i> Edit</a>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
</body>
</html>
