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

$products = [];
try {
    $stmt = $pdo->query("SELECT id, name, price FROM products WHERE deleted_at IS NULL");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error fetching products: " . $e->getMessage();
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
            
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, ?)");
            $stmt->execute([$user_id, $total_amount, $status]);
            $order_id = $pdo->lastInsertId();
            
            for ($i = 0; $i < count($product_ids); $i++) {
                $product_id = filter_var($product_ids[$i], FILTER_VALIDATE_INT);
                $quantity = filter_var($quantities[$i], FILTER_VALIDATE_INT);
                if ($product_id && $quantity) {
                    $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $price = $stmt->fetchColumn();
                    
                    $stmt = $pdo->prepare("INSERT INTO order_product (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$order_id, $product_id, $quantity, $price]);
                }
            }
            
            $pdo->commit();
            $_SESSION['success_message'] = "Order created successfully.";
            header('Location: admin_dashboard.php');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $_SESSION['error_message'] = "Error creating order: " . $e->getMessage();
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
    <title>Create Order - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>Create New Order</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Order Information</h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="create_order.php">
                    <div class="form-group">
                        <label for="user_id">Customer</label>
                        <select name="user_id" id="user_id" class="form-control" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($users as $user): ?>
                                <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="product-section" class="form-group">
                        <label>Products</label>
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
                    </div>
                    
                    <button type="button" id="add-product" class="btn btn-primary mb-3">Add Another Product</button>
                    
                    <div class="form-group">
                        <label for="total_amount">Total Amount</label>
                        <input type="number" name="total_amount" id="total_amount" class="form-control" step="0.01" required readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn btn-success">Create Order</button>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
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
