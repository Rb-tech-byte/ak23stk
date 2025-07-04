<?php
session_start();
require_once __DIR__ . '/database/db_config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$payment = null;
$users = [];
$orders = [];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM payments WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        $payment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("SELECT id, name FROM users WHERE deleted_at IS NULL");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("SELECT id, total_amount FROM orders WHERE deleted_at IS NULL");
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching data: " . $e->getMessage();
    }
}

if (!$payment) {
    $_SESSION['error_message'] = "Payment not found or has been deleted.";
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);
    $payment_status = filter_input(INPUT_POST, 'payment_status', FILTER_SANITIZE_STRING);
    $transaction_id = filter_input(INPUT_POST, 'transaction_id', FILTER_SANITIZE_STRING);
    
    if ($user_id && $amount && $payment_method && $payment_status) {
        try {
            $stmt = $pdo->prepare("UPDATE payments SET user_id = ?, order_id = ?, amount = ?, payment_method = ?, payment_status = ?, transaction_id = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$user_id, $order_id ?: null, $amount, $payment_method, $payment_status, $transaction_id, $id]);
            $_SESSION['success_message'] = "Payment updated successfully.";
            header('Location: payment_details.php?id=' . $id);
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error updating payment: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Please fill in all required fields.";
    }
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
    <title>Edit Payment - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>Edit Payment</h1>
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
                
                <form method="POST" action="edit_payment.php?id=<?php echo $payment['id']; ?>">
                    <div class="form-group">
                        <label for="user_id">Customer</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo $payment['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="order_id">Order (optional)</label>
                        <select name="order_id" id="order_id" class="form-control">
                            <option value="">Select Order</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['id']; ?>" <?php echo $payment['order_id'] == $order['id'] ? 'selected' : ''; ?>>
                                    Order #<?php echo $order['id']; ?> ($<?php echo $order['total_amount']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" value="<?php echo htmlspecialchars($payment['amount']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="">Select Payment Method</option>
                            <option value="credit_card" <?php echo $payment['payment_method'] == 'credit_card' ? 'selected' : ''; ?>>Credit Card</option>
                            <option value="debit_card" <?php echo $payment['payment_method'] == 'debit_card' ? 'selected' : ''; ?>>Debit Card</option>
                            <option value="paypal" <?php echo $payment['payment_method'] == 'paypal' ? 'selected' : ''; ?>>PayPal</option>
                            <option value="bank_transfer" <?php echo $payment['payment_method'] == 'bank_transfer' ? 'selected' : ''; ?>>Bank Transfer</option>
                            <option value="cash" <?php echo $payment['payment_method'] == 'cash' ? 'selected' : ''; ?>>Cash</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_status">Payment Status</label>
                        <select name="payment_status" id="payment_status" class="form-control" required>
                            <option value="pending" <?php echo $payment['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="completed" <?php echo $payment['payment_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            <option value="failed" <?php echo $payment['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                            <option value="refunded" <?php echo $payment['payment_status'] == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="transaction_id">Transaction ID (optional)</label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control" value="<?php echo htmlspecialchars($payment['transaction_id']); ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-success">Update Payment</button>
                    <a href="payment_details.php?id=<?php echo $payment['id']; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
</body>
</html>
