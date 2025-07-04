<?php
// Include initialization
require_once __DIR__ . '/../init.php';

// Start output buffering
ob_start();
?>
<!DOCTYPE html>
<html lang="en" class="<?php echo isset($one->html_classes) ? $one->html_classes : ''; ?>">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?php echo htmlspecialchars($one->title); ?></title>
  <meta name="description" content="<?php echo htmlspecialchars($one->description); ?>">
  <meta name="author" content="<?php echo htmlspecialchars($one->author); ?>">
  <meta name="robots" content="<?php echo htmlspecialchars($one->robots); ?>">

  <!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo ASSETS_URL; ?>img/favicon.ico">
  
  <!-- OneUI CSS -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/oneui.min.css">
  
  <!-- Theme CSS -->
  <?php if (!empty($one->theme)): ?>
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/themes/<?php echo htmlspecialchars($one->theme); ?>.min.css">
  <?php endif; ?>
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/4download-style.css">
  <link rel="stylesheet" href="<?php echo ASSETS_URL; ?>css/ak23-custom.css">
  
  <!-- Page-specific CSS -->
  <?php if (!empty($page_css)): ?>
    <?php foreach ((array)$page_css as $css): ?>
      <link rel="stylesheet" href="<?php echo ASSETS_URL . 'css/' . $css; ?>">
    <?php endforeach; ?>
  <?php endif; ?>
  
  <!-- Page-specific head content -->
  <?php if (!empty($page_head_content)): ?>
    <?php echo $page_head_content; ?>
  <?php endif; ?>
</head>

<body>
  <!-- Page Container -->
  <div id="page-container" class="sidebar-o sidebar-dark side-scroll page-header-fixed page-header-dark">
    <?php include __DIR__ . '/sidebar.php'; ?>
    
    <!-- Header -->
    <header id="page-header">
      <!-- Header Content -->
      <div class="content-header">
        <!-- Left Section -->
        <div class="d-flex align-items-center">
          <!-- Toggle Sidebar -->
          <button type="button" class="btn btn-sm btn-alt-secondary me-2 d-lg-none" data-toggle="layout" data-action="sidebar_toggle">
            <i class="fa fa-fw fa-bars"></i>
          </button>
          <!-- END Toggle Sidebar -->
          
          <!-- Toggle Mini Sidebar -->
          <button type="button" class="btn btn-sm btn-alt-secondary me-2 d-none d-lg-inline-block" data-toggle="layout" data-action="sidebar_mini_toggle">
            <i class="fa fa-fw fa-ellipsis-v"></i>
          </button>
          <!-- END Toggle Mini Sidebar -->
          
          <!-- Page Title -->
          <h1 class="h3 mb-0"><?php echo $page_title ?? 'Dashboard'; ?></h1>
        </div>
        <!-- END Left Section -->
        
        <!-- Right Section -->
        <div class="d-flex align-items-center">
          <!-- User Dropdown -->
          <?php if (isset($_SESSION['user_id'])): ?>
          <div class="dropdown d-inline-block ms-2">
            <button type="button" class="btn btn-sm btn-alt-secondary d-flex align-items-center" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <img class="rounded-circle" src="<?php echo !empty($_SESSION['avatar']) ? $_SESSION['avatar'] : ASSETS_URL . 'img/avatars/avatar.jpg'; ?>" alt="Header Avatar" style="width: 21px;">
              <span class="d-none d-sm-inline-block ms-2"><?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
              <i class="fa fa-fw fa-angle-down d-none d-sm-inline-block ms-1 opacity-50"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0" aria-labelledby="page-header-user-dropdown">
              <div class="px-3 py-2 bg-body-light rounded-top">
                <h5 class="h6 text-center mb-0">
                  <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User'); ?>
                </h5>
                <div class="text-center">
                  <span class="badge bg-<?php echo !empty($_SESSION['is_admin']) ? 'success' : 'primary'; ?>">
                    <?php echo !empty($_SESSION['is_admin']) ? 'Administrator' : 'User'; ?>
                  </span>
                </div>
              </div>
              <div class="p-2">
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="profile.php">
                  <span>Profile</span>
                  <span><i class="fa fa-fw fa-user me-1"></i></span>
                </a>
                <div role="separator" class="dropdown-divider"></div>
                <a class="dropdown-item d-flex align-items-center justify-content-between" href="logout.php">
                  <span>Log Out</span>
                  <span><i class="fa fa-fw fa-sign-out-alt me-1"></i></span>
                </a>
              </div>
            </div>
          </div>
          <?php endif; ?>
          <!-- END User Dropdown -->
        </div>
        <!-- END Right Section -->
      </div>
      <!-- END Header Content -->
      
      <!-- Header Loader -->
      <div id="page-header-loader" class="overlay-header bg-primary">
        <div class="content-header">
          <div class="w-100 text-center">
            <i class="fa fa-fw fa-2x fa-circle-notch fa-spin text-white"></i>
          </div>
        </div>
      </div>
      <!-- END Header Loader -->
    </header>
    <!-- END Header -->
    
    <!-- Main Container -->
    <main id="main-container">
      <!-- Page Content -->
      <div class="content">
        <?php if (isset($_SESSION['success_message'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success_message']; 
            unset($_SESSION['success_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error_message']; 
            unset($_SESSION['error_message']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
