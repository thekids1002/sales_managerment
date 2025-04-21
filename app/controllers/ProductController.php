<?php

/**
 * Product Controller
 * Handles all product-related operations
 */
class ProductController
{
    private $productModel;
    private $categoryModel;

    public function __construct($db = null)
    {
        if ($db) {
            $this->productModel = new Product($db);
            $this->categoryModel = new Category($db);
        } else {
            global $productModel, $categoryModel;
            $this->productModel = $productModel;
            $this->categoryModel = $categoryModel;
        }
    }

    /**
     * Display list of all products
     */
    public function index()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;

        if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
            $categoryId = (int)$_GET['category_id'];
            $products = $this->productModel->getByCategoryPaginated($categoryId, $page, $perPage);
        } else {
            $products = $this->productModel->getPaginated($page, $perPage);
        }

        $categories = $this->categoryModel->getAll();

        view('products/index', [
            'title' => 'Products',
            'products' => $products['data'],
            'categories' => $categories,
            'pagination' => [
                'total' => $products['total'],
                'per_page' => $products['per_page'],
                'current_page' => $products['current_page'],
                'last_page' => $products['last_page']
            ]
        ]);
    }

    /**
     * Show create product form
     */
    public function create()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $categories = $this->categoryModel->getAll();

        view('products/create', [
            'title' => 'Add New Product',
            'categories' => $categories
        ]);
    }

    /**
     * Store new product
     */
    public function store()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        // Check CSRF token
        if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
            flash('error', 'Invalid request');
            redirect('/products');
        }

        // Validate input
        $category_id = empty($_POST['category_id']) ? null : intval($_POST['category_id']);
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

        if (empty($name) || $price <= 0) {
            flash('error', 'Name and valid price are required');
            redirect('/products/create');
        }

        $productData = [
            'category_id' => $category_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'quantity' => $quantity
        ];

        if ($this->productModel->create($productData)) {
            flash('success', 'Product created successfully');
            redirect('/products');
        } else {
            flash('error', 'Failed to create product');
            redirect('/products/create');
        }
    }

    /**
     * Show edit product form
     */
    public function edit($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $product = $this->productModel->getById($id);

        if (!$product) {
            flash('error', 'Product not found');
            redirect('/products');
        }

        $categories = $this->categoryModel->getAll();

        view('products/edit', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Update product
     */
    public function update($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        // Check CSRF token
        if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
            flash('error', 'Invalid request');
            redirect('/products');
        }

        // Validate input for product editing
        $category_id = empty($_POST['category_id']) ? null : intval($_POST['category_id']);
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $description = isset($_POST['description']) ? trim($_POST['description']) : '';
        $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
        $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

        if (empty($name) || $price <= 0) {
            flash('error', 'Name and valid price are required');
            redirect("/products/{$id}/edit");
        }

        $productData = [
            'category_id' => $category_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'quantity' => $quantity
        ];

        if ($this->productModel->update($id, $productData)) {
            flash('success', 'Product updated successfully');
            redirect('/products');
        } else {
            flash('error', 'Failed to update product');
            redirect("/products/{$id}/edit");
        }
    }

    /**
     * Filter products by category
     */
    public function filterByCategory($categoryId)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 10;
        
        $products = $this->productModel->getByCategoryPaginated($categoryId, $page, $perPage);
        $categories = $this->categoryModel->getAll();
        $currentCategory = $this->categoryModel->getById($categoryId);
        
        view('products/index', [
            'title' => 'Products in ' . ($currentCategory ? $currentCategory['name'] : 'Category'),
            'products' => $products['data'],
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'pagination' => [
                'total' => $products['total'],
                'per_page' => $products['per_page'],
                'current_page' => $products['current_page'],
                'last_page' => $products['last_page']
            ]
        ]);
    }
}
