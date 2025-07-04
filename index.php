<?php
session_start();
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';

// Dynamically construct base URL
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Include the db_config that creates the $pdo connection
require_once __DIR__ . '/database/db_config.php';

$categories = [];
$products = [];
$error = '';

// Group categories for sidebar and navigation
$categoryGroups = [
    'Audio' => ['Audio Plugins', 'Digital Audio Workstations', 'Kontakt Libraries', 'Audio Samples', 'Synth Presets', 'Audio Libraries'],
    'Video/Graphics' => ['Photo Editing Software', 'Video Editing Software', 'Graphic Design Tools', 'Screen Capture & Recorder'],
    'Utilities' => ['Converters', 'Security Tools', 'System Utilities', 'Download Managers', 'Office Tools'],
    'Others' => ['Activators', 'Operating Systems', 'Plugins Tools & Utilities']
];

// Pagination setup
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 9;
$offset = ($page - 1) * $perPage;

// Category filter
$filterCategory = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Build query string for preserving parameters
$queryParams = [];
if ($filterCategory) {
    $queryParams['category'] = $filterCategory;
}
if ($page > 1) {
    $queryParams['page'] = $page;
}
$queryString = $queryParams ? '?' . http_build_query($queryParams) : '';

try {
    // Fetch categories
    $stmt = $pdo->prepare("SELECT id, name, slug FROM categories WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC");
    $stmt->execute();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[$row['id']] = $row;
    }

    // Fetch total products for pagination
    $sql = "SELECT COUNT(*) as total FROM products p WHERE p.is_active = 1 AND p.deleted_at IS NULL";
    $params = [];
    
    if ($filterCategory) {
        $sql .= " AND p.category_id = ?";
        $params[] = $filterCategory;
    }
    
    $totalQuery = $pdo->prepare($sql);
    $totalQuery->execute($params);
    $total = $totalQuery->fetchColumn();
    $totalPages = ceil($total / $perPage);

    // Fetch products with category details
    $query = "
        SELECT p.id, p.category_id, p.name, p.slug, p.description, p.price, p.created_at, c.name AS category_name, c.slug AS category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1 AND p.deleted_at IS NULL
    ";
    
    $params = [];
    if ($filterCategory) {
        $query .= " AND p.category_id = ?";
        $params[] = $filterCategory;
    }
    
    $query .= " ORDER BY p.created_at DESC LIMIT :offset, :perPage";
    $params['offset'] = $offset;
    $params['perPage'] = $perPage;

    $stmt = $pdo->prepare($query);
    
    // Bind parameters
    if ($filterCategory) {
        $stmt->bindParam(1, $filterCategory, PDO::PARAM_INT);
    }
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':perPage', $perPage, PDO::PARAM_INT);
    
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch images for each product
    foreach ($products as &$product) {
        $imgStmt = $pdo->prepare("SELECT value, type FROM medias WHERE products_id = ? AND file_type = 'image' AND deleted_at IS NULL ORDER BY id ASC LIMIT 1");
        $imgStmt->execute([$product['id']]);
        $product['images'] = $imgStmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($product); // Break reference

} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
} catch (Exception $e) {
    $error = 'Error: ' . $e->getMessage();
}

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<!-- Header -->
<?php
require_once 'includes/navbar.php';
?>
<!-- END Header -->


    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h2>Premium Audio Plugins & Kits</h2>
                <p>Discover thousands of professional audio plugins, sample packs, and sound kits for music production at unbeatable prices. Download instantly after purchase.</p>
                <div class="hero-buttons">
                    <a href="<?php echo $baseUrl; ?>products.php" class="btn btn-primary">Browse Products</a>
                    <a href="<?php echo $baseUrl; ?>trending.php" class="btn btn-outline-light">View Deals</a>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Main Content with Sidebar -->
    <div class="container">
        <div class="row">
           
            <!-- Main Content -->
            <div class="col-md-9 col-lg-9 main-content">
                <h2 class="section-title">Featured Products</h2>
                <div class="product-grid">
                    <?php if (empty($products)): ?>
                        <div class="col-12"><p class="text-center text-muted py-5">No products found. Please try another category.</p></div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <?php
                            $imgSrc = !empty($product['images']) ?
                                ($product['images'][0]['type'] === 'url' ? $product['images'][0]['value'] : $one->assets_folder . '/' . $product['images'][0]['value']) :
                                $one->assets_folder . '/media/various/default_product.png';
                            ?>
                            <div class="product-card">
                                <a href="<?php echo $baseUrl; ?>product-details.php?slug=<?php echo urlencode($product['slug']) . $queryString; ?>" class="text-decoration-none text-dark">
                                    <div class="product-img position-relative">
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                        <?php if ($page == 1): ?>
                                            <div class="product-badge">NEW</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="product-body">
                                        <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                        <div class="product-category"><?php echo htmlspecialchars($product['category_name']); ?></div>
                                        <p class="product-description"><?php echo htmlspecialchars(mb_strimwidth($product['description'], 0, 100, '...')); ?></p>
                                        <div class="product-price">TZS <?php echo number_format($product['price'], 0); ?></div>
                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary">Add to Cart</button>
                                            <button class="btn btn-outline-secondary">View Details</button>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav class="mt-5">
                        <ul class="pagination justify-content-center flex-wrap">
                            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $baseUrl; ?>?page=<?php echo $page - 1; ?><?php echo $filterCategory ? '&category=' . $filterCategory : ''; ?>">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            </li>
                            <?php 
                            $startPage = max(1, $page - 2);
                            $endPage = min($totalPages, $page + 2);
                            
                            if ($startPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>?page=1<?php echo $filterCategory ? '&category=' . $filterCategory : ''; ?>">1</a>
                                </li>
                                <?php if ($startPage > 2): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>?page=<?php echo $i; ?><?php echo $filterCategory ? '&category=' . $filterCategory : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($endPage < $totalPages): ?>
                                <?php if ($endPage < $totalPages - 1): ?>
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                <?php endif; ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo $baseUrl; ?>?page=<?php echo $totalPages; ?><?php echo $filterCategory ? '&category=' . $filterCategory : ''; ?>"><?php echo $totalPages; ?></a>
                                </li>
                            <?php endif; ?>
                            
                            <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo $baseUrl; ?>?page=<?php echo $page + 1; ?><?php echo $filterCategory ? '&category=' . $filterCategory : ''; ?>">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Newsletter -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 text-center">
                    <h3 class="mb-3">Subscribe to Our Newsletter</h3>
                    <p class="mb-4">Get exclusive deals, new product alerts, and production tips delivered to your inbox.</p>
                    <form class="row g-2 justify-content-center">
                        <div class="col-md-8">
                            <input type="email" class="form-control form-control-lg" placeholder="Your email address">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">Subscribe</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <h5>About AK23StudioKits</h5>
                    <p>Your premier destination for professional audio plugins, sample packs, and music production tools at competitive prices.</p>
                    <div class="social-icons mt-4">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5>Categories</h5>
                    <ul>
                        <?php foreach ($categories as $cat): ?>
                            <li><a href="<?php echo $baseUrl; ?>category.php?category=<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-md-2 mb-4 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul>
                        <li><a href="<?php echo $baseUrl; ?>">Home</a></li>
                        <li><a href="<?php echo $baseUrl; ?>trending.php">Trending</a></li>
                        <li><a href="<?php echo $baseUrl; ?>new-arrivals.php">New Arrivals</a></li>
                        <li><a href="<?php echo $baseUrl; ?>support.php">Support Us</a></li>
                        <li><a href="<?php echo $baseUrl; ?>requests.php">Requests</a></li>
                    </ul>
                </div>
                
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul>
                        <li><i class="bi bi-envelope me-2"></i> support@ak23studiokits.com</li>
                        <li><i class="bi bi-telephone me-2"></i> +255 123 456 789</li>
                        <li><i class="bi bi-geo-alt me-2"></i> Dar es Salaam, Tanzania</li>
                    </ul>
                    <div class="mt-4">
                        <img src="https://via.placeholder.com/200x60/1a2226/ffffff?text=Secure+Payments" alt="Payment Methods" class="img-fluid">
                    </div>
                </div>
            </div>
            
            <div class="copyright">
                <p>© <?php echo date('Y'); ?> AK23StudioKits. All rights reserved. Designed with <i class="bi bi-heart-fill text-danger"></i></p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });

            // Simulate auto-suggest
            const searchInput = document.querySelector('.search-form input');
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

            // Hero animation
            const heroContent = document.querySelector('.hero-content');
            heroContent.style.opacity = '0';
            heroContent.style.transform = 'translateY(30px)';
            setTimeout(() => {
                heroContent.style.transition = 'opacity 1s ease, transform 1s ease';
                heroContent.style.opacity = '1';
                heroContent.style.transform = 'translateY(0)';
            }, 300);

            // Product card hover effect
            const productCards = document.querySelectorAll('.product-card');
            productCards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.querySelector('.product-img').style.transform = 'scale(1.05)';
                });
                card.addEventListener('mouseleave', () => {
                    card.querySelector('.product-img').style.transform = 'scale(1)';
                });
            });

            // Sidebar toggle animation
            const sidebarToggler = document.querySelector('.navbar-toggler.d-md-none');
            const sidebarContent = document.querySelector('.sidebar-content');
            sidebarToggler.addEventListener('click', () => {
                if (sidebarContent.classList.contains('show')) {
                    sidebarContent.style.transform = 'translateX(-100%)';
                    setTimeout(() => {
                        sidebarContent.classList.remove('show');
                    }, 300);
                } else {
                    sidebarContent.classList.add('show');
                    sidebarContent.style.transform = 'translateX(0)';
                }
            });
        });
    </script>
</body>
</html>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>