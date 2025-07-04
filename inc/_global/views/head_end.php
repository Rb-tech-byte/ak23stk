<?php
/**
 * head_end.php
 *
 * Author: pixelcave
 *
 * (continue) The first block of code used in every page of the template
 *
 * The reason we separated head_start.php and head_end.php is for enabling
 * us to include between them extra plugin CSS files needed only in specific pages
 *
 */
?>

  <!-- OneUI framework -->
  <link rel="stylesheet" id="css-main" href="<?php echo $one->assets_folder; ?>/css/oneui.min.css">

  <!-- You can include a specific file from css/themes/ folder to alter the default color theme of the template. eg: -->
  <!-- <link rel="stylesheet" id="css-theme" href="assets/css/themes/amethyst.min.css"> -->
  <?php if ($one->theme) { ?>
  <link rel="stylesheet" id="css-theme" href="<?php echo $one->assets_folder; ?>/css/themes/<?php echo $one->theme; ?>.min.css">
  <?php } ?>
  <!-- END Stylesheets -->

  <!-- Chart.js for dashboard charts -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="<?php echo $one->assets_folder; ?>/js/dashboard.js"></script>

  <!-- Load and set color theme + dark mode preference (blocking script to prevent flashing) -->
  <script src="<?php echo $one->assets_folder; ?>/js/setTheme.js"></script>
</head>

<body>
