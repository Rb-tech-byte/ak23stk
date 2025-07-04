<?php
session_start();
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';

// Dynamically construct base URL
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Include the db_config that creates the $pdo connection
require_once __DIR__ . '/database/db_config.php';

$product = null;
$relatedProducts = [];
$error = '';
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

try {
    // Fetch product details
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.slug, p.description, p.price, p.created_at, c.name AS category_name, c.slug AS category_slug
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.slug = ? AND p.is_active = 1 AND p.deleted_at IS NULL
    ");
    $stmt->execute([$slug]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        $error = 'Product not found.';
    } else {
        // Fetch product images
        $imgStmt = $pdo->prepare("SELECT value, type FROM medias WHERE products_id = ? AND file_type = 'image' AND deleted_at IS NULL ORDER BY id ASC");
        $imgStmt->execute([$product['id']]);
        $product['images'] = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch related products (same category, excluding current product)
        $relatedStmt = $pdo->prepare("
            SELECT p.id, p.name, p.slug, p.description, p.price, c.name AS category_name, c.slug AS category_slug
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1 AND p.deleted_at IS NULL
            LIMIT 3
        ");
        $relatedStmt->execute([$product['category_id'], $product['id']]);
        $relatedProducts = $relatedStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($relatedProducts as &$related) {
            $imgStmt = $pdo->prepare("SELECT value, type FROM medias WHERE products_id = ? AND file_type = 'image' AND deleted_at IS NULL ORDER BY id ASC LIMIT 1");
            $imgStmt->execute([$related['id']]);
            $related['images'] = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($related);
    }
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<!-- Link to updated CSS -->
<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">
<script src="<?php echo $one->assets_folder; ?>/js/plugins/sweetalert2/sweetalert2.min.js"></script>

<!-- Header (same as index.php) -->
<header class="bg-dark py-3">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
            <a href="<?php echo $baseUrl; ?>" class="navbar-brand d-flex align-items-center">
                <img src="https://ak23studiokits.com/wp-content/uploads/2025/06/cropped-ak2.png" alt="Logo" height="40" class="me-2">
                <div class="text-white">
                    <h5 class="mb-0 fw-bold">Download audio plugins and kits</h5>
                </div>
            </a>
            <form method="get" class="search-wrapper d-flex" action="<?php echo $baseUrl; ?>search.php">
                <div class="input-group position-relative">
                    <input type="text" name="q" class="form-control" placeholder="Search products..." required>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    <div class="dropdown-menu autosuggest" style="display: none;"></div>
                </div>
            </form>
        </div>
        <nav class="navbar navbar-expand-lg navbar-dark mt-2">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto text-uppercase fw-bold">
                    <li class="nav-item"><a class="nav-link text-white" href="<?php echo $baseUrl; ?>">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?php echo $baseUrl; ?>category/vst-plugins">VST Plugins</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?php echo $baseUrl; ?>category/software">Software</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?php echo $baseUrl; ?>category/macos">MacOS</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?php echo $baseUrl; ?>how-to-download.php">How to Download</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="<?php echo $baseUrl; ?>contact.php">Contact</a></li>
                </ul>
            </div>
        </nav>
    </div>
</header>
<!-- END Header -->

<!-- Main Content with Sidebar -->
<div class="container">
    <div class="row">
        <!-- Sidebar (same as index.php) -->
        <div class="col-md-3 col-lg-3 sidebar">
            <button class="navbar-toggler d-md-none mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="sidebar-content collapse d-md-block" id="sidebarMenu">
                <div class="card mb-3">
                    <div class="card-header">Categories</div>
                    <div class="card-body p-0">
                        <ul class="list-unstyled p-3">
                            <?php foreach ($categoryGroups as $groupName => $groupCategories): ?>
                                <?php
                                $validCategories = array_filter($groupCategories, function($catName) use ($categories) {
                                    foreach ($categories as $cat) {
                                        if ($cat['name'] === $catName) {
                                            return true;
                                        }
                                    }
                                    return false;
                                });
                                if (!empty($validCategories)):
                                ?>
                                    <li class="mb-2">
                                        <h6><?php echo htmlspecialchars($groupName); ?></h6>
                                        <ul class="list-unstyled ps-3">
                                            <?php foreach ($validCategories as $catName):
                                                foreach ($categories as $cat):
                                                    if ($cat['name'] === $catName): ?>
                                                        <li><a href="<?php echo $baseUrl; ?>category.php?slug=<?php echo urlencode($cat['slug']); ?>" class="text-decoration-none"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                                                    <?php endif;
                                                endforeach;
                                            endforeach; ?>
                                        </ul>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                <div class="card mb-3">
                    <div class="card-header">Popular Posts</div>
                    <div class="card-body p-0">
                        <ul class="list-unstyled p-3">
                            <li><a href="#" class="text-decoration-none">Top Audio Plugin 2025</a></li>
                            <li><a href="#" class="text-decoration-none">Best Video Editing Software</a></li>
                            <li><a href="#" class="text-decoration-none">Must-Have Utilities</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Tags</div>
                    <div class="card-body">
                        <div class="tags-cloud">
                            <a href="#" class="badge bg-primary">Audio</a>
                            <a href="#" class="badge bg-primary">VST</a>
                            <a href="#" class="badge bg-primary">Software</a>
                            <a href="#" class="badge bg-primary">MacOS</a>
                            <a href="#" class="badge bg-primary">Utilities</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-9 main-content">
            <div class="my-4">
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($product): ?>
                    <h1 class="display-4 fw-bold mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <p class="text-muted small mb-0">Posted on: <?php echo date('M d, Y', strtotime($product['created_at'])); ?></p>
                            <p class="text-muted small">Category: <a href="<?php echo $baseUrl; ?>category.php?slug=<?php echo urlencode($product['category_slug']); ?>"><?php echo htmlspecialchars($product['category_name']); ?></a></p>
                        </div>
                        <div class="social-share">
                            <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-twitter"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-linkedin"></i></a>
                        </div>
                    </div>
                    <?php
                    $imgSrc = !empty($product['images']) ?
                        ($product['images'][0]['type'] === 'url' ? $product['images'][0]['value'] : $one->assets_folder . '/' . $product['images'][0]['value']) :
                        $one->assets_folder . '/media/various/default_product.png';
                    ?>
                    <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="img-fluid rounded mb-3" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                    <p class="fw-bold">Price: TZS <?php echo number_format($product['price'], 0); ?></p>
                    <a href="#" class="btn btn-primary mb-3">Download Now</a>

                    <!-- Related Posts -->
                    <?php if (!empty($relatedProducts)): ?>
                        <h3 class="mt-5 mb-3">Related Posts</h3>
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                            <?php foreach ($relatedProducts as $related): ?>
                                <?php
                                $relatedImgSrc = !empty($related['images']) ?
                                    ($related['images'][0]['type'] === 'url' ? $related['images'][0]['value'] : $one->assets_folder . '/' . $related['images'][0]['value']) :
                                    $one->assets_folder . '/media/various/default_product.png';
                                ?>
                                <div class="col">
                                    <div class="card border-0 h-100">
                                        <a href="<?php echo $baseUrl; ?>product.php?slug=<?php echo urlencode($related['slug']); ?>" class="text-decoration-none text-dark">
                                            <img src="<?php echo htmlspecialchars($relatedImgSrc); ?>" class="card-img-top rounded" alt="<?php echo htmlspecialchars($related['name']); ?>">
                                            <div class="card-body">
                                                <h6 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h6>
                                                <p class="card-text small text-muted"><?php echo htmlspecialchars(mb_strimwidth($related['description'], 0, 80, '...')); ?></p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Footer (same as index.php) -->
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4 mb-3">
                <h5>Categories</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $baseUrl; ?>category/audio" class="text-white">Audio</a></li>
                    <li><a href="<?php echo $baseUrl; ?>category/video-graphics" class="text-white">Video/Graphics</a></li>
                    <li><a href="<?php echo $baseUrl; ?>category/utilities" class="text-white">Utilities</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5>Support</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $baseUrl; ?>how-to-download.php" class="text-white">How to Download</a></li>
                    <li><a href="<?php echo $baseUrl; ?>contact.php" class="text-white">Contact Us</a></li>
                    <li><a href="<?php echo $baseUrl; ?>support.php" class="text-white">Support Us</a></li>
                </ul>
            </div>
            <div class="col-md-4 mb-3">
                <h5>About</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $baseUrl; ?>about.php" class="text-white">About Us</a></li>
                    <li><a href="<?php echo $baseUrl; ?>requests.php" class="text-white">Requests</a></li>
                </ul>
            </div>
        </div>
        <div class="text-center mt-3">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> AK23StudioKits. All rights reserved.</p>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Simulate auto-suggest
    const searchInput = document.querySelector('.search-wrapper input');
    const autosuggest = document.querySelector('.autosuggest');
    searchInput.addEventListener('input', function() {
        const suggestions = ['Audio Plugin', 'Video Software', 'Utility Tool'];
        if (this.value.length > 0) {
            autosuggest.innerHTML = suggestions.map(s => `<a class="dropdown-item" href="${baseUrl}search.php?q=${encodeURIComponent(s)}">${s}</a>`).join('');
            autosuggest.style.display = 'block';
        } else {
            autosuggest.style.display = 'none';
        }
    });
});
</script>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>