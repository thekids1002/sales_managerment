<?php
/**
 * Public Entry Point
 * This is the file accessed by the web server
 */

// Change to the parent directory
chdir(dirname(__DIR__));

// Load the front controller
require_once 'index.php'; 