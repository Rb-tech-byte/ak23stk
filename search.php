<?php
// Start session and include required files
session_start();
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';
require_once __DIR__ . '/database/db_config.php';

// Set page title and base URL
$pageTitle = 'Search Results';
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . 
           $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Initialize variables
$searchQuery = '';
$categoryFilter = 0;
$sort = 'relevance';
$page = 1;
$perPage = 12;
$products = [];
$totalResults = 0;
$categories = [];
$categoryName = 'All Categories';
$error = '';

// Validate and sanitize input
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $searchQuery = isset($_GET['q']) ? trim(htmlspecialchars($_GET['q'], ENT_QUOTES, 'UTF-8')) : '';
    $categoryFilter = isset($_GET['category']) ? filter_var($_GET['category'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0]
    ]) : 0;
    
    $validSorts = ['relevance', 'newest', 'price_asc', 'price_desc', 'popular'];
    $sort = isset($_GET['sort']) && in_array($_GET['sort'], $validSorts) ? $_GET['sort'] : 'relevance';
    
    $page = isset($_GET['page']) ? filter_var($_GET['page'], FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'default' => 1]
    ]) : 1;
}

// Calculate pagination offset
$offset = ($page - 1) * $perPage;

try {
    // Get categories for filter dropdown
    $catStmt = $pdo->prepare("SELECT id, name FROM categories WHERE is_active = 1 AND deleted_at IS NULL ORDER BY name ASC");
    $catStmt->execute();
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Validate category filter against available categories
    if ($categoryFilter > 0) {
        $validCategory = false;
        foreach ($categories as $category) {
            if ($category['id'] == $categoryFilter) {
                $validCategory = true;
                $categoryName = $category['name'];
                break;
            }
        }
        if (!$validCategory) {
            $categoryFilter = 0;
        }
    }

    // Perform search if there's a query
    if (!empty($searchQuery)) {
        // Prepare base query with full-text search
        $sql = "SELECT SQL_CALC_FOUND_ROWS p.*, c.name as category_name,
                       MATCH(p.name, p.description, p.tags) AGAINST(:search IN BOOLEAN MODE) as relevance
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1 
                AND p.deleted_at IS NULL ";
        
        $params = [':search' => $searchQuery . '*'];
        $conditions = [];
        
        // Add search conditions
        $searchTerms = explode(' ', $searchQuery);
        $searchConditions = [];
        foreach ($searchTerms as $i => $term) {
            if (strlen($term) > 2) { // Only search for terms longer than 2 characters
                $param = ":term$i";
                $searchConditions[] = "(p.name LIKE $param OR p.description LIKE $param OR p.tags LIKE $param)";
                $params[$param] = "%$term%";
            }
        }
        
        if (!empty($searchConditions)) {
            $sql .= "AND (MATCH(p.name, p.description, p.tags) AGAINST(:search IN BOOLEAN MODE) ";
            $sql .= "OR " . implode(' AND ', $searchConditions) . ") ";
        } else {
            $sql .= "AND MATCH(p.name, p.description, p.tags) AGAINST(:search IN BOOLEAN MODE) ";
        }
        
        // Add category filter if specified
        if ($categoryFilter > 0) {
            $sql .= "AND p.category_id = :category_id ";
            $params[':category_id'] = $categoryFilter;
        }
        
        // Add sorting
        switch ($sort) {
            case 'newest':
                $sql .= "ORDER BY p.created_at DESC ";
                break;
            case 'price_asc':
                $sql .= "ORDER BY p.price ASC ";
                break;
            case 'price_desc':
                $sql .= "ORDER BY p.price DESC ";
                break;
            case 'popular':
                $sql .= "ORDER BY p.downloads DESC ";
                break;
            default: // relevance
                $sql .= "ORDER BY relevance DESC, p.downloads DESC ";
        }
        
        // Add pagination
        $sql .= "LIMIT :limit OFFSET :offset";
        $params[':limit'] = $perPage;
        $params[':offset'] = $offset;
        
        // Prepare and execute the query
        $stmt = $pdo->prepare($sql);
        
        // Bind parameters with proper types
        foreach ($params as $key => $value) {
            $paramType = PDO::PARAM_STR;
            if ($key === ':limit' || $key === ':offset' || $key === ':category_id') {
                $paramType = PDO::PARAM_INT;
            }
            $stmt->bindValue($key, $value, $paramType);
        }
        
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get total count using FOUND_ROWS() for better performance
        $countStmt = $pdo->query("SELECT FOUND_ROWS()");
        $totalResults = (int)$countStmt->fetchColumn();
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    $error = "An error occurred while processing your request. Please try again later.";
    $products = [];
    $totalResults = 0;
}

// Calculate pagination
$totalPages = $perPage > 0 ? (int)ceil($totalResults / $perPage) : 1;
$startResult = $totalResults > 0 ? $offset + 1 : 0;
$endResult = min($offset + $perPage, $totalResults);

// Include header
require 'includes/header.php';

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>

<!-- Page Content -->
<div class="content">
    <div class="block block-rounded">
        <div class="block-header">
            <h3 class="block-title">
                <?php if (!empty($searchQuery)): ?>
                    Search Results for "<?php echo htmlspecialchars($searchQuery); ?>"
                    <?php if ($categoryFilter > 0): ?>
                        in <?php echo htmlspecialchars($categoryName); ?>
                    <?php endif; ?>
                <?php else: ?>
                    Search Products
                <?php endif; ?>
            </h3>
            <div class="block-options">
                <?php if ($totalResults > 0): ?>
                    <span class="text-muted">Showing <?php echo $startResult; ?>-<?php echo $endResult; ?> of <?php echo $totalResults; ?> results</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="block-content">
            <!-- Search Form -->
            <div class="mb-4">
                <form action="search.php" method="get" class="mb-4" id="searchForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Search products..." required>
                        <select class="form-select" name="category" style="max-width: 200px;">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-search me-1"></i> Search
                        </button>
                    </div>
                </form>
                
                <?php if (!empty($searchQuery) && $totalResults > 0): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="text-muted">
                            <?php if ($totalResults === 1): ?>
                                1 result found
                            <?php else: ?>
                                <?php echo number_format($totalResults); ?> results found
                            <?php endif; ?>
                        </div>
                        <div class="ms-3">
                            <div class="dropdown d-inline-block">
                                <button type="button" class="btn btn-sm btn-alt-secondary dropdown-toggle" id="sortDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Sort by: 
                                    <?php 
                                    $sortLabels = [
                                        'relevance' => 'Relevance',
                                        'newest' => 'Newest',
                                        'price_asc' => 'Price: Low to High',
                                        'price_desc' => 'Price: High to Low',
                                        'popular' => 'Most Popular'
                                    ];
                                    echo $sortLabels[$sort] ?? 'Relevance';
                                    ?>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="sortDropdown">
                                    <?php foreach ($sortLabels as $key => $label): ?>
                                        <?php $active = $sort === $key ? 'active' : ''; ?>
                                        <a class="dropdown-item d-flex justify-content-between align-items-center <?php echo $active; ?>" 
                                           href="?q=<?php echo urlencode($searchQuery); ?>&category=<?php echo $categoryFilter; ?>&sort=<?php echo $key; ?><?php echo $page > 1 ? '&page=' . $page : ''; ?>">
                                            <?php echo $label; ?>
                                            <?php if ($active): ?>
                                                <i class="fa fa-check"></i>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Search Results -->
            <?php if (!empty($searchQuery)): ?>
                <?php if (!empty($products)): ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-6 col-xl-4 mb-4">
                                <div class="block block-rounded h-100">
                                    <div class="block-content p-3">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0 me-3">
                                                <?php if (!empty($product['image_url'])): ?>
                                                    <img class="img-avatar img-avatar-thumb" src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="">
                                                <?php else: ?>
                                                    <div class="img-avatar img-avatar-thumb bg-primary-lighter text-primary">
                                                        <i class="fa fa-box"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h4 class="h5 mb-1">
                                                    <a href="product.php?id=<?php echo $product['id']; ?>">
                                                        <?php echo htmlspecialchars($product['name']); ?>
                                                    </a>
                                                </h4>
                                                <p class="text-muted mb-0">
                                                    <?php echo htmlspecialchars($product['category_name']); ?>
                                                </p>
                                            </div>
                                        </div>
                                        
                                        <p class="mb-3 text-muted">
                                            <?php 
                                            $description = strip_tags($product['description']);
                                            if (strlen($description) > 120) {
                                                $description = substr($description, 0, 120) . '...';
                                            }
                                            echo htmlspecialchars($description);
                                            ?>
                                        </p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <?php if ($product['price'] > 0): ?>
                                                    <span class="fw-bold text-primary">$<?php echo number_format($product['price'], 2); ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Free</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-muted small">
                                                <i class="fa fa-download me-1"></i> <?php echo number_format($product['downloads']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="block-content block-content-full bg-body-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye me-1"></i> View Details
                                            </a>
                                            <span class="text-muted small">
                                                Added <?php echo date('M j, Y', strtotime($product['created_at'])); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Search results navigation">
                            <ul class="pagination justify-content-center">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                            <a class="page-link" href="?q=<?php echo urlencode($searchQuery); ?>&category=<?php echo $categoryFilter; ?>&sort=<?php echo $sort; ?>&page=<?php echo $page - 1; ?>" aria-label="Previous" data-page="<?php echo $page - 1; ?>">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php 
                                $startPage = max(1, $page - 2);
                                $endPage = min($totalPages, $startPage + 4);
                                $startPage = max(1, $endPage - 4);
                                
                                if ($startPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?q=<?php echo urlencode($searchQuery); ?>&category=<?php echo $categoryFilter; ?>&sort=<?php echo $sort; ?>&page=1">1</a>
                                    </li>
                                    <?php if ($startPage > 2): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?q=<?php echo urlencode($searchQuery); ?>&category=<?php echo $categoryFilter; ?>&sort=<?php echo $sort; ?>&page=<?php echo $i; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($endPage < $totalPages): ?>
                                    <?php if ($endPage < $totalPages - 1): ?>
                                        <li class="page-item disabled">
                                            <span class="page-link">...</span>
                                        </li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?q=<?php echo urlencode($searchQuery); ?>&category=<?php echo $categoryFilter; ?>&sort=<?php echo $sort; ?>&page=<?php echo $totalPages; ?>">
                                            <?php echo $totalPages; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                            <a class="page-link" href="?q=<?php echo urlencode($searchQuery); ?>&category=<?php echo $categoryFilter; ?>&sort=<?php echo $sort; ?>&page=<?php echo $page + 1; ?>" aria-label="Next" data-page="<?php echo $page + 1; ?>">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="fa fa-search fa-4x text-muted"></i>
                        </div>
                        <h4>No results found for "<?php echo htmlspecialchars($searchQuery); ?>"</h4>
                        <p class="text-muted">Try different keywords or remove search filters</p>
                        <a href="products.php" class="btn btn-primary">
                            <i class="fa fa-arrow-left me-1"></i> Browse All Products
                        </a>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fa fa-search fa-4x text-muted"></i>
                    </div>
                    <h4>Search for products</h4>
                    <p class="text-muted">Enter keywords to find the products you're looking for</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- END Page Content -->

<?php 
// Include footer and page end
require 'includes/footer.php';
?>

<!-- Add some JavaScript for better UX -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add loading state to form submission
    const searchForm = document.getElementById('searchForm');
    if (searchForm) {
        searchForm.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Searching...';
            }
        });
    }

    // Add smooth scroll to top when paginating
    const paginationLinks = document.querySelectorAll('.pagination a[data-page]');
    paginationLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
            // Small delay to allow scroll to complete before navigation
            setTimeout(() => {
                window.location.href = this.href;
            }, 300);
        });
    });
});
</script>

<?php require 'inc/_global/views/page_end.php'; ?>
