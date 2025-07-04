<?php
session_start();
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';
require_once __DIR__ . '/database/db_config.php';

// Redirect to login if user is not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = 'wishlist.php';
    header('Location: login.php');
    exit();
}

$pageTitle = 'My Wishlist';
// Using BASE_URL constant instead of $baseUrl

$userId = $_SESSION['user_id'];
$wishlist = [];
$message = '';

// Handle remove from wishlist
if (isset($_GET['remove']) && is_numeric($_GET['remove'])) {
    $productId = intval($_GET['remove']);
    try {
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        $message = '<div class="alert alert-success">Item removed from your wishlist.</div>';
    } catch (PDOException $e) {
        error_log("Error removing from wishlist: " . $e->getMessage());
        $message = '<div class="alert alert-danger">Error removing item. Please try again.</div>';
    }
}

try {
    // Get wishlist items
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM wishlist w 
                          JOIN products p ON w.product_id = p.id 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE w.user_id = ? AND p.is_active = 1 AND p.deleted_at IS NULL 
                          ORDER BY w.created_at DESC");
    $stmt->execute([$userId]);
    $wishlist = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $message = '<div class="alert alert-danger">Error loading wishlist. Please try again later.</div>';
}

require 'includes/header.php';
?>

<!-- Page Content -->
<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title">My Wishlist</h3>
            <div class="block-options">
                <a href="products.php" class="btn btn-sm btn-primary">
                    <i class="fa fa-arrow-left mr-1"></i> Continue Shopping
                </a>
            </div>
        </div>
        <div class="block-content">
            <?php echo $message; ?>
            
            <?php if (!empty($wishlist)): ?>
                <div class="table-responsive">
                    <table class="table table-hover table-vcenter">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th class="text-center" style="width: 100px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($wishlist as $item): ?>
                                <tr>
                                    <td>
                                        <a class="font-w600" href="product.php?id=<?php echo $item['id']; ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['category_name']); ?></td>
                                    <td>
                                        <?php if ($item['price'] > 0): ?>
                                            $<?php echo number_format($item['price'], 2); ?>
                                        <?php else: ?>
                                            <span class="text-success">Free</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group">
                                            <a href="product.php?id=<?php echo $item['id']; ?>" class="btn btn-sm btn-primary" data-toggle="tooltip" title="View">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="wishlist.php?remove=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" data-toggle="tooltip" title="Remove" onclick="return confirm('Are you sure you want to remove this item from your wishlist?');">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <div class="mb-3">
                        <i class="fa fa-heart fa-4x text-muted"></i>
                    </div>
                    <h4>Your wishlist is empty</h4>
                    <p class="text-muted">You haven't added any products to your wishlist yet.</p>
                    <a href="products.php" class="btn btn-primary">
                        <i class="fa fa-shopping-bag mr-1"></i> Browse Products
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- END Page Content -->

<?php 
require 'includes/footer.php';
require 'inc/_global/views/page_end.php';
?>
