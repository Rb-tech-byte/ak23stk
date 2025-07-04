<?php
/**
 * Title & Meta Tags
 * 
 * This file contains the title and meta tags for the HTML head section.
 * It should be included in the head section of your layout files.
 */

// Default title if not set
$page_title = $page_title ?? 'AK23StudioKits';

// Default description if not set
$page_description = $page_description ?? 'Professional Studio Kits for Artists and Producers';

// Default keywords if not set
$page_keywords = $page_keywords ?? 'studio kits, beats, instrumentals, music production, ak23studiokits';

// Default author if not set
$page_author = $page_author ?? 'AK23StudioKits';
?>

<!-- Primary Meta Tags -->
<title><?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="title" content="<?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($page_keywords, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="author" content="<?php echo htmlspecialchars($page_author, ENT_QUOTES, 'UTF-8'); ?>">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo SITE_URL; ?>/assets/images/og-image.jpg">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", ENT_QUOTES, 'UTF-8'); ?>">
<meta property="twitter:title" content="<?php echo htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="twitter:description" content="<?php echo htmlspecialchars($page_description, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="twitter:image" content="<?php echo SITE_URL; ?>/assets/images/twitter-card.jpg">

<!-- Favicon -->
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico" type="image/x-icon">
<link rel="icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico" type="image/x-icon">

<!-- Canonical URL -->
<link rel="canonical" href="<?php echo htmlspecialchars((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", ENT_QUOTES, 'UTF-8'); ?>">

<!-- Viewport for responsive design -->
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

<!-- Theme Color -->
<meta name="theme-color" content="#7266ee">
<meta name="msapplication-navbutton-color" content="#7266ee">
<meta name="apple-mobile-web-app-status-bar-style" content="#7266ee">

<!-- Prevent indexing of staging/development environments -->
<?php if (strpos(SITE_URL, 'localhost') !== false || strpos(SITE_URL, 'staging') !== false || strpos(SITE_URL, 'dev') !== false): ?>
<meta name="robots" content="noindex, nofollow">
<?php endif; ?>
