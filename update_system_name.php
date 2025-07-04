<?php
session_start();
require_once __DIR__ . '/database/db_config.php';
require 'inc/_global/config.php';
require 'inc/backend/config.php';
require 'inc/_global/views/head_start.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit;
}

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
$baseUrl = rtrim($baseUrl, '/') . '/';

// Initialize variables
$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Admin';
$error = '';
$success = '';
$currentSystemName = '';

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update') {
    $systemName = trim($_POST['system_name'] ?? '');

    // Validate input
    if (empty($systemName)) {
        $error = 'System name is required.';
    } else {
        $stmt = $pdo->prepare("UPDATE settings SET value = ? WHERE name = 'system_name'");
        if ($stmt->execute([$systemName])) {
            $success = 'System name updated successfully.';
            $currentSystemName = $systemName;
        } else {
            $error = 'Failed to update system name.';
        }
    }
}

// Fetch current system name
try {
    $stmt = $pdo->prepare("SELECT value FROM settings WHERE name = 'system_name'");
    $stmt->execute();
    if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $currentSystemName = $row['value'];
    }
} catch (PDOException $e) {
    $error = 'Database error: ' . $e->getMessage();
}


require 'inc/_global/views/head_end.php';
require 'inc/_global/views/page_start.php';
?>

<link rel="stylesheet" href="<?php echo $one->assets_folder; ?>/css/4download-style.css">

<!-- Page Content -->
<div class="container dashboard-container">
    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="sidebar">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>admin_dashboard.php">
                            <i class="bi bi-speedometer"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'categories.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>categories.php">
                            <i class="bi bi-tag"></i> Category
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>products.php">
                            <i class="bi bi-cart"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'payments.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>payments.php">
                            <i class="bi bi-credit-card"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>users.php">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'medias.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>medias.php">
                            <i class="bi bi-image"></i> Medias
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'support.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>support.php">
                            <i class="bi bi-headset"></i> Ticket/Support
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="generalSettingDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-gear"></i> General Setting
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="generalSettingDropdown">
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>payment_methods.php">Payment Method</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>file_storage.php">Files Storage</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>update_system_name.php">Update System Name</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>update_logo.php">Update Logo</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>mail_smtp.php">Mail SMTP</a></li>
                            <li><a class="dropdown-item" href="<?php echo $baseUrl; ?>sms_setting.php">SMS Setting</a></li>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_panel.php' ? 'active' : ''; ?>" href="<?php echo $baseUrl; ?>admin_panel.php">
                            <i class="bi bi-lock"></i> Admin Panel
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $baseUrl; ?>logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <!-- Update System Name Form -->
            <div class="block block-rounded mb-4 animated fadeIn">
                <div class="block-header block-header-default">
                    <h3 class="block-title">Update System Name</h3>
                    <div class="block-options">
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="darkModeToggle" data-bs-toggle="tooltip" title="Toggle Dark Mode">
                            <i class="bi bi-moon"></i>
                        </button>
                    </div>
                </div>
                <div class="block-content">
                    <form action="<?php echo htmlspecialchars($baseUrl); ?>update_system_name.php" method="POST">
                        <input type="hidden" name="action" value="update">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="systemName" class="form-label">System Name</label>
                                    <input type="text" class="form-control" id="systemName" name="system_name" value="<?php echo htmlspecialchars($currentSystemName); ?>" required>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Update System Name</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-text">
        <strong>AK23StudioKits</strong> &copy; <span data-toggle="year-copy"><?php echo date('Y'); ?></span>
    </div>
</div>

<!-- JavaScript for Tooltips and Dark Mode -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function(tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Dark mode toggle
    document.getElementById('darkModeToggle').addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        this.querySelector('i').classList.toggle('bi-moon');
        this.querySelector('i').classList.toggle('bi-sun');
    });
});
</script>

<?php require 'inc/_global/views/page_end.php'; ?>
