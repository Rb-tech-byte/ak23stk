<?php
session_start();
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';
require_once __DIR__ . '/database/db_config.php';

$pageTitle = 'Trending Products';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 12;
$offset = ($page - 1) * $perPage;

try {
    // Get total count of trending products
    $countStmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_trending = 1 AND is_active = 1 AND deleted_at IS NULL");
    $totalProducts = $countStmt->fetchColumn();
    $totalPages = ceil($totalProducts / $perPage);

    // Get trending products with pagination
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name 
                          FROM products p 
                          LEFT JOIN categories c ON p.category_id = c.id 
                          WHERE p.is_trending = 1 AND p.is_active = 1 AND p.deleted_at IS NULL 
                          ORDER BY p.views DESC, p.created_at DESC 
                          LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $products = [];
    $totalPages = 0;
}

require 'includes/header.php';
?>

<!-- Page Content -->
<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title">Trending Products <small>Most popular downloads this week</small></h3>
        </div>
        <div class="block-content">
            <?php if (!empty($products)): ?>
                <div class="row items-push">
                    <?php foreach ($products as $product): ?>
                        <div class="col-md-6 col-xl-4">
                            <div class="block block-rounded block-link-pop">
                                <div class="block-content block-content-full text-center">
                                    <div class="item item-2x item-circle bg-gray-lighter text-primary mx-auto mb-3">
                                        <i class="fa fa-fire"></i>
                                    </div>
                                    <div class="font-w600 mb-1"><?php echo htmlspecialchars($product['name']); ?></div>
                                    <div class="text-muted mb-3"><?php echo htmlspecialchars($product['category_name']); ?></div>
                                    <a class="btn btn-sm btn-primary" href="product.php?id=<?php echo $product['id']; ?>">
                                        <i class="fa fa-download mr-1"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            <?php else: ?>
                <div class="alert alert-info">No trending products found at the moment. Check back later!</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- END Page Content -->

<?php 
require 'includes/footer.php';
require 'inc/_global/views/page_end.php';
?>
