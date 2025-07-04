<?php
session_start();
require_once __DIR__ . '/database/db_config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$users = [];
try {
    $stmt = $pdo->query("SELECT id, name FROM users WHERE deleted_at IS NULL");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error fetching users: " . $e->getMessage();
}

$orders = [];
try {
    $stmt = $pdo->query("SELECT id, total_amount FROM orders WHERE deleted_at IS NULL");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error fetching orders: " . $e->getMessage();
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
            $stmt = $pdo->prepare("INSERT INTO payments (user_id, order_id, amount, payment_method, payment_status, transaction_id) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $order_id ?: null, $amount, $payment_method, $payment_status, $transaction_id]);
            $_SESSION['success_message'] = "Payment created successfully.";
            header('Location: admin_dashboard.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Error creating payment: " . $e->getMessage();
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
    <title>Create Payment - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>Create New Payment</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="create_payment.php">
                    <div class="form-group">
                        <label for="user_id">Customer</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="order_id">Order (optional)</label>
                        <select name="order_id" id="order_id" class="form-control">
                            <option value="">Select Order</option>
                            <?php foreach ($orders as $order): ?>
                                <option value="<?php echo $order['id']; ?>">Order #<?php echo $order['id']; ?> ($<?php echo $order['total_amount']; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="amount">Amount ($)</label>
                        <input type="number" name="amount" id="amount" class="form-control" step="0.01" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select name="payment_method" id="payment_method" class="form-control" required>
                            <option value="">Select Payment Method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="debit_card">Debit Card</option>
                            <option value="paypal">PayPal</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cash">Cash</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="payment_status">Payment Status</label>
                        <select name="payment_status" id="payment_status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="completed">Completed</option>
                            <option value="failed">Failed</option>
                            <option value="refunded">Refunded</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="transaction_id">Transaction ID (optional)</label>
                        <input type="text" name="transaction_id" id="transaction_id" class="form-control">
                    </div>
                    
                    <button type="submit" class="btn btn-success">Create Payment</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
</body>
</html>
