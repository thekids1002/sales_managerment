<?php

/**
 * Order Controller
 * Handles all order-related operations
 */
class OrderController
{
    private $orderModel;
    private $customerModel;
    private $productModel;

    public function __construct($db = null)
    {
        if ($db) {
            $this->orderModel = new Order($db);
            $this->customerModel = new Customer($db);
            $this->productModel = new Product($db);
        } else {
            global $orderModel, $customerModel, $productModel;
            $this->orderModel = $orderModel;
            $this->customerModel = $customerModel;
            $this->productModel = $productModel;
        }
    }

    /**
     * Display list of all orders
     */
    public function index()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $orders = $this->orderModel->getAll();
        $drafts = isset($_SESSION['tempOrder']) ? $_SESSION['tempOrder'] : [];
        
        // Merge orders and drafts
        $orders = array_merge($orders, $drafts);

        view('orders/index', [
            'title' => 'Orders',
            'orders' => $orders
        ]);
    }

    /**
     * Show create order form
     */
    public function create()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $customers = $this->customerModel->getAll();
        $products = $this->productModel->getAll();

        view('orders/create', [
            'title' => 'Create New Order',
            'customers' => $customers,
            'products' => $products
        ]);
    }

    /**
     * Show order details - checks both database and session
     */
    public function view($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        // Check if it's a draft order in session
        if (isset($_SESSION['tempOrder']) && isset($_SESSION['tempOrder'][$id])) {
            $order = $_SESSION['tempOrder'][$id];
            $orderItems = $order['items'];
            
            view('orders/view', [
                'title' => 'Draft Order Details',
                'order' => $order,
                'items' => $orderItems,
                'isDraft' => true
            ]);
            return;
        }
        
        // Check if it's a regular order in database
        $order = $this->orderModel->getById($id);

        if (!$order) {
            // If not found in either place, redirect to 404
            view('errors/404', ['title' => 'Order Not Found']);
            return;
        }

        $orderItems = $this->orderModel->getOrderItems($id);

        view('orders/view', [
            'title' => 'Order Details',
            'order' => $order,
            'items' => $orderItems,
            'isDraft' => false
        ]);
    }

    /**
     * Print order - regular order from database
     */
    public function printOrder($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $isDraft = false;

        $order = $this->orderModel->getById($id);

        if (!$order) {
            if (!isset($_SESSION['tempOrder']) || !isset($_SESSION['tempOrder'][$id])) {
                view('errors/404', ['title' => 'Draft Order Not Found']);
                return;
            }
            $order = $_SESSION['tempOrder'][$id];
            $isDraft = true;
        }

        $orderItems = $this->orderModel->getOrderItems($id);

        view('orders/print', [
            'title' => 'Print Order',
            'order' => $order,
            'items' => $orderItems,
            'isDraft' => $isDraft
        ]);
    }

    /**
     * Print draft order - draft from session
     */
    public function printDraft($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        if (!isset($_SESSION['tempOrder']) || !isset($_SESSION['tempOrder'][$id])) {
            view('errors/404', ['title' => 'Draft Order Not Found']);
            return;
        }

        $order = $_SESSION['tempOrder'][$id];
        $items = $order['items'];

        view('orders/print', [
            'title' => 'Print Draft Order',
            'order' => $order,
            'items' => $items,
            'isDraft' => true
        ]);
    }

    public function saveTempOrder()
    {
        if (!isLoggedIn()) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Not logged in']);
            exit;
        }

        // Get JSON input
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (!$data) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Invalid data format']);
            exit;
        }

        // Get order data from JSON
        $customerId = intval(isset($data['customer_id']) ? $data['customer_id'] : 0);
        $notes = trim(isset($data['notes']) ? $data['notes'] : '');
        $items = isset($data['items']) ? $data['items'] : [];

        if ($customerId <= 0 || empty($items)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Customer and at least one product are required']);
            exit;
        }

        // Get customer information
        $customer = $this->customerModel->getById($customerId);
        if (!$customer) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Customer not found']);
            exit;
        }

        // Prepare order items with product information
        $orderItems = [];
        $totalAmount = 0;

        foreach ($items as $item) {
            $productId = intval(isset($item['product_id']) ? $item['product_id'] : 0);
            $quantity = intval(isset($item['quantity']) ? $item['quantity'] : 0);
            $unitPrice = floatval(isset($item['unit_price']) ? $item['unit_price'] : 0);

            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }

            $product = $this->productModel->getById($productId);
            if (!$product) {
                continue;
            }

            $subtotal = $unitPrice * $quantity;
            $totalAmount += $subtotal;

            $orderItems[] = [
                'product_id' => $productId,
                'product_name' => $product['name'],
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $subtotal
            ];
        }

        if (empty($orderItems)) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Please add at least one valid product to the order']);
            exit;
        }

        $orderId = uniqid();
        $orderData = [
            'id' => $orderId,
            'customer_id' => $customerId,
            'customer_name' => $customer['name'],
            'customer_email' => $customer['email'],
            'customer_phone' => $customer['phone'],
            'customer_address' => $customer['address'],
            'notes' => $notes,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'total_amount' => $totalAmount,
            'items' => $orderItems,
        ];

        // Store order data in session
        if (!isset($_SESSION['tempOrder'])) {
            $_SESSION['tempOrder'] = [];
        }
        $_SESSION['tempOrder'][$orderId] = $orderData;
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'id' => $orderId]);
        exit;
    }

    /**
     * Save draft order to database
     */
    public function saveDraftToDatabase($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        // Check CSRF token
        if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
            flash('error', 'Invalid request');
            redirect('/orders');
        }
        
        // Check if draft exists
        if (!isset($_SESSION['tempOrder'][$id])) {
            flash('error', 'Draft order not found');
            redirect('/orders');
        }
        
        $draft = $_SESSION['tempOrder'][$id];
        
        // Calculate total amount and validate items
        $totalAmount = 0;
        $orderItems = [];
        
        foreach ($draft['items'] as $item) {
            $productId = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            $unitPrice = floatval($item['unit_price']);
            
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            
            $product = $this->productModel->getById($productId);
            if (!$product) {
                continue;
            }
            
            // Check if enough stock - use quantity field returned by getById
            if ($product['quantity'] < $quantity) {
                flash('error', "Not enough stock for product: {$product['name']}");
                redirect("/orders/{$id}");
            }
            
            $itemTotal = $unitPrice * $quantity;
            $totalAmount += $itemTotal;
            
            $orderItems[] = [
                'product_id' => $productId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice
            ];
        }
        
        if (empty($orderItems)) {
            flash('error', 'Please add at least one valid product to the order');
            redirect("/orders/{$id}");
        }
        
        $orderData = [
            'customer_id' => $draft['customer_id'],
            'total_amount' => $totalAmount,
            'notes' => $draft['notes'],
            'items' => $orderItems
        ];
        
        // Create real order
        if ($orderId = $this->orderModel->create($orderData)) {
            // Remove draft from session
            unset($_SESSION['tempOrder'][$id]);
            
            flash('success', 'Draft order converted to real order successfully');
            redirect("/orders/{$orderId}");
        } else {
            flash('error', 'Failed to convert draft order');
            redirect("/orders/{$id}");
        }
    }
}
