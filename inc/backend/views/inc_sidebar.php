<?php
/**
 * backend/views/inc_sidebar.php
 *
 * Author: pixelcave
 *
 * The sidebar of each page (Backend pages)
 *
 */
?>

<!-- Sidebar -->
<!--
    Sidebar Mini Mode - Display Helper classes

    Adding 'smini-hide' class to an element will make it invisible (opacity: 0) when the sidebar is in mini mode
    Adding 'smini-show' class to an element will make it visible (opacity: 1) when the sidebar is in mini mode
        If you would like to disable the transition animation, make sure to also add the 'no-transition' class to your element

    Adding 'smini-hidden' to an element will hide it when the sidebar is in mini mode
    Adding 'smini-visible' to an element will show it (display: inline-block) only when the sidebar is in mini mode
    Adding 'smini-visible-block' to an element will show it (display: block) only when the sidebar is in mini mode
-->
<nav id="sidebar" aria-label="Main Navigation">
  <!-- Side Header -->
  <div class="content-header">
    <!-- Logo -->
    <a class="fw-semibold text-dual" href="index.php">
      <span class="smini-visible">
        <i class="fa fa-circle-notch text-primary"></i>
      </span>
      <span class="smini-hide fs-5 tracking-wider">AK23</span>
    </a>
    <!-- END Logo -->

    <!-- Extra -->
    <div class="d-flex align-items-center gap-1">
      <!-- Dark Mode -->
      <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
      <div class="dropdown">
        <button type="button" class="btn btn-sm btn-alt-secondary" id="sidebar-dark-mode-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="far fa-fw fa-moon" data-dark-mode-icon></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end smini-hide border-0" aria-labelledby="sidebar-dark-mode-dropdown">
          <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_off" data-dark-mode="off">
            <i class="far fa-sun fa-fw opacity-50"></i>
            <span class="fs-sm fw-medium">Light</span>
          </button>
          <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_on" data-dark-mode="on">
            <i class="far fa-moon fa-fw opacity-50"></i>
            <span class="fs-sm fw-medium">Dark</span>
          </button>
          <button type="button" class="dropdown-item d-flex align-items-center gap-2" data-toggle="layout" data-action="dark_mode_system" data-dark-mode="system">
            <i class="fa fa-desktop fa-fw opacity-50"></i>
            <span class="fs-sm fw-medium">System</span>
          </button>
        </div>
      </div>
      <!-- END Dark Mode -->

      <!-- Options -->
      <div class="dropdown">
        <button type="button" class="btn btn-sm btn-alt-secondary" id="sidebar-themes-dropdown" data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="fa fa-fw fa-brush"></i>
        </button>
        <div class="dropdown-menu dropdown-menu-end fs-sm smini-hide border-0" aria-labelledby="sidebar-themes-dropdown">
          <!-- Color Themes -->
          <!-- Layout API, functionality initialized in Template._uiHandleTheme() -->
          <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="default">
            <span>Default</span>
            <i class="fa fa-circle text-default"></i>
          </button>
          <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="<?php echo $one->assets_folder; ?>/css/themes/amethyst.min.css">
            <span>Amethyst</span>
            <i class="fa fa-circle text-amethyst"></i>
          </button>
          <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="<?php echo $one->assets_folder; ?>/css/themes/city.min.css">
            <span>City</span>
            <i class="fa fa-circle text-city"></i>
          </button>
          <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="<?php echo $one->assets_folder; ?>/css/themes/flat.min.css">
            <span>Flat</span>
            <i class="fa fa-circle text-flat"></i>
          </button>
          <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="<?php echo $one->assets_folder; ?>/css/themes/modern.min.css">
            <span>Modern</span>
            <i class="fa fa-circle text-modern"></i>
          </button>
          <button class="dropdown-item d-flex align-items-center justify-content-between fw-medium" data-toggle="theme" data-theme="<?php echo $one->assets_folder; ?>/css/themes/smooth.min.css">
            <span>Smooth</span>
            <i class="fa fa-circle text-smooth"></i>
          </button>
          <!-- END Color Themes -->

          <div class="dropdown-divider d-dark-none"></div>

          <!-- Sidebar Styles -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout" data-action="sidebar_style_light" href="javascript:void(0)">
            <span>Sidebar Light</span>
          </a>
          <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout" data-action="sidebar_style_dark" href="javascript:void(0)">
            <span>Sidebar Dark</span>
          </a>
          <!-- END Sidebar Styles -->

          <div class="dropdown-divider d-dark-none"></div>

          <!-- Header Styles -->
          <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
          <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout" data-action="header_style_light" href="javascript:void(0)">
            <span>Header Light</span>
          </a>
          <a class="dropdown-item fw-medium d-dark-none" data-toggle="layout" data-action="header_style_dark" href="javascript:void(0)">
            <span>Header Dark</span>
          </a>
          <!-- END Header Styles -->
        </div>
      </div>
      <!-- END Options -->

      <!-- Close Sidebar, Visible only on mobile screens -->
      <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
      <a class="d-lg-none btn btn-sm btn-alt-secondary ms-1" data-toggle="layout" data-action="sidebar_close" href="javascript:void(0)">
        <i class="fa fa-fw fa-times"></i>
      </a>
      <!-- END Close Sidebar -->
    </div>
    <!-- END Extra -->
  </div>
  <!-- END Side Header -->

  <!-- Sidebar Scrolling -->
  <div class="js-sidebar-scroll">
    <!-- Side Navigation -->
    <div class="content-side">
      <ul class="nav-main">
        <?php $one->build_nav(); ?>
      </ul>
    </div>
    <!-- END Side Navigation -->
  </div>
  <!-- END Sidebar Scrolling -->
</nav>
<!-- END Sidebar -->
