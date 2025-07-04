<?php
/**
 * head_start.php
 *
 * Author: pixelcave
 *
 * The first block of code used in every page of the template
 *
 */
?>
<!doctype html>
<html lang="en" class="<?php echo $one->html_classes(false); ?>">
<head>
  <meta charset="utf-8">
  <!--
    Available classes for <html> element:

    'dark'                  Enable dark mode - Default dark mode preference can be set in app.js file (always saved and retrieved in localStorage afterwards):
                              window.One = new App({ darkMode: "system" }); // "on" or "off" or "system"
    'dark-custom-defined'   Dark mode is always set based on the preference in app.js file (no localStorage is used)
    'remember-theme'        Remembers active color theme between pages using localStorage when set through
                              - Theme helper buttons [data-toggle="theme"]
  -->
  <meta name="viewport" content="width=device-width,initial-scale=1.0">

  <title><?php echo $one->title; ?></title>

  <meta name="description" content="<?php echo $one->description; ?>">
  <meta name="author" content="<?php echo $one->author; ?>">
  <meta name="robots" content="<?php echo $one->robots; ?>">

  <!-- Open Graph Meta -->
  <meta property="og:title" content="<?php echo $one->title; ?>">
  <meta property="og:site_name" content="<?php echo $one->name; ?>">
  <meta property="og:description" content="<?php echo $one->description; ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo $one->og_url_site; ?>">
  <meta property="og:image" content="<?php echo $one->og_url_image; ?>">

  <!-- Icons -->
  <!-- The following icons can be replaced with your own, they are used by desktop and mobile browsers -->
  <link rel="shortcut icon" href="<?php echo $one->assets_folder; ?>/media/favicons/favicon.png">
  <link rel="icon" type="image/png" sizes="192x192" href="<?php echo $one->assets_folder; ?>/media/favicons/favicon-192x192.png">
  <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $one->assets_folder; ?>/media/favicons/apple-touch-icon-180x180.png">
  <!-- END Icons -->

  <!-- Stylesheets -->