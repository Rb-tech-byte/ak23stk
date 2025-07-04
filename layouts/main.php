<?php
/**
 * Main Layout Template
 * 
 * This is the main layout template that wraps all pages
 */

// Prevent direct access
if (!defined('BASE_URL')) {
    die('Direct access not permitted');
}

// Ensure output buffering is started
if (ob_get_level() === 0) {
    ob_start();
}
?>
<!DOCTYPE html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable">
<head>
    <meta charset="utf-8" />
    <title><?php echo isset($title) ? htmlspecialchars($title, ENT_QUOTES, 'UTF-8') : 'AK23StudioKits'; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="AK23StudioKits - Professional Studio Kits" name="description" />
    <meta content="AK23StudioKits" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>assets/images/favicon.ico">
    <?php 
    // Include any additional head content
    if (function_exists('get_head_content')) {
        echo get_head_content();
    }
    ?>
</head>

<body>
    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php 
// Include header if it hasn't been included yet
if (!function_exists('get_head_content')) {
    include __DIR__ . '/../includes/header.php';
}
?>

<?php 
// Include sidebar if it exists and hasn't been included yet
if (file_exists(__DIR__ . '/../includes/sidebar.php') && !function_exists('get_sidebar_content')) {
    include __DIR__ . '/../includes/sidebar.php';
}
?>
        
        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php 
                    // Page title
                    if (isset($page_title)): 
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-flex align-items-center justify-content-between">
                                <h4 class="mb-0"><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></h4>
                                <?php if (isset($page_actions)): ?>
                                <div class="page-title-right">
                                    <?php echo $page_actions; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php 
                    // Display flash messages
                    if (isset($_SESSION['flash_message'])): 
                        $flash = $_SESSION['flash_message'];
                        unset($_SESSION['flash_message']);
                    ?>
                    <div class="alert alert-<?php echo htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8'); ?> alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Main content will be inserted here -->
                    <?php echo $content ?? ''; ?>
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            
            <?php 
// Include footer if it exists and hasn't been included yet
if (file_exists(__DIR__ . '/../includes/footer.php') && !function_exists('get_footer_content')) {
    include __DIR__ . '/../includes/footer.php';
}
?>
        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
    
    <?php include 'layouts/vendor-scripts.php'; ?>
    
    <?php 
    // Include any additional scripts
    if (function_exists('get_footer_scripts')) {
        echo get_footer_scripts();
    }
    ?>
</body>
</html>
