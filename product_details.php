<?php
session_start();
require_once __DIR__ . '/database/db_config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit;
}

$slug = filter_input(INPUT_GET, 'slug', FILTER_SANITIZE_STRING);
$product = null;

if ($slug) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.slug = ? AND p.deleted_at IS NULL");
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error fetching product details: " . $e->getMessage();
    }
}

if (!$product) {
    $_SESSION['error_message'] = "Product not found or has been deleted.";
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
    <title>Product Details - Admin Dashboard</title>
</head>
<body>
    <?php include 'sidebar_start.php'; ?>
    
    <div class="content">
        <h1>Product Details</h1>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Product: <?php echo htmlspecialchars($product['name']); ?></h6>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-danger">
                        <?php echo htmlspecialchars($_SESSION['error_message']); unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6">
                        <?php if ($product['image_url']): ?>
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="img-fluid mb-3" style="max-height: 300px;">
                        <?php else: ?>
                            <div class="alert alert-info">No image available</div>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6">
                        <h5>Product Information</h5>
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($product['name'] ?? 'N/A'); ?></p>
                        <p><strong>Slug:</strong> <?php echo htmlspecialchars($product['slug'] ?? 'N/A'); ?></p>
                        <p><strong>Price:</strong> $<?php echo number_format($product['price'] ?? 0, 2); ?></p>
                        <p><strong>Stock Quantity:</strong> <?php echo htmlspecialchars($product['stock_quantity'] ?? 'N/A'); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($product['category_name'] ?? 'N/A'); ?></p>
                        <p><strong>Created At:</strong> <?php echo htmlspecialchars($product['created_at'] ?? 'N/A'); ?></p>
                        <p><strong>Updated At:</strong> <?php echo htmlspecialchars($product['updated_at'] ?? 'N/A'); ?></p>
                    </div>
                </div>
                
                <hr>
                
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($product['description'] ?? 'No description available.')); ?></p>
                
                <div class="mt-3">
                    <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning" data-toggle="tooltip" title="Edit Product"><i class="bi bi-pencil"></i> Edit</a>
                    <a href="admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'sidebar_end.php'; ?>
</body>
</html>
