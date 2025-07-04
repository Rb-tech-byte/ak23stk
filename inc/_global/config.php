<?php
/**
 * Global Configuration File
 * 
 * This file contains global configuration settings for the application.
 * No output, whitespace, or header logic should be here.
 */

// Prevent direct access
defined('DS') || define('DS', DIRECTORY_SEPARATOR);

// Define application environment
if (!defined('ENVIRONMENT')) {
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    if (strpos($host, 'localhost') !== false || in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
        define('ENVIRONMENT', 'development');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
    } else {
        define('ENVIRONMENT', 'production');
        error_reporting(0);
        ini_set('display_errors', 0);
    }
}

// Define base URL if not already defined
if (!defined('BASE_URL')) {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $basePath = rtrim(str_replace('\\', '/', $scriptDir), '/');
    
    // Remove 'inc/_global' from the path if present
    $basePath = str_replace('/inc/_global', '', $basePath);
    
    // Ensure we don't have double slashes
    $basePath = str_replace('//', '/', $basePath);
    
    // Define the base URL
    define('BASE_URL', rtrim("$scheme://$host$basePath", '/') . '/');
}

// Define site name
if (!defined('SITE_NAME')) {
    define('SITE_NAME', 'AK23StudioKits');
}

// Define paths
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)) . DS);
}

if (!defined('INCLUDES_PATH')) {
    define('INCLUDES_PATH', ROOT_PATH . 'includes' . DS);
}

if (!defined('UPLOADS_PATH')) {
    define('UPLOADS_PATH', ROOT_PATH . 'uploads' . DS);
}

// Include required classes
require_once __DIR__ . '/../_classes/Template.php';

// Initialize Template
$one = new Template(SITE_NAME, '1.0', 'assets');

// Set default timezone
date_default_timezone_set('Africa/Dar_es_Salaam');

// Database configuration - Only define if not already defined in db_config.php
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'ak23skdb');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');

// Debug mode
define('DEBUG_MODE', ENVIRONMENT === 'development');

// Error logging
if (DEBUG_MODE) {
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT_PATH . 'logs/error.log');
}

// Global helper functions
require_once __DIR__ . '/functions.php';

//                               : The data is added in the <head> section of the page
$one->author                     = 'wolinetltd';
$one->robots                     = 'index, follow';
$one->title                      = 'wolinet - Bootstrap 5 Admin Template &amp; UI Framework';
$one->description                = 'wolinet- Bootstrap 5 Admin Template &amp; UI Framework created by pixelcave';

//                               : The url of your site, used in Open Graph Meta Data (eg 'https://example.com')
$one->og_url_site                = '';

//                               : The url of your image/logo, used in Open Graph Meta Data (eg 'https://example.com/assets/img/your_logo.png')
$one->og_url_image               = '';


// **************************************************************************************************
// GLOBAL GENERIC
// **************************************************************************************************

// ''                            : default color theme
// 'amethyst'                    : Amethyst color theme
// 'city'                        : City color theme
// 'flat'                        : Flat color theme
// 'modern'                      : Modern color theme
// 'smooth'                      : Smooth color theme
$one->theme                      = '';

// true                          : Enables Page Loader screen
// false                         : Disables Page Loader screen
$one->page_loader                = false;

// true                          : Remembers active color theme between pages using
//                                 localStorage when set through
//                                 - Theme helper buttons [data-toggle="theme"]
// false                         : Does not remember the active color theme
$one->remember_theme             = true;


// **************************************************************************************************
// GLOBAL INCLUDED VIEWS
// **************************************************************************************************

//                               : Useful for adding different sidebars/headers per page or per section
$one->inc_side_overlay           = '';
$one->inc_sidebar                = '';
$one->inc_header                 = '';
$one->inc_footer                 = '';


// **************************************************************************************************
// GLOBAL SIDEBAR & SIDE OVERLAY
// **************************************************************************************************

// true                          : Left Sidebar and right Side Overlay
// false                         : Right Sidebar and left Side Overlay
$one->l_sidebar_left             = true;

// true                          : Mini hoverable Sidebar (screen width > 991px)
// false                         : Normal mode
$one->l_sidebar_mini             = false;

// true                          : Visible Sidebar (screen width > 991px)
// false                         : Hidden Sidebar (screen width > 991px)
$one->l_sidebar_visible_desktop  = true;

// true                          : Visible Sidebar (screen width < 992px)
// false                         : Hidden Sidebar (screen width < 992px)
$one->l_sidebar_visible_mobile   = false;

// true                          : Dark themed Sidebar
// false                         : Light themed Sidebar (works with Dark Mode off)
$one->l_sidebar_dark             = true;

// true                          : Hoverable Side Overlay (screen width > 991px)
// false                         : Normal mode
$one->l_side_overlay_hoverable   = false;

// true                          : Visible Side Overlay
// false                         : Hidden Side Overlay
$one->l_side_overlay_visible     = false;

// true                          : Enables a visible clickable (closes Side Overlay) Page Overlay when Side Overlay opens
// false                         : Disables Page Overlay when Side Overlay opens
$one->l_page_overlay             = true;

// true                          : Custom scrolling (screen width > 991px)
// false                         : Native scrolling
$one->l_side_scroll              = true;


// **************************************************************************************************
// GLOBAL HEADER
// **************************************************************************************************

// true                          : Fixed Header
// false                         : Static Header
$one->l_header_fixed             = true;

// true                          : Dark themed Header
// false                         : Light themed Header (works with Dark Mode off)
$one->l_header_dark              = false;


// **************************************************************************************************
// GLOBAL MAIN CONTENT
// **************************************************************************************************

// ''                            : Full width Main Content
// 'boxed'                       : Full width Main Content with a specific maximum width (screen width > 1200px)
// 'narrow'                      : Full width Main Content with a percentage width (screen width > 1200px)
$one->l_m_content                = '';


// **************************************************************************************************
// GLOBAL MAIN MENU
// **************************************************************************************************

// It will get compared with the url of each menu link to make the link active and set up main menu accordingly
// If you are using query strings to load different pages, you can use the following value: basename($_SERVER['REQUEST_URI'])
$one->main_nav_active            = basename($_SERVER['PHP_SELF']);

// You can use the following array to create your main menu
$one->main_nav                   = array();
