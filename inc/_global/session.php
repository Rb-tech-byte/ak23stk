<?php
/**
 * Session Management
 * 
 * Handles session initialization and security settings
 */

// Only initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set session cookie parameters before starting the session
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    $httponly = true;
    
    // Set the session cookie parameters
    session_set_cookie_params([
        'lifetime' => 0, // Until the browser is closed
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'],
        'secure' => $secure,
        'httponly' => $httponly,
        'samesite' => 'Lax' // or 'Strict' for more security
    ]);
    
    // Start the session
    session_start();
    
    // Regenerate session ID to prevent session fixation
    if (!isset($_SESSION['created'])) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    } 
    // Regenerate session ID every 30 minutes
    else if (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// Initialize CSRF token if not exists
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
