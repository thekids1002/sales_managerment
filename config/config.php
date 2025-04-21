<?php
/**
 * Application Configuration
 */
return [
    // Base URL of the application
    'base_url' => 'http://localhost/sale_managerment',
    
    // Application environment (development, testing, production)
    'environment' => 'development',
    
    // Logging settings
    'log' => [
        'enabled' => true,
        'path' => __DIR__ . '/../logs',
        'level' => 'debug', // debug, info, warning, error
    ],
    
    // Session settings
    'session' => [
        'name' => 'sale_management_session',
        'lifetime' => 7200, // 2 hours
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true
    ],

    // Error display (set to false in production)
    'display_errors' => true
]; 