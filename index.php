<?php
/**
 * Front Controller
 * Entry point for all requests
 */

// Load bootstrap
$app = require_once __DIR__ . '/src/bootstrap.php';

// Extract app variables
$config = $app['config'];
$db = $app['db'];

// Load model files
require_once __DIR__ . '/app/models/Product.php';
require_once __DIR__ . '/app/models/Customer.php';
require_once __DIR__ . '/app/models/Order.php';
require_once __DIR__ . '/app/models/Category.php';

// Load controller files
require_once __DIR__ . '/app/controllers/ProductController.php';
require_once __DIR__ . '/app/controllers/OrderController.php';
require_once __DIR__ . '/app/controllers/AuthController.php';
require_once __DIR__ . '/app/controllers/ReportController.php';

// Initialize models
$productModel = new Product($db);
$customerModel = new Customer($db);
$orderModel = new Order($db);
$categoryModel = new Category($db);

// Initialize router
$router = new Router();

// Define routes
$router->get('/', function() {
    view('home/index', ['title' => 'Sale Management System']);
});

// Authentication routes
$router->get('/login', function() {
    $authController = new AuthController();
    $authController->showLoginForm();
});

$router->post('/login', function() {
    $authController = new AuthController();
    $authController->login();
});

$router->get('/logout', function() {
    $authController = new AuthController();
    $authController->logout();
});


// Product routes using controller
$router->get('/products', function() {
    $productController = new ProductController();
    $productController->index();
});

$router->get('/products/category/{id}', function($id) {
    $productController = new ProductController();
    $productController->filterByCategory($id);
});

$router->get('/products/create', function() {
    $productController = new ProductController();
    $productController->create();
});

$router->post('/products/create', function() {
    $productController = new ProductController();
    $productController->store();
});

$router->get('/products/{id}/edit', function($id) {
    $productController = new ProductController();
    $productController->edit($id);
});

$router->post('/products/{id}/edit', function($id) {
    $productController = new ProductController();
    $productController->update($id);
});

// Order routes
$router->get('/orders', function() {
    $orderController = new OrderController();
    $orderController->index();
});

$router->get('/orders/create', function() {
    $orderController = new OrderController();
    $orderController->create();
});

$router->post('/orders/create', function() {
    $orderController = new OrderController();
    $orderController->store();
});

$router->get('/orders/{id}', function($id) {
    $orderController = new OrderController();
    $orderController->view($id);
});

$router->get('/orders/{id}/print', function($id) {
    $orderController = new OrderController();
    $orderController->printOrder($id);
});

// Template route for order preview
$router->post('/orders/template', function() {
    $orderController = new OrderController();
    $orderController->renderTemplate();
});

// Customer purchase report
$router->get('/reports/customer', function() {
    $reportController = new ReportController();
    $reportController->customerReport();
});

$router->get('/reports/customer/{id}', function($id) {
    $reportController = new ReportController();
    $reportController->customerPurchaseHistory($id);
});

// Report routes
$router->get('/reports/sales', function() {
    $reportController = new ReportController();
    $reportController->salesReport();
});

// 404 handler
$router->notFound(function() {
    view('errors/404', ['title' => 'Page Not Found']);
});

// Dispatch the request
$router->dispatch(); 