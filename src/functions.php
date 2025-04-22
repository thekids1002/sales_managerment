<?php
/**
 * Helper Functions
 */

/**
 * Render a view
 * 
 * @param string $view View file path
 * @param array $data Data to pass to the view
 * @return void
 */
function view($view, $data = []) {
    // Extract data to make variables available in the view
    extract($data);
    
    // Start output buffering
    ob_start();
    
    // Include the view file
    $viewPath = VIEWS_PATH . '/' . $view . '.php';
    if (file_exists($viewPath)) {
        include $viewPath;
    } else {
        throw new Exception("View '{$view}' not found.");
    }
    
    // Get the buffered content
    $content = ob_get_clean();
    
    // Output the content
    echo $content;
}

/**
 * Redirect to a URL
 * 
 * @param string $url URL to redirect to
 * @return void
 */
function redirect($url) {
    header("Location: " . baseUrl($url));
    exit;
}

/**
 * Get base URL
 * 
 * @param string $path Path to append to base URL
 * @return string Full URL
 */
function baseUrl($path = '') {
    global $config;
    $baseUrl = $config['base_url'];
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Sanitize output
 * 
 * @param string $value Value to sanitize
 * @return string Sanitized value
 */
function e($value) {
    return !empty($value) ? htmlspecialchars($value, ENT_QUOTES, 'UTF-8') : '';
}

/**
 * Check if user is logged in
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user
 * 
 * @return array|null User data or null if not logged in
 */
function currentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $db;
    $userId = $_SESSION['user_id'];
    
    return $db->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
}

/**
 * Flash message
 * 
 * @param string $key Message key
 * @param string $message Message to flash
 * @return string|null Message or null if not set
 */
function flash($key, $message = null) {
    if ($message) {
        // Set flash message
        $_SESSION['flash'][$key] = $message;
        return null;
    } else {
        // Get and clear flash message
        $message = isset($_SESSION['flash'][$key]) ? $_SESSION['flash'][$key] : null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }
}

/**
 * Generate CSRF token
 * 
 * @return string CSRF token
 */
function csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 * 
 * @param string $token Token to verify
 * @return bool True if token is valid, false otherwise
 */
function csrf_verify($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Format price
 * 
 * @param float $price Price to format
 * @param string $locale Locale for formatting (default: 'vi_VN')
 * @return string Formatted price
 */
function formatPrice($price, $locale = 'vi_VN') {
    // Format price as a currency string
    return number_format($price, 2, ',', '.') . ' VND';
}

/**
 * Format date
 * 
 * @param string $date Date to format
 * @param string $format Format string
 * @return string Formatted date
 */
function formatDate($date, $format = 'Y-m-d H:i:s') {
    return date($format, strtotime($date));
}

/**
 * Debug variable
 * 
 * @param mixed $var Variable to debug
 * @param bool $die Whether to die after debug
 * @return void
 */
function debug($var, $die = true) {
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
    
    if ($die) {
        die();
    }
} 