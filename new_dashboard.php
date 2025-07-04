<?php
// Set page title
$page_title = 'Dashboard';

// Include header
require_once 'includes/header.php';

// Get statistics for the dashboard
try {
    // Get total users
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE deleted_at IS NULL");
    $total_users = $stmt->fetchColumn();
    
    // Get total products
    $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE deleted_at IS NULL");
    $total_products = $stmt->fetchColumn();
    
    // Get total orders
    $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL");
    $total_orders = $stmt->fetchColumn();
    
    // Get total revenue
    $stmt = $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'completed'");
    $total_revenue = $stmt->fetchColumn();
    
    // Get recent orders
    $stmt = $pdo->query("SELECT o.*, u.username as customer_name 
                         FROM orders o 
                         LEFT JOIN users u ON o.user_id = u.id 
                         WHERE o.deleted_at IS NULL 
                         ORDER BY o.created_at DESC LIMIT 5");
    $recent_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get recent users
    $stmt = $pdo->query("SELECT * FROM users 
                         WHERE deleted_at IS NULL 
                         ORDER BY created_at DESC LIMIT 5");
    $recent_users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Log error
    error_log("Dashboard error: " . $e->getMessage());
    
    // Set default values
    $total_users = 0;
    $total_products = 0;
    $total_orders = 0;
    $total_revenue = 0;
    $recent_orders = [];
    $recent_users = [];
    
    // Show error message to admin
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        $_SESSION['error_message'] = "Error loading dashboard data: " . $e->getMessage();
    }
}
?>

<!-- Stats -->
<div class="row">
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="users.php">
            <div class="block-content block-content-full">
                <div class="fs-sm fw-semibold text-uppercase text-muted">Total Users</div>
                <div class="fs-2 fw-normal text-dark"><?php echo number_format($total_users); ?></div>
                <div class="d-flex align-items-center mt-2">
                    <div class="text-success">
                        <i class="fa fa-fw fa-user-plus"></i>
                    </div>
                    <div class="ms-1">
                        <span class="text-muted">View all</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="products.php">
            <div class="block-content block-content-full">
                <div class="fs-sm fw-semibold text-uppercase text-muted">Total Products</div>
                <div class="fs-2 fw-normal text-dark"><?php echo number_format($total_products); ?></div>
                <div class="d-flex align-items-center mt-2">
                    <div class="text-info">
                        <i class="fa fa-fw fa-box"></i>
                    </div>
                    <div class="ms-1">
                        <span class="text-muted">View all</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="orders.php">
            <div class="block-content block-content-full">
                <div class="fs-sm fw-semibold text-uppercase text-muted">Total Orders</div>
                <div class="fs-2 fw-normal text-dark"><?php echo number_format($total_orders); ?></div>
                <div class="d-flex align-items-center mt-2">
                    <div class="text-warning">
                        <i class="fa fa-fw fa-shopping-cart"></i>
                    </div>
                    <div class="ms-1">
                        <span class="text-muted">View all</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-6 col-md-3 col-lg-6 col-xl-3">
        <a class="block block-rounded block-link-pop" href="payments.php">
            <div class="block-content block-content-full">
                <div class="fs-sm fw-semibold text-uppercase text-muted">Total Revenue</div>
                <div class="fs-2 fw-normal text-dark">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="d-flex align-items-center mt-2">
                    <div class="text-success">
                        <i class="fa fa-fw fa-dollar-sign"></i>
                    </div>
                    <div class="ms-1">
                        <span class="text-muted">View all</span>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>
<!-- END Stats -->

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="block block-rounded block-mode-loading-oneui">
            <div class="block-header block-header-default">
                <h3 class="block-title">Recent Orders</h3>
                <div class="block-options">
                    <a href="orders.php" class="btn btn-sm btn-alt-primary">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
            </div>
            <div class="block-content block-content-full">
                <?php if (empty($recent_orders)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No recent orders found.</p>
                        <a href="create_order.php" class="btn btn-primary">
                            <i class="fa fa-plus me-1"></i> Create Order
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-borderless table-vcenter fs-sm">
                            <thead>
                                <tr class="text-uppercase">
                                    <th>Order #</th>
                                    <th class="d-none d-sm-table-cell">Customer</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-center">Status</th>
                                    <th class="d-none d-sm-table-cell text-end">Date</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_orders as $order): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-semibold">#<?php echo htmlspecialchars($order['id']); ?></span>
                                        </td>
                                        <td class="d-none d-sm-table-cell">
                                            <?php echo !empty($order['customer_name']) ? htmlspecialchars($order['customer_name']) : 'Guest'; ?>
                                        </td>
                                        <td class="text-end">
                                            $<?php echo number_format($order['total_amount'] ?? 0, 2); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                            $status_class = 'bg-info';
                                            if (isset($order['status'])) {
                                                switch (strtolower($order['status'])) {
                                                    case 'completed':
                                                        $status_class = 'bg-success';
                                                        break;
                                                    case 'pending':
                                                        $status_class = 'bg-warning';
                                                        break;
                                                    case 'cancelled':
                                                        $status_class = 'bg-danger';
                                                        break;
                                                    case 'processing':
                                                        $status_class = 'bg-info';
                                                        break;
                                                    case 'shipped':
                                                        $status_class = 'bg-primary';
                                                        break;
                                                }
                                            }
                                            ?>
                                            <span class="badge <?php echo $status_class; ?>">
                                                <?php echo !empty($order['status']) ? htmlspecialchars(ucfirst($order['status'])) : 'N/A'; ?>
                                            </span>
                                        </td>
                                        <td class="d-none d-sm-table-cell text-end">
                                            <?php echo !empty($order['created_at']) ? date('M j, Y', strtotime($order['created_at'])) : 'N/A'; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <a href="order_details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-alt-secondary" data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="edit_order.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-alt-primary" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- END Recent Orders -->

    <!-- Recent Users -->
    <div class="col-lg-4">
        <div class="block block-rounded block-mode-loading-oneui">
            <div class="block-header block-header-default">
                <h3 class="block-title">Recent Users</h3>
                <div class="block-options">
                    <a href="users.php" class="btn btn-sm btn-alt-primary">
                        <i class="fa fa-list"></i> View All
                    </a>
                </div>
            </div>
            <div class="block-content block-content-full">
                <?php if (empty($recent_users)): ?>
                    <div class="text-center py-4">
                        <p class="text-muted">No recent users found.</p>
                        <a href="create_user.php" class="btn btn-primary">
                            <i class="fa fa-user-plus me-1"></i> Add User
                        </a>
                    </div>
                <?php else: ?>
                    <ul class="nav-items my-2">
                        <?php foreach ($recent_users as $user): ?>
                            <li>
                                <a class="d-flex py-2" href="user_details.php?id=<?php echo $user['id']; ?>">
                                    <div class="flex-shrink-0 mx-3">
                                        <img class="img-avatar img-avatar32" src="<?php echo !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'assets/img/avatars/avatar.jpg'; ?>" alt="">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold"><?php echo htmlspecialchars($user['name'] ?? $user['username']); ?></div>
                                        <div class="fs-sm text-muted"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- END Recent Users -->
</div>

<!-- Page-specific JS -->
<?php
$page_js = [];
$page_js_content = "
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Initialize charts if needed
    if (typeof Chart !== 'undefined') {
        // Add chart initialization code here
    }";
?>

<?php
// Include footer
require_once 'includes/footer.php';
?>
