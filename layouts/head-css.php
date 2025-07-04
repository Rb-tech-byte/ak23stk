<?php
/**
 * Head CSS Includes
 * 
 * This file contains all the CSS includes for the application.
 * It should be included in the head section of your layout files.
 */
?>

<!-- Bootstrap Css -->
<link href="<?php echo BASE_URL; ?>assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

<!-- Icons Css -->
<link href="<?php echo BASE_URL; ?>assets/libs/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo BASE_URL; ?>assets/libs/remixicon/fonts/remixicon.css" rel="stylesheet" type="text/css" />
<link href="<?php echo BASE_URL; ?>assets/libs/boxicons/css/boxicons.min.css" rel="stylesheet" type="text/css" />

<!-- App Css-->
<link href="<?php echo BASE_URL; ?>assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />

<!-- Custom Css -->
<link href="<?php echo BASE_URL; ?>assets/css/custom.css" rel="stylesheet" type="text/css" />

<!-- DataTables CSS -->
<link href="<?php echo BASE_URL; ?>assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo BASE_URL; ?>assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet" type="text/css" />

<!-- Select2 CSS -->
<link href="<?php echo BASE_URL; ?>assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

<!-- Toastr CSS -->
<link href="<?php echo BASE_URL; ?>assets/libs/toastr/build/toastr.min.css" rel="stylesheet" type="text/css" />

<!-- Sweet Alert 2 CSS -->
<link href="<?php echo BASE_URL; ?>assets/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

<!-- Custom Scrollbar CSS -->
<link href="<?php echo BASE_URL; ?>assets/libs/simplebar/simplebar.min.css" rel="stylesheet" type="text/css" />

<!-- Custom styles for this template-->
<style>
    :root {
        --bs-body-bg: #f5f5f9;
        --bs-body-font-family: 'Poppins', sans-serif;
    }
    
    body {
        font-size: 0.9rem;
        color: #495057;
        background-color: var(--bs-body-bg);
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }
    
    .page-content {
        padding: 1.5rem 1.5rem 4.5rem;
    }
    
    @media (max-width: 768px) {
        .page-content {
            padding: 1rem 1rem 4rem;
        }
    }
</style>

<!-- Page-specific CSS -->
<?php if (isset($page_specific_css)): ?>
    <?php foreach ($page_specific_css as $css): ?>
        <link href="<?php echo rtrim(BASE_URL, '/') . '/' . ltrim($css, '/'); ?>" rel="stylesheet" />
    <?php endforeach; ?>
<?php endif; ?>

<!-- Inline CSS -->
<?php if (isset($inline_css)): ?>
    <style>
        <?php echo $inline_css; ?>
    </style>
<?php endif; ?>
