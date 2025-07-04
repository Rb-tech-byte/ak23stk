

<?php
// Initialize query string
$queryString = '';

// Preserve query string parameters if they exist
if (!empty($_SERVER['QUERY_STRING'])) {
    $queryString = '&' . $_SERVER['QUERY_STRING'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AK23StudioKits - Premium Audio Plugins & Kits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">
    <script src="<?php echo $one->assets_folder; ?>/js/plugins/sweetalert2/sweetalert2.min.js"></script>
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between flex-wrap">
            <div>
                <a href="#"><i class="bi bi-envelope me-1"></i> support@ak23studiokits.com</a>
                <a href="#"><i class="bi bi-headset me-1"></i> Support Center</a>
            </div>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>user_dashboard.php"><i class="bi bi-person me-1"></i> Dashboard</a>
                    <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>logout.php"><i class="bi bi-box-arrow-right me-1"></i> Logout</a>
                <?php else: ?>
                    <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>login.php"><i class="bi bi-person me-1"></i> Login</a>
                    <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>signup.php"><i class="bi bi-person-plus me-1"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <div class="logo-area">
                        <img src="https://ak23studiokits.com/wp-content/uploads/2025/06/cropped-ak2.png" alt="Logo" class="logo-img">
                        <div class="logo-text">
                            <h1>AK23StudioKits</h1>
                            <p>Download audio plugins and kits</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-3 mb-lg-0">
                    <form method="get" class="search-form" action="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>search.php">
                        <div class="input-group position-relative">
                            <input type="text" name="q" class="form-control" placeholder="Search for plugins, samples, kits..." required>
                            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                            <div class="dropdown-menu autosuggest" style="display: none;"></div>
                        </div>
                    </form>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <div class="d-flex justify-content-lg-end justify-content-center">
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>wishlist.php" class="btn btn-light me-2"><i class="bi bi-heart me-1"></i> Wishlist</a>
                        <a href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>cart.php" class="btn btn-light"><i class="bi bi-cart3 me-1"></i> Cart</a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark main-nav">
        <div class="container">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>">Home</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Categories
                        </a>
                        <ul class="dropdown-menu mega-menu" aria-labelledby="categoriesDropdown">
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
                                    <li class="mega-menu-group">
                                        <h6 class="dropdown-header"><?php echo htmlspecialchars($groupName); ?></h6>
                                        <div class="mega-menu-items">
                                            <?php foreach ($validCategories as $catName): ?>
                                                <?php foreach ($categories as $cat): ?>
                                                    <?php if ($cat['name'] === $catName): ?>
                                                        <a class="dropdown-item" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category.php?category=<?php echo $cat['id'] . $queryString; ?>">
                                                            <?php echo htmlspecialchars($cat['name']); ?>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category.php?category=macos<?php echo $queryString; ?>">MacOS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category.php?category=audio-library<?php echo $queryString; ?>">Audio Library</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>category.php?category=sample-packs<?php echo $queryString; ?>">Sample Packs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>trending.php">Trending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>support.php">Support Us</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo defined('BASE_URL') ? BASE_URL : ''; ?>requests.php">Requests</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    