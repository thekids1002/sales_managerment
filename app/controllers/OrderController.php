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

        if (!isLoggedIn()) {
            redirect('/login');
        }
    }

    /**
     * Display list of all orders
     */
    public function index()
    {
        
        $orders = $this->orderModel->getAll();

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
        
        // Check if it's a regular order in database
        $order = $this->orderModel->getById($id);

        if (!$order) {
            view('errors/404', ['title' => 'Order Not Found']);
            return;
        }

        $orderItems = $this->orderModel->getOrderItems($id);

        view('orders/view', [
            'title' => 'Order Details',
            'order' => $order,
            'items' => $orderItems
        ]);
    }

    /**
     * Print order
     */
    public function printOrder($id)
    {
        

        $order = $this->orderModel->getById($id);

        if (!$order) {
            view('errors/404', ['title' => 'Order Not Found']);
            return;
        }

        $orderItems = $this->orderModel->getOrderItems($id);

        view('orders/print', [
            'title' => 'Print Order',
            'order' => $order,
            'items' => $orderItems
        ]);
    }

    /**
     * Store a new order
     */
    public function store()
    {
        
        
        // Check CSRF token
        if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
            flash('error', 'Invalid request');
            redirect('/orders/create');
            return;
        }
        
        // Get form data
        $customerId = isset($_POST['customer_id']) ? (int)$_POST['customer_id'] : 0;
        $notes = isset($_POST['notes']) ? $_POST['notes'] : '';
        $items = isset($_POST['items']) ? $_POST['items'] : [];
        
        // Validate
        if ($customerId <= 0) {
            flash('error', 'Please select a customer');
            redirect('/orders/create');
            return;
        }
        
        if (empty($items)) {
            flash('error', 'Please add at least one product to the order');
            redirect('/orders/create');
            return;
        }
        
        // Calculate total amount and validate items
        $totalAmount = 0;
        $orderItems = [];
        
        foreach ($items as $item) {
            $productId = isset($item['product_id']) ? (int)$item['product_id'] : 0;
            $quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
            
            if ($productId <= 0 || $quantity <= 0) {
                continue;
            }
            
            $product = $this->productModel->getById($productId);
            if (!$product) {
                continue;
            }
            
            // Check if enough stock
            if ($product['quantity'] < $quantity) {
                flash('error', "Not enough stock for product: {$product['name']}");
                redirect('/orders/create');
                return;
            }
            
            $unitPrice = $product['price'];
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
            redirect('/orders/create');
            return;
        }
        
        // Create order in database
        $orderData = [
            'customer_id' => $customerId,
            'total_amount' => $totalAmount,
            'notes' => $notes,
            'items' => $orderItems
        ];
        
        $orderId = $this->orderModel->create($orderData);
        
        if ($orderId) {
            flash('success', 'Order created successfully');
            redirect("/orders/{$orderId}");
        } else {
            flash('error', 'Unable to create order. Please try again');
            redirect('/orders/create');
        }
    }
}
