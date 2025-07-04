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
$users = [];
$products = [];

if ($id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($order) {
            $stmt = $pdo->prepare("SELECT op.*, p.name as product_name FROM order_product op LEFT JOIN products p ON op.product_id = p.id WHERE op.order_id = ?");
            $stmt->execute([$id]);
            $order_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        $stmt = $pdo->query("SELECT id, name FROM users WHERE deleted_at IS NULL");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = $pdo->query("SELECT id, name, price FROM products WHERE deleted_at IS NULL");
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching data: " . $e->getMessage();
    }
}

if (!$order) {
    $_SESSION['error_message'] = "Order not found or has been deleted.";
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);
    $product_ids = $_POST['product_ids'] ?? [];
    $quantities = $_POST['quantities'] ?? [];
    $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_VALIDATE_FLOAT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);
    
    if ($user_id && !empty($product_ids) && !empty($quantities) && $total_amount) {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("UPDATE orders SET user_id = ?, total_amount = ?, status = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$user_id, $total_amount, $status, $id]);
            
            $stmt = $pdo->prepare("DELETE FROM order_product WHERE order_id = ?");
            $stmt->execute([$id]);
            
            for ($i = 0; $i < count($product_ids); $i++) {
                $product_id = filter_var($product_ids[$i], FILTER_VALIDATE_INT);
                $quantity = filter_var($quantities[$i], FILTER_VALIDATE_INT);
                if ($product_id && $quantity) {
                    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $price = $stmt->fetchColumn();
                    
                    $stmt = $pdo->prepare("INSERT INTO order_product (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$id, $product_id, $quantity, $price]);
                }
            }
            
            $pdo->commit();
            $_SESSION['success_message'] = "Order updated successfully.";
            header('Location: order_details.php?id=' . $id);
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Error updating order: " . $e->getMessage();
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
    // Initialize $one if not already set
    if (!isset($one)) {
        $one = new stdClass();
        $one->assets_folder = 'assets';
        $one->theme = 'default';
    }
    require 'inc/_global/views/head_start.php';
    ?>
    <title>Edit Order - Admin Dashboard</title>
    <?php require 'inc/_global/views/head_end.php'; ?>
</head>
<body>
    <?php 
    include 'sidebar_start.php';
    require 'inc/_global/views/page_start.php';
    ?>
    
    <div class="content">
        <h1>Edit Order</h1>
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
                
                <form method="POST" action="edit_order.php?id=<?php echo $order['id']; ?>">
                    <div class="form-group">
                        <label for="user_id">Customer</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>" <?php echo $order['user_id'] == $user['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="product-section" class="form-group">
                        <label>Products</label>
                        <?php if (empty($order_products)): ?>
                            <div class="product-row mb-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <select name="product_ids[]" class="form-control product-select" required>
                                            <option value="">Select Product</option>
                                            <?php foreach ($products as $product): ?>
                                                <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                                                    <?php echo htmlspecialchars($product['name']); ?> ($<?php echo $product['price']; ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="number" name="quantities[]" class="form-control quantity-input" placeholder="Quantity" min="1" value="1" required>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-remove-product">Remove</button>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($order_products as $op): ?>
                                <div class="product-row mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <select name="product_ids[]" class="form-control product-select" required>
                                                <option value="">Select Product</option>
                                                <?php foreach ($products as $product): ?>
                                                    <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>" <?php echo $op['product_id'] == $product['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($product['name']); ?> ($<?php echo $product['price']; ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" name="quantities[]" class="form-control quantity-input" placeholder="Quantity" min="1" value="<?php echo $op['quantity']; ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-remove-product">Remove</button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <button type="button" id="add-product" class="btn btn-primary mb-3">Add Another Product</button>
                    
                    <div class="form-group">
                        <label for="total_amount">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount" class="form-control" step="0.01" value="<?php echo $order['total_amount']; ?>" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Update Order</button>
                    <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addProductBtn = document.getElementById('add-product');
            const productSection = document.getElementById('product-section');
            const totalAmountInput = document.getElementById('total_amount');
            
            addProductBtn.addEventListener('click', function() {
                const productRow = document.createElement('div');
                productRow.className = 'product-row mb-3';
                productRow.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <select name="product_ids[]" class="form-control product-select" required>
                                <option value="">Select Product</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                                        <?php echo htmlspecialchars($product['name']); ?> ($<?php echo $product['price']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="quantities[]" class="form-control quantity-input" placeholder="Quantity" min="1" value="1" required>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-danger btn-remove-product">Remove</button>
                        </div>
                    </div>
                `;
                productSection.appendChild(productRow);
                calculateTotal();
            });
            
            productSection.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-product')) {
                    const productRows = document.querySelectorAll('.product-row');
                    if (productRows.length > 1) {
                        e.target.closest('.product-row').remove();
                        calculateTotal();
                    }
                }
            });
            
            productSection.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity-input')) {
                    calculateTotal();
                }
            });
            
            function calculateTotal() {
                let total = 0;
                const productRows = document.querySelectorAll('.product-row');
                productRows.forEach(row => {
                    const select = row.querySelector('.product-select');
                    const quantityInput = row.querySelector('.quantity-input');
                    if (select.value && quantityInput.value) {
                        const price = parseFloat(select.options[select.selectedIndex].dataset.price);
                        const quantity = parseInt(quantityInput.value);
                        total += price * quantity;
                    }
                });
                totalAmountInput.value = total.toFixed(2);
            }
            
            // Initial calculation
            calculateTotal();
        });
    </script>
</body>
</html>
