<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/_global/views/head_start.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: op_auth_signin.php');
    exit;
}

// Using BASE_URL constant instead of $baseUrl

// Fetch user data
$user = null;
$purchases = [];
$error = '';

try {
    $stmt = $pdo->prepare('SELECT name, email, phone, address, city, state, postal_code, country, profile_photo_path, is_admin, created_at FROM users WHERE id = ? AND deleted_at IS NULL LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        // Fetch purchase history
        $purchaseStmt = $pdo->prepare('
            SELECT o.id, p.name, p.slug, p.price, o.created_at, o.status 
            FROM orders o 
            JOIN products p ON o.product_id = p.id 
            WHERE o.user_id = ? AND o.deleted_at IS NULL 
            ORDER BY o.created_at DESC
        ');
        $purchaseStmt->execute([$_SESSION['user_id']]);
        $purchases = $purchaseStmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $error = 'User data not found.';
    }
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}

// Fetch categories for mega menu
$categories = [];
$categoryGroups = [
    'Audio' => ['Audio Plugins', 'Digital Audio Workstations', 'Kontakt Libraries', 'Audio Samples', 'Synth Presets', 'Audio Libraries'],
    'Video/Graphics' => ['Photo Editing Software', 'Video Editing Software', 'Graphic Design Tools', 'Screen Capture & Recorder'],
    'Utilities' => ['Converters', 'Security Tools', 'System Utilities', 'Download Managers', 'Office Tools'],
    'Others' => ['Activators', 'Operating Systems', 'Plugins Tools & Utilities']
];
try {
    $catStmt = $pdo->query("SELECT id, name, slug FROM categories WHERE is_active=1 AND deleted_at IS NULL ORDER BY name ASC");
    while ($row = $catStmt->fetch(PDO::FETCH_ASSOC)) {
        $categories[$row['id']] = $row;
    }
} catch (PDOException $e) {
    // Optionally log or display error
}

// Build query string for preserving parameters
$queryParams = [];
if (isset($_GET['category'])) {
    $queryParams['category'] = intval($_GET['category']);
}
if (isset($_GET['page']) && intval($_GET['page']) > 1) {
    $queryParams['page'] = intval($_GET['page']);
}
$queryString = $queryParams ? '?' . http_build_query($queryParams) : '';

require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<!-- Custom CSS -->
<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/custom-dashboard.css">

<!-- Header -->
<header class="bg-primary text-white py-3">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <img src="https://ak23studiokits.com/wp-content/uploads/2025/06/cropped-ak2.png" alt="Logo" class="me-3" style="height: 40px;">
                <div>
                    <h5 class="mb-0 fw-bold">AK23StudioKits</h5>
                    <small class="text-white-50">Download audio plugins and kits</small>
                </div>
            </div>
            <form method="get" action="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>search.php" class="d-flex w-100 w-md-auto mb-2 mb-md-0">
                <input type="text" name="q" class="form-control me-2" placeholder="Search products here..." aria-label="Search">
                <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
            </form>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>">Home</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
                        <div class="dropdown-menu" aria-labelledby="categoriesMenu">
                            <div class="container">
                                <div class="row">
                                    <?php foreach ($categoryGroups as $groupName => $groupCategories): ?>
                                        <div class="col-6 col-md-3">
                                            <h6 class="dropdown-header"><?php echo htmlspecialchars($groupName); ?></h6>
                                            <ul class="list-unstyled">
                                                <?php foreach ($groupCategories as $catName): 
                                                    foreach ($categories as $cat): 
                                                        if ($cat['name'] === $catName): ?>
                                                            <li><a class="dropdown-item" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category/<?php echo urlencode($cat['slug']) . $queryString; ?>">
                                                                <?php echo htmlspecialchars($cat['name']); ?>
                                                            </a></li>
                                                        <?php endif; 
                                                    endforeach; 
                                                endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category/macos<?php echo $queryString; ?>">MacOS</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category/audio-library<?php echo $queryString; ?>">Audio Library</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category/sample-packs<?php echo $queryString; ?>">Sample Packs</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>trending.php">Trending</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>support.php">Support Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>requests.php">Requests</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link active" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>user_dashboard.php">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>login.php">Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>signup.php">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
</header>

<!-- Page Content -->
<main class="content py-4">
    <div class="container">
        <div class="row g-4">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="card sidebar-card">
                    <div class="card-body">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link active" href="#profile"><i class="bi bi-person me-2"></i>Profile</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#purchases"><i class="bi bi-cart me-2"></i>Purchase History</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#settings"><i class="bi bi-gear me-2"></i>Settings</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-danger" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9">
                <?php if ($error): ?>
                    <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
                <?php elseif ($user): ?>
                    <!-- Profile Section -->
                    <div class="card mb-4" id="profile">
                        <div class="card-header bg-primary text-white">
                            <h2 class="card-title mb-0">Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img src="<?php echo $user['profile_photo_path'] ?: $one->assets_folder . '/media/avatars/avatar0.jpg'; ?>" alt="Profile Photo" class="img-fluid rounded-circle mb-3" style="max-width: 150px;">
                                </div>
                                <div class="col-md-8">
                                    <p class="mb-2"><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                                    <?php if ($user['phone']): ?>
                                        <p class="mb-2"><strong>Phone:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($user['address'] && $user['city'] && $user['country']): ?>
                                        <p class="mb-2"><strong>Address:</strong> 
                                            <?php echo htmlspecialchars($user['address']); ?>, 
                                            <?php echo htmlspecialchars($user['city']); ?>,
                                            <?php echo $user['state'] ? htmlspecialchars($user['state']) . ', ' : ''; ?>
                                            <?php echo htmlspecialchars($user['postal_code']); ?>, 
                                            <?php echo htmlspecialchars($user['country']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <p class="mb-2"><strong>Joined:</strong> <?php echo date('M d, Y', strtotime($user['created_at'])); ?></p>
                                    <p><strong>Role:</strong> <?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Purchase History -->
                    <div class="card mb-4" id="purchases">
                        <div class="card-header bg-primary text-white">
                            <h2 class="card-title mb-0">Purchase History</h2>
                        </div>
                        <div class="card-body">
                            <?php if (empty($purchases)): ?>
                                <p class="text-muted text-center">No purchases found.</p>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Date</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($purchases as $purchase): ?>
                                                <tr>
                                                    <td><a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>product/<?php echo urlencode($purchase['slug']) . $queryString; ?>" class="text-decoration-none"><?php echo htmlspecialchars($purchase['name']); ?></a></td>
                                                    <td>TZS <?php echo number_format($purchase['price'], 0); ?></td>
                                                    <td><?php echo date('M d, Y', strtotime($purchase['created_at'])); ?></td>
                                                    <td><?php echo htmlspecialchars($purchase['status']); ?></td>
                                                    <td>
                                                        <?php if ($purchase['status'] === 'paid'): ?>
                                                            <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>download.php?product_id=<?php echo $purchase['id']; ?>" class="btn btn-success btn-sm">Download</a>
                                                        <?php else: ?>
                                                            <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>pesapal.php?product_id=<?php echo $purchase['id']; ?>" class="btn btn-primary btn-sm">Pay Now</a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Profile Photo Upload -->
                    <div class="card" id="settings">
                        <div class="card-header bg-primary text-white">
                            <h2 class="card-title mb-0">Update Profile Photo</h2>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>upload_profile_photo.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">Upload New Photo</label>
                                    <input type="file" class="form-control" id="profile_photo" name="profile_photo" accept="image/*" required>
                                    <div class="invalid-feedback">Please select an image.</div>
                                </div>
                                <button type="submit" class="btn btn-primary">Upload Photo</button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <footer class="bg-light text-center py-3 mt-4">
        <p class="mb-0"><strong>AK23StudioKits</strong> © <?php echo date('Y'); ?></p>
    </footer>
</main>

<?php
require 'inc/_global/views/page_end.php';
require 'inc/_global/views/footer_start.php';
require 'inc/_global/views/footer_end.php';
?>