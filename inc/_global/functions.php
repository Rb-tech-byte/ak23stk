<?php
/**
 * Global Helper Functions
 * 
 * Contains utility functions used throughout the application.
 */

if (!function_exists('pre')) {
    /**
     * Pretty print data for debugging
     * 
     * @param mixed $data The data to print
     * @param bool $exit Whether to exit after printing
     */
    function pre($data, $exit = true) {
        echo '<pre>' . print_r($data, true) . '</pre>';
        if ($exit) exit;
    }
}

if (!function_exists('isSecure')) {
    /**
     * Check if the current request is using HTTPS
     * 
     * @return bool True if secure, false otherwise
     */
    function isSecure() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
               (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    }
}

if (!function_exists('base_url')) {
    /**
     * Get the base URL with an optional path
     * 
     * @param string $uri Optional path to append to base URL
     * @return string Full URL
     */
    function base_url($uri = '') {
        $base = defined('BASE_URL') ? rtrim(BASE_URL, '/') : '';
        $uri = ltrim($uri, '/');
        return $base . ($uri ? '/' . $uri : '');
    }
}

if (!function_exists('redirect')) {
    /**
     * Redirect to a URL
     * 
     * @param string $url URL to redirect to
     * @param int $statusCode HTTP status code (default: 302)
     */
    function redirect($url, $statusCode = 302) {
        if (!headers_sent()) {
            header('Location: ' . $url, true, $statusCode);
            exit;
        }
        
        // Fallback with JavaScript if headers already sent
        echo '<script>window.location.href="' . $url . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $url . '" /></noscript>';
        exit;
    }
}

if (!function_exists('sanitize_input')) {
    /**
     * Sanitize user input
     * 
     * @param mixed $data The input data to sanitize
     * @return mixed Sanitized data
     */
    function sanitize_input($data) {
        if (is_array($data)) {
            return array_map('sanitize_input', $data);
        }
        
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $data;
    }
}

if (!function_exists('csrf_token')) {
    /**
     * Generate or retrieve CSRF token
     * 
     * @return string CSRF token
     */
    function csrf_token() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('verify_csrf_token')) {
    /**
     * Verify CSRF token
     * 
     * @param string $token Token to verify
     * @return bool True if valid, false otherwise
     */
    function verify_csrf_token($token) {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('get_gravatar')) {
    /**
     * Get Gravatar URL for a given email address
     * 
     * @param string $email Email address
     * @param int $size Size in pixels (default: 80)
     * @return string Gravatar URL
     */
    function get_gravatar($email, $size = 80) {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s={$size}&d=mp";
    }
}

if (!function_exists('format_date')) {
    /**
     * Format a date string
     * 
     * @param string $date Date string
     * @param string $format Date format (default: 'M d, Y')
     * @return string Formatted date
     */
    function format_date($date, $format = 'M d, Y') {
        $date = new DateTime($date);
        return $date->format($format);
    }
}

if (!function_exists('generate_slug')) {
    /**
     * Generate a URL-friendly slug from a string
     * 
     * @param string $string The string to convert to a slug
     * @return string The generated slug
     */
    function generate_slug($string) {
        // Replace non-alphanumeric characters with hyphens
        $string = preg_replace('/[^a-z0-9-]/i', '-', $string);
        // Replace multiple hyphens with a single hyphen
        $string = preg_replace('/-+/', '-', $string);
        // Remove leading/trailing hyphens
        $string = trim($string, '-');
        // Convert to lowercase
        return strtolower($string);
    }
}
