<?php
/**
 * Application Bootstrap File
 * This file initializes the application and loads all necessary configurations
 */

// Define application constants
define('APP_PATH', realpath(__DIR__ . '/../'));
define('CONFIG_PATH', APP_PATH . '/config');
define('APP_SRC_PATH', APP_PATH . '/src');
define('MODELS_PATH', APP_PATH . '/app/models');
define('CONTROLLERS_PATH', APP_PATH . '/app/controllers');
define('VIEWS_PATH', APP_PATH . '/app/views');
define('PUBLIC_PATH', APP_PATH . '/public');

// Load configuration
$config = require CONFIG_PATH . '/config.php';
$dbConfig = require CONFIG_PATH . '/database.php';

// Configure error reporting
if ($config['environment'] === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session
session_name($config['session']['name']);
session_start([
    'cookie_lifetime' => $config['session']['lifetime'],
    'cookie_path' => $config['session']['path'],
    'cookie_domain' => $config['session']['domain'],
    'cookie_secure' => $config['session']['secure'],
    'cookie_httponly' => $config['session']['httponly']
]);

// Autoloader for classes
spl_autoload_register(function ($class) {
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = APP_PATH . "/{$class}.php";
    
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    
    return false;
});

// Load database connection class
require_once APP_SRC_PATH . '/Database.php';

// Initialize database connection
$db = new Database($dbConfig);

// Include utility functions
require_once APP_SRC_PATH . '/functions.php';

// Load router
require_once APP_SRC_PATH . '/Router.php';

// Return config for use in application
return [
    'config' => $config,
    'db' => $db
]; 