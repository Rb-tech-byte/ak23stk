<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
//require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$order = null;
$order_products = [];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            // Attempt to fetch order products, but handle if table doesn't exist
            try {
                $stmt = $pdo->prepare("SELECT op.*, p.name as product_name FROM order_product op LEFT JOIN products p ON op.product_id = p.id WHERE op.order_id = ?");
                $stmt->execute([$id]);
                $order_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $order_products = [];
                $_SESSION['error_message'] = "Error fetching order products: " . $e->getMessage();
            }
            
            $stmt = $pdo->prepare("SELECT name FROM users WHERE id = ? AND deleted_at IS NULL");
            $stmt->execute([$order['user_id']]);
            $customer_name = $stmt->fetchColumn() ?: 'Unknown';
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching order details: " . $e->getMessage();
    }
}

if (!$order) {
    $_SESSION['error_message'] = "Order not found or has been deleted.";
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
require 'inc/_global/views/head_start.php';
require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
// --- Layout and Data Fetch ---
    ?>
    <title>Order Details - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>Order Details</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order #<?php echo $order['id']; ?></h6>
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
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
                        <p><strong>User ID:</strong> <?php echo htmlspecialchars($order['user_id'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-md-6">
                        <h5>Order Information</h5>
                        <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status'] ?? 'N/A'); ?></p>
                        <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'] ?? 0, 2); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($order['created_at'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <hr>
                
                <h5>Products in this Order</h5>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($order_products)): ?>
                                <tr>
                                    <td colspan="4" class="text-center">No products found for this order.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($order_products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['product_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($product['quantity'] ?? 'N/A'); ?></td>
                                        <td>$<?php echo number_format($product['price'] ?? 0, 2); ?></td>
                                        <td>$<?php echo number_format(($product['price'] ?? 0) * ($product['quantity'] ?? 0), 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3">
                    <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn btn-warning" data-toggle="tooltip" title="Edit Order"><i class="bi bi-pencil"></i> Edit</a>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
</body>
</html>
