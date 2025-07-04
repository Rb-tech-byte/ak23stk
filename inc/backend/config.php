<?php
/**
 * backend/config.php
 *
 * Author: pixelcave
 * 
 * Backend pages configuration file
 */

// **************************************************************************************************
// INCLUDED VIEWS
// **************************************************************************************************

$one->inc_side_overlay = 'inc/backend/views/inc_side_overlay.php';
$one->inc_sidebar      = 'inc/backend/views/inc_sidebar.php';
$one->inc_header       = 'inc/backend/views/inc_header.php';
$one->inc_footer       = 'inc/backend/views/inc_footer.php';

// **************************************************************************************************
// MAIN CONTENT
// **************************************************************************************************

$one->l_m_content = 'narrow';

// **************************************************************************************************
// MAIN MENU
// **************************************************************************************************

$one->main_nav = array(
    array(
        'name' => 'Dashboard',
        'icon' => 'si si-speedometer',
        'url'  => 'users_dashboard.php'
    ),
    array(
        'name' => 'Category',
        'icon' => 'fa fa-list',
        'url'  => 'categories.php'
    ),
    array(
        'name' => 'Products',
        'icon' => 'fa fa-box',
        'url'  => 'products.php' // ✅ Direct to products.php
    ),
    array(
        'name' => 'Payments',
        'icon' => 'fa fa-credit-card',
        'url'  => 'payments.php' // ✅ Direct to payments.php
    ),
    array(
        'name' => 'Users',
        'icon' => 'fa fa-users',
        'url'  => 'users.php'
    ),
    array(
        'name' => 'Tickets',
        'icon' => 'fa fa-ticket-alt',
        'url'  => 'support.php'
    ),
    array(
        'name' => 'Medias',
        'icon' => 'fa fa-photo-video',
        'url'  => 'medias.php'
    ),
    array(
        'name' => 'General Setting',
        'icon' => 'fa fa-cogs',
        'sub'  => array(
            array(
                'name' => 'Mail SMTP',
                'url'  => 'mail_smtp.php'
            ),
            array(
                'name' => 'SMS',
                'url'  => 'sms_setting.php'
            ),
            array(
                'name' => 'Update Name',
                'url'  => 'update_system_name.php'
            ),
            array(
                'name' => 'Update Logo',
                'url'  => 'update_logo.php'
            ),
            array(
                'name' => 'Storage File',
                'url'  => 'file_storage.php'
            )
        )
    )
);
