<!-- Sidebar -->
<nav id="sidebar" aria-label="Main Navigation">
  <!-- Side Header -->
  <div class="bg-header-dark">
    <div class="content-header bg-white-10">
      <!-- Logo -->
      <a class="fw-semibold text-white" href="index.php">
        <span class="smini-visible">
          <i class="fa fa-circle-notch text-primary"></i>
        </span>
        <span class="smini-hidden">
          <span class="h3 fw-bold text-white">AK23STK</span>
        </span>
      </a>
      <!-- END Logo -->

      <!-- Options -->
      <div>
        <!-- Toggle Sidebar Style -->
        <div class="dropdown d-inline-block ms-1">
          <button type="button" class="btn btn-sm btn-alt-secondary" id="sidebar-style-toggler" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-fw fa-brush"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end fs-sm" aria-labelledby="sidebar-style-toggler">
            <a class="dropdown-item" href="#" data-toggle="layout" data-action="sidebar_style_dark">
              <i class="fa fa-fw fa-moon me-1"></i> Dark
            </a>
            <a class="dropdown-item" href="#" data-toggle="layout" data-action="sidebar_style_light">
              <i class="fa fa-fw fa-sun me-1"></i> Light
            </a>
          </div>
        </div>
        <!-- END Toggle Sidebar Style -->

        <!-- Close Sidebar, Visible only on mobile screens -->
        <button type="button" class="btn btn-sm btn-alt-secondary d-lg-none ms-1" data-toggle="layout" data-action="sidebar_close">
          <i class="fa fa-fw fa-times"></i>
        </button>
        <!-- END Close Sidebar -->
      </div>
      <!-- END Options -->
    </div>
  </div>
  <!-- END Side Header -->

  <!-- Sidebar Scrolling -->
  <div class="js-sidebar-scroll">
    <!-- Side Navigation -->
    <div class="content-side">
      <ul class="nav-main">
        <li class="nav-main-item">
          <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? ' active' : ''; ?>" href="dashboard.php">
            <i class="nav-main-link-icon fa fa-home"></i>
            <span class="nav-main-link-name">Dashboard</span>
          </a>
        </li>

        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
        <!-- Admin Menu Items -->
        <li class="nav-main-heading">Administration</li>
        
        <li class="nav-main-item<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['users.php', 'user_details.php', 'create_user.php', 'edit_user.php'])) ? ' open' : ''; ?>">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="true" href="#">
            <i class="nav-main-link-icon fa fa-users"></i>
            <span class="nav-main-link-name">Users</span>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'users.php') ? ' active' : ''; ?>" href="users.php">
                <span class="nav-main-link-name">All Users</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'create_user.php') ? ' active' : ''; ?>" href="create_user.php">
                <span class="nav-main-link-name">Add New User</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-main-item<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['products.php', 'product_details.php', 'create_product.php', 'edit_product.php'])) ? ' open' : ''; ?>">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
            <i class="nav-main-link-icon fa fa-box"></i>
            <span class="nav-main-link-name">Products</span>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'products.php') ? ' active' : ''; ?>" href="products.php">
                <span class="nav-main-link-name">All Products</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'create_product.php') ? ' active' : ''; ?>" href="create_product.php">
                <span class="nav-main-link-name">Add New Product</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'categories.php') ? ' active' : ''; ?>" href="categories.php">
                <span class="nav-main-link-name">Categories</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-main-item<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['orders.php', 'order_details.php', 'create_order.php', 'edit_order.php'])) ? ' open' : ''; ?>">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
            <i class="nav-main-link-icon fa fa-shopping-cart"></i>
            <span class="nav-main-link-name">Orders</span>
            <?php 
            // Example of showing pending orders count
            try {
                $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
                $pending_orders = $stmt->fetchColumn();
                if ($pending_orders > 0) {
                    echo '<span class="badge rounded-pill bg-danger float-end">' . $pending_orders . '</span>';
                }
            } catch (PDOException $e) {
                // Error handling
            }
            ?>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'orders.php') ? ' active' : ''; ?>" href="orders.php">
                <span class="nav-main-link-name">All Orders</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'create_order.php') ? ' active' : ''; ?>" href="create_order.php">
                <span class="nav-main-link-name">Create Order</span>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-main-item<?php echo (in_array(basename($_SERVER['PHP_SELF']), ['payments.php', 'payment_details.php', 'create_payment.php', 'edit_payment.php'])) ? ' open' : ''; ?>">
          <a class="nav-main-link nav-main-link-submenu" data-toggle="submenu" aria-haspopup="true" aria-expanded="false" href="#">
            <i class="nav-main-link-icon fa fa-credit-card"></i>
            <span class="nav-main-link-name">Payments</span>
          </a>
          <ul class="nav-main-submenu">
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'payments.php') ? ' active' : ''; ?>" href="payments.php">
                <span class="nav-main-link-name">All Payments</span>
              </a>
            </li>
            <li class="nav-main-item">
              <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'create_payment.php') ? ' active' : ''; ?>" href="create_payment.php">
                <span class="nav-main-link-name">Record Payment</span>
              </a>
            </li>
          </ul>
        </li>
        <?php endif; ?>

        <!-- Common Menu Items -->
        <li class="nav-main-heading">Account</li>
        
        <li class="nav-main-item">
          <a class="nav-main-link<?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? ' active' : ''; ?>" href="profile.php">
            <i class="nav-main-link-icon fa fa-user"></i>
            <span class="nav-main-link-name">My Profile</span>
          </a>
        </li>
        
        <li class="nav-main-item">
          <a class="nav-main-link" href="logout.php">
            <i class="nav-main-link-icon fa fa-sign-out-alt"></i>
            <span class="nav-main-link-name">Logout</span>
          </a>
        </li>
      </ul>
    </div>
    <!-- END Side Navigation -->
  </div>
  <!-- END Sidebar Scrolling -->
</nav>
<!-- END Sidebar -->
