<?php
/**
 * User Dashboard
 * 
 * This file handles the user dashboard functionality
 */

// Start output buffering at the very beginning
if (ob_get_level() === 0) {
    ob_start();
}

// Include database configuration first
require_once __DIR__ . '/database/db_config.php';

// Include initialization
require_once __DIR__ . '/init.php';

// Include global config
require_once __DIR__ . '/inc/_global/config.php';

// Check for output before headers
if (ob_get_length()) {
    error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Output before headers: ' . ob_get_clean());
    ob_start();
}

// Initialize CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize variables
$errors = [];
$user = null;
$recentOrders = [];
$downloads = [];
$isAuthenticated = isset($_SESSION['user_id']);

// Debug flag
$debug = IS_LOCAL;

// Check if user is logged in
if (!$isAuthenticated) {
    header('Location: ' . SITE_URL . '/auth-signin-basic.php');
    exit;
}

try {
    // Get user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user) {
        session_destroy();
        header('Location: ' . SITE_URL . '/auth-signin-basic.php');
        exit;
    }

    // Get recent orders
    $stmt = $pdo->prepare("
        SELECT o.*, p.name as product_name, p.slug as product_slug, p.price 
        FROM orders o 
        JOIN products p ON o.product_id = p.id 
        WHERE o.user_id = ? 
        ORDER BY o.created_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$user['id']]);
    $recentOrders = $stmt->fetchAll();

    // Get recent downloads
    $stmt = $pdo->prepare("
        SELECT d.*, p.name as product_name, p.slug as product_slug 
        FROM downloads d 
        JOIN products p ON d.product_id = p.id 
        WHERE d.user_id = ? 
        ORDER BY d.downloaded_at DESC 
        LIMIT 5
    ");
    $stmt->execute([$user['id']]);
    $downloads = $stmt->fetchAll();

} catch (PDOException $e) {
    $errors[] = 'Database error: ' . $e->getMessage();
    error_log('['.date('Y-m-d H:i:s').'] Dashboard Error: ' . $e->getMessage());
}

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ' . SITE_URL . '/auth-signin-basic.php');
    ob_end_flush();
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$isAuthenticated) {
    if ($debug) {
        error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Form submitted to auth-signin-basic.php');
    }

    // Validate CSRF token
    $csrfToken = filter_input(INPUT_POST, 'csrf_token', FILTER_UNSAFE_RAW);
    $csrfToken = $csrfToken ? htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') : '';
    if (!$csrfToken || !hash_equals($_SESSION['csrf_token'], $csrfToken)) {
        $errors[] = 'Security error: Invalid form submission.';
        error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] CSRF token validation failed');
    } else {
        // Sanitize inputs
        $email = filter_input(INPUT_POST, 'login-email', FILTER_SANITIZE_EMAIL);
        $password = filter_input(INPUT_POST, 'login-password', FILTER_UNSAFE_RAW);
        $password = $password ? htmlspecialchars($password, ENT_QUOTES, 'UTF-8') : '';

        if ($debug) {
            error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Email: ' . $email);
        }

        // Validate inputs
        if (!$email || !$password) {
            $errors[] = 'Email and password are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format.';
        } else {
            // Query user
            try {
                $stmt = $pdo->prepare('
                    SELECT id, name, email, password, is_admin, role_id, temp_password
                    FROM users
                    WHERE email = ? AND deleted_at IS NULL
                    LIMIT 1
                ');
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if ($debug) {
                    error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] User query result: ' . ($user ? 'Found' : 'Not found'));
                }

                if ($user && password_verify($password, $user['password'])) {
                    // Regenerate session ID
                    session_regenerate_id(true);

                    // Store session data
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    $_SESSION['role_id'] = $user['role_id'];
                    $_SESSION['last_login'] = date('Y-m-d H:i:s', time() + 3*3600);

                    // Log login activity
                    try {
                        $stmt = $pdo->prepare('
                            INSERT INTO activity_log (log_name, description, subject_type, subject_id, causer_type, causer_id, properties, created_at)
                            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                        ');
                        $stmt->execute([
                            'login',
                            'User logged in',
                            'App\\Models\\User',
                            $user['id'],
                            'App\\Models\\User',
                            $user['id'],
                            json_encode([
                                'email' => $user['email'],
                                'ip' => $_SERVER['REMOTE_ADDR'],
                                'user_agent' => $_SERVER['HTTP_USER_AGENT']
                            ])
                        ]);
                    } catch (PDOException $e) {
                        error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Activity log error: ' . $e->getMessage());
                    }

                    // Redirect
                    $redirectUrl = !empty($user['temp_password']) ? SITE_URL . '/change-password.php' : SITE_URL . '/index.php';
                    if ($debug) {
                        error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Redirecting to: ' . $redirectUrl);
                    }
                    header('Location: ' . $redirectUrl);
                    ob_end_flush();
                    exit;
                } else {
                    $errors[] = 'Invalid email or password.';
                    if ($debug) {
                        error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Invalid login attempt for: ' . $email);
                    }
                }
            } catch (PDOException $e) {
                $errors[] = 'Database error. Please try again.';
                error_log('[' . date('Y-m-d H:i:s', time() + 3*3600) . '] Login query error: ' . $e->getMessage());
            }
        }
    }
    // Regenerate CSRF token
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Include layout files
include 'layouts/main.php';
// Helper to include a PHP file with variables
if (!function_exists('includeFileWithVariables')) {
    function includeFileWithVariables($filePath, $variables = []) {
        extract($variables);
        include $filePath;
    }
}
includeFileWithVariables('layouts/title-meta.php', ['title' => 'Dashboard - AK23StudioKits']);
include 'layouts/head-css.php';
?>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php include 'includes/header.php'; ?>
        
        <!-- ========== Left Sidebar Start ========== -->
        <?php include 'includes/sidebar.php'; ?>
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">AK23StudioKits</a></li>
                                        <li class="breadcrumb-item active">Dashboard</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                    <div class="row">
                        <div class="col-xl-4">
                            <!-- User profile card -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <div class="mb-4">
                                            <img src="<?php echo SITE_URL; ?>/assets/images/users/avatar-1.jpg" class="rounded-circle avatar-lg img-thumbnail" alt="profile-image">
                                        </div>
                                        <h5 class="font-size-16 mb-1"><?php echo htmlspecialchars($user['name']); ?></h5>
                                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                        
                                        <div class="mt-4">
                                            <a href="<?php echo SITE_URL; ?>/profile-edit.php" class="btn btn-primary btn-sm"><i class="mdi mdi-account-edit me-1"></i> Edit Profile</a>
                                            <a href="?action=logout" class="btn btn-light btn-sm"><i class="mdi mdi-logout me-1"></i> Logout</a>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <div class="mt-4">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="p-2 border border-dashed rounded text-center">
                                                    <div>
                                                        <p class="font-size-16 mb-1"><?php echo count($recentOrders); ?></p>
                                                        <p class="text-muted mb-0">Orders</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-2 border border-dashed rounded text-center">
                                                    <div>
                                                        <p class="font-size-16 mb-1"><?php echo count($downloads); ?></p>
                                                        <p class="text-muted mb-0">Downloads</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8">
                            <!-- Recent Orders -->
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Recent Orders</h4>
                                    
                                    <?php if (!empty($recentOrders)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-centered table-nowrap mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Order #</th>
                                                        <th>Product</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                        <th>Status</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($recentOrders as $order): ?>
                                                    <tr>
                                                        <td>#<?php echo $order['id']; ?></td>
                                                        <td>
                                                            <a href="<?php echo SITE_URL; ?>/product-details.php?slug=<?php echo $order['product_slug']; ?>" class="text-body">
                                                                <?php echo htmlspecialchars($order['product_name']); ?>
                                                            </a>
                                                        </td>
                                                        <td>TZS <?php echo isset($order['amount']) && is_numeric($order['amount']) ? number_format((float)$order['amount'], 2) : '0.00'; ?></td>
                                                        <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'danger'); ?> font-size-12">
                                                                <?php echo ucfirst($order['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="<?php echo SITE_URL; ?>/order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-light">View</a>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-end mt-3">
                                            <a href="<?php echo SITE_URL; ?>/orders.php" class="btn btn-primary btn-sm">View All</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            You haven't placed any orders yet. <a href="<?php echo SITE_URL; ?>/products.php" class="text-primary">Browse products</a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Recent Downloads -->
                            <div class="card mt-4">
                                <div class="card-body">
                                    <h4 class="card-title mb-4">Recent Downloads</h4>
                                    
                                    <?php if (!empty($downloads)): ?>
                                        <div class="table-responsive">
                                            <table class="table table-centered table-nowrap mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Product</th>
                                                        <th>Downloaded On</th>
                                                        <th>Expires</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($downloads as $download): 
                                                        $expiryDate = date('Y-m-d H:i:s', strtotime($download['downloaded_at'] . ' + ' . DOWNLOAD_EXPIRY_DAYS . ' days'));
                                                        $isExpired = strtotime($expiryDate) < time();
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <a href="<?php echo SITE_URL; ?>/product-details.php?slug=<?php echo $download['product_slug']; ?>" class="text-body">
                                                                <?php echo htmlspecialchars($download['product_name']); ?>
                                                            </a>
                                                        </td>
                                                        <td><?php echo date('M d, Y', strtotime($download['downloaded_at'])); ?></td>
                                                        <td>
                                                            <span class="text-<?php echo $isExpired ? 'danger' : 'success'; ?>">
                                                                <?php echo date('M d, Y', strtotime($expiryDate)); ?>
                                                                <?php if ($isExpired): ?>
                                                                    <span class="badge bg-danger ms-2">Expired</span>
                                                                <?php endif; ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php if (!$isExpired): ?>
                                                                <a href="<?php echo SITE_URL; ?>/download.php?id=<?php echo $download['id']; ?>" class="btn btn-sm btn-primary">
                                                                    <i class="mdi mdi-download me-1"></i> Download
                                                                </a>
                                                            <?php else: ?>
                                                                <button class="btn btn-sm btn-light" disabled>Expired</button>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="text-end mt-3">
                                            <a href="<?php echo SITE_URL; ?>/downloads.php" class="btn btn-primary btn-sm">View All</a>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-info">
                                            No downloads found. Your downloads will appear here after purchasing products.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>document.write(new Date().getFullYear())</script> © AK23StudioKits.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Crafted with <i class="mdi mdi-heart text-danger"></i> by AK23StudioKits
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Main footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">© <?php echo date('Y'); ?> AK23StudioKits. Crafted with <i class="mdi mdi-heart text-danger"></i></p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JAVASCRIPT -->
    <?php include 'layouts/vendor-scripts.php'; ?>
    
    <!-- App js -->
    <script src="<?php echo SITE_URL; ?>/assets/js/app.js"></script>
    
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Initialize popovers
        var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
        
        // Initialize form validation
        (function () {
            'use strict';
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
        
        // Initialize DataTables if any
        if ($.fn.DataTable) {
            $('.datatable').DataTable({
                responsive: true,
                pageLength: 10,
                language: {
                    paginate: {
                        previous: "<i class='mdi mdi-chevron-left'>",
                        next: "<i class='mdi mdi-chevron-right'>"
                    }
                },
                drawCallback: function () {
                    $(".dataTables_paginate > .pagination").addClass("pagination-rounded");
                }
            });
        }
    </script>
</body>
</html>
<?php
// End output buffering
ob_end_flush();
?>