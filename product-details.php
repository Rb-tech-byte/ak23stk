 <?php
session_start();
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';

// Dynamically construct base URL
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Database connection
require_once __DIR__ . '/database/db_config.php';

// Get product slug from URL
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$product = null;
$categories = [];
$recommended = [];
$trending = [];
$error = '';
$hasPaid = false;

// Initialize variables
$product = null;
$recommended = [];
$trending = [];
$error = '';
$hasPaid = false;
$productImages = [];

// Category groups for navigation
$categoryGroups = [
    'Audio' => ['Audio Plugins', 'Digital Audio Workstations', 'Kontakt Libraries', 'Audio Samples', 'Synth Presets', 'Audio Libraries'],
    'Video/Graphics' => ['Photo Editing Software', 'Video Editing Software', 'Graphic Design Tools', 'Screen Capture & Recorder'],
    'Utilities' => ['Converters', 'Security Tools', 'System Utilities', 'Download Managers', 'Office Tools'],
    'Others' => ['Activators', 'Operating Systems', 'Plugins Tools & Utilities']
];

// Fetch categories for navigation
try {
    $stmt = $pdo->query("SELECT id, name, slug FROM categories WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('Error fetching categories: ' . $e->getMessage());
    $categories = [];
}

// Fetch product details
if ($slug) {
    try {
        // Get product details
        $stmt = $pdo->prepare("
            SELECT p.*, c.name AS category_name, c.slug AS category_slug 
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.slug = ? AND p.is_active = 1 AND p.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            // Check if user has paid for this product
            if (isset($_SESSION['user_id'])) {
                $stmt = $pdo->prepare("
                    SELECT o.id 
                    FROM orders o
                    JOIN payments p ON o.id = p.order_id
                    WHERE o.product_id = ? AND o.user_id = ? AND p.status = 'completed'
                    LIMIT 1
                ");
                $stmt->execute([$product['id'], $_SESSION['user_id']]);
                $hasPaid = $stmt->rowCount() > 0;
            }
            
            // Fetch product images
            $stmt = $pdo->prepare("
                SELECT value, type 
                FROM medias 
                WHERE products_id = ? AND file_type = 'image' AND deleted_at IS NULL
            ");
            $stmt->execute([$product['id']]);
            $product['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Fetch recommended products
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.slug, p.price, p.images,
                       c.name AS category_name, c.slug AS category_slug 
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.category_id = ? AND p.id != ? AND p.is_active = 1
                ORDER BY p.created_at DESC LIMIT 3
            ");
            $stmt->execute([$product['category_id'], $product['id']]);
            $recommended = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add images to recommended products
            foreach ($recommended as &$rec) {
                $stmt = $pdo->prepare("
                    SELECT value, type 
                    FROM medias 
                    WHERE products_id = ? AND file_type = 'image' 
                    ORDER BY id ASC LIMIT 1
                ");
                $stmt->execute([$rec['id']]);
                $rec['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            // Fetch trending products
            $stmt = $pdo->prepare("
                SELECT p.id, p.name, p.slug, p.price, p.images,
                       c.name AS category_name, c.slug AS category_slug 
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1
                ORDER BY p.created_at DESC LIMIT 3
            ");
            $stmt->execute();
            $trending = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add images to trending products
            foreach ($trending as &$trend) {
                $stmt = $pdo->prepare("
                    SELECT value, type 
                    FROM medias 
                    WHERE products_id = ? AND file_type = 'image' 
                    ORDER BY id ASC LIMIT 1
                ");
                $stmt->execute([$trend['id']]);
                $trend['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }
    } catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
}

// Base URL is defined in config.php

require 'inc/_global/views/head_end.php';
?>

<body>
    <?php 
    // Include navbar
    require_once 'includes/navbar.php';
    
    // Start main content
    require 'inc/_global/views/page_start.php';
    ?>

    <div class="container mt-4 mb-5">
    <?php if ($error): ?>
        <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
    <?php elseif (!$product): ?>
        <div class="alert alert-warning text-center">
            Product not found. <?= $slug ? 'Slug: ' . htmlspecialchars($slug) : '' ?>
        </div>
    <?php else: ?>
        <!-- Breadcrumb -->
        <nav class="breadcrumb mb-4">
            <a class="breadcrumb-item" href="<?= $baseUrl ?>">Home</a>
            <a class="breadcrumb-item" href="<?= $baseUrl ?>category.php?slug=<?= urlencode($product['category_slug']) ?>">
                <?= htmlspecialchars($product['category_name']) ?>
            </a>
            <span class="breadcrumb-item active"><?= htmlspecialchars($product['name']) ?></span>
        </nav>

        <div class="row g-4">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Title & Metadata -->
                <div class="block block-rounded mb-4">
                    <div class="block-header block-header-default">
                        <h1 class="block-title"><?= htmlspecialchars($product['name']) ?></h1>
                    </div>
                    <div class="block-content">
                        <ul class="list-unstyled text-sm text-muted">
                            <li><strong>Release:</strong> <?= date('M d, Y', strtotime($product['created_at'])) ?></li>
                            <li><strong>Category:</strong> 
                                <a href="<?= $baseUrl ?>category.php?slug=<?= urlencode($product['category_slug']) ?>" 
                                   class="text-primary">
                                    <?= htmlspecialchars($product['category_name']) ?>
                                </a>
                            </li>
                            <li><strong>Price:</strong> TZS <?= number_format($product['price'], 0) ?></li>
                            <li><strong>File Size:</strong> <?= htmlspecialchars($product['file_size'] ?? '120 MB') ?></li>
                            <li><strong>Password:</strong> 
                                <span class="font-mono bg-light px-2 rounded">
                                    <?= htmlspecialchars($product['password_hint'] ?? 'ak23studiokits.com') ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Download Links -->
                <div class="block block-rounded mb-4">
                    <div class="block-header block-header-default">
                        <h2 class="block-title">Download</h2>
                    </div>
                    <div class="block-content">
                        <div class="alert alert-warning d-flex align-items-center justify-content-between">
                            <div>
                                <span class="font-bold text-yellow-700 text-lg">Download Now</span>
                                <span class="ms-2 text-gray-700">
                                    <?= htmlspecialchars($product['file_size'] ?? '120 MB') ?>, 
                                    Google Drive, MediaFire, Box
                                </span>
                            </div>
                            <?php if ($hasPaid): ?>
                                <a href="<?= $baseUrl ?>download.php?product_id=<?= $product['id'] ?>" 
                                   class="btn btn-success animate-pulse">
                                   Download Now
                                </a>
                            <?php else: ?>
                                <button type="button" class="btn btn-primary" 
                                        data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    Pay & Unlock Download
                                </button>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2 text-sm text-muted">
                            <i class="bi bi-lock me-1"></i> 
                            Download links are available after payment
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="block block-rounded mb-4">
                    <div class="block-header block-header-default">
                        <h2 class="block-title">Description</h2>
                    </div>
                    <div class="block-content">
                        <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="col-lg-4">
                <!-- Visual Section -->
                <div class="block block-rounded mb-4">
                    <div class="block-content text-center">
                        <?php if (!empty($product['images'])): ?>
                            <?php
                            $mainImg = $product['images'][0]['type'] === 'url' 
                                ? $product['images'][0]['value'] 
                                : $one->assets_folder . '/' . $product['images'][0]['value'];
                            ?>
                            <img src="<?= htmlspecialchars($mainImg) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 class="img-fluid mb-3" style="max-height: 300px; object-fit: cover;">
                            <div class="d-flex gap-2 justify-content-center flex-wrap">
                                <?php foreach ($product['images'] as $image): ?>
                                    <?php
                                    $imgUrl = $image['type'] === 'url' 
                                        ? htmlspecialchars($image['value']) 
                                        : $one->assets_folder . '/' . htmlspecialchars($image['value']);
                                    ?>
                                    <img src="<?= $imgUrl ?>" class="rounded" 
                                         alt="Product Image" 
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="bg-light d-flex align-items-center justify-content-center" 
                                 style="height: 200px;">
                                <span class="text-muted">No image available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recommended Products -->
                <div class="block block-rounded mb-4">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Recommended for You</h3>
                    </div>
                    <div class="block-content">
                        <?php if (empty($recommended)): ?>
                            <p class="text-muted">No recommended products found.</p>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php foreach ($recommended as $rec): ?>
                                    <?php
                                    $recImg = !empty($rec['images']) 
                                        ? ($rec['images'][0]['type'] === 'url' 
                                            ? $rec['images'][0]['value'] 
                                            : $one->assets_folder . '/' . $rec['images'][0]['value']) 
                                        : $one->assets_folder . '/media/various/default_product.png';
                                    ?>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($recImg) ?>" 
                                             class="rounded" 
                                             alt="<?= htmlspecialchars($rec['name']) ?>" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                        <div>
                                            <div class="font-semibold text-sm">
                                                <a href="<?= $baseUrl ?>product-details.php?slug=<?= urlencode($rec['slug']) ?>" 
                                                   class="text-decoration-none text-dark">
                                                    <?= htmlspecialchars($rec['name']) ?>
                                                </a>
                                            </div>
                                            <div class="text-xs text-muted">
                                                TZS <?= number_format($rec['price'], 0) ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Trending Today -->
                <div class="block block-rounded">
                    <div class="block-header block-header-default">
                        <h3 class="block-title">Trending Today</h3>
                    </div>
                    <div class="block-content">
                        <?php if (empty($trending)): ?>
                            <p class="text-muted">No trending products found.</p>
                        <?php else: ?>
                            <div class="space-y-2">
                                <?php foreach ($trending as $trend): ?>
                                    <?php
                                    $trendImg = !empty($trend['images']) 
                                        ? ($trend['images'][0]['type'] === 'url' 
                                            ? $trend['images'][0]['value'] 
                                            : $one->assets_folder . '/' . $trend['images'][0]['value']) 
                                        : $one->assets_folder . '/media/various/default_product.png';
                                    ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= htmlspecialchars($trendImg) ?>" 
                                             class="rounded" 
                                             alt="<?= htmlspecialchars($trend['name']) ?>" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                        <a href="<?= $baseUrl ?>product-details.php?slug=<?= urlencode($trend['slug']) ?>" 
                                           class="text-sm text-decoration-none text-dark">
                                            <?= htmlspecialchars($trend['name']) ?>
                                        </a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Payment Modal -->
        <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content block block-rounded">
                    <div class="block-header block-header-default">
                        <h2 class="block-title" id="paymentModalLabel">Complete Payment</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="paymentForm" method="POST" action="<?= $baseUrl ?>init-payment.php">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="block-content">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" name="email" id="email" class="form-control" 
                                       placeholder="your@email.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" name="phone" id="phone" class="form-control" 
                                       placeholder="255xxxxxxxxx" required
                                       pattern="^255\d{9}$">
                                <small class="form-text text-muted">Format: 255xxxxxxxxx</small>
                            </div>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                You'll be redirected to Pesapal to complete payment
                            </div>
                        </div>
                        <div class="block-content block-content-full bg-body-light text-end">
                            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Pay with Pesapal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Handle payment form submission
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const email = document.getElementById('email').value;
            const phone = document.getElementById('phone').value;
            
            if (!email || !phone) {
                e.preventDefault();
                Swal.fire('Error', 'Please fill all required fields', 'error');
                return;
            }
            
            // Show processing message
            Swal.fire({
                title: 'Processing Payment',
                text: 'Please wait while we redirect to Pesapal',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        });
    }
});
</script>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>'