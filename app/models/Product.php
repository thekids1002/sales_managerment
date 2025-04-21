<?php
/**
 * Product Model
 */
class Product 
{
    private $db;
    
    public function __construct($db) 
    {
        $this->db = $db;
    }
    
    /**
     * Get all products
     * 
     * @return array
     */
    public function getAll() 
    {
        return $this->db->fetchAll("SELECT p.*, p.stock_quantity as quantity, c.name as category_name 
                                    FROM products p 
                                    LEFT JOIN categories c ON p.category_id = c.id 
                                    ORDER BY p.name");
    }
    
    /**
     * Get product by ID
     * 
     * @param int $id Product ID
     * @return array|false
     */
    public function getById($id) 
    {
        return $this->db->fetch("SELECT p.*, p.stock_quantity as quantity, c.name as category_name 
                                FROM products p 
                                LEFT JOIN categories c ON p.category_id = c.id 
                                WHERE p.id = ?", [$id]);
    }
    
    /**
     * Create new product
     * 
     * @param array $data Product data
     * @return int|false ID of the new product or false on failure
     */
    public function create($data) 
    {
        $sql = "INSERT INTO products (category_id, name, description, price, stock_quantity, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())";
                
        $params = [
            isset($data['category_id']) ? $data['category_id'] : null,
            $data['name'], 
            $data['description'], 
            $data['price'], 
            $data['quantity'] // Map 'quantity' from form to 'stock_quantity' in database
        ];
        
        return $this->db->execute($sql, $params) ? $this->db->lastInsertId() : false;
    }
    
    /**
     * Update product
     * 
     * @param int $id Product ID
     * @param array $data Product data
     * @return bool
     */
    public function update($id, $data) 
    {
        $sql = "UPDATE products 
                SET category_id = ?,
                    name = ?, 
                    description = ?, 
                    price = ?, 
                    stock_quantity = ?, 
                    updated_at = NOW() 
                WHERE id = ?";
                
        $params = [
            isset($data['category_id']) ? $data['category_id'] : null,
            $data['name'], 
            $data['description'], 
            $data['price'], 
            $data['quantity'], // Map 'quantity' from form to 'stock_quantity' in database
            $id
        ];
        
        return $this->db->execute($sql, $params);
    }
    
    /**
     * Delete product
     * 
     * @param int $id Product ID
     * @return bool
     */
    public function delete($id) 
    {
        return $this->db->execute("DELETE FROM products WHERE id = ?", [$id]);
    }
    
    /**
     * Get low stock products (quantity < 10)
     * 
     * @return array
     */
    public function getLowStock() 
    {
        return $this->db->fetchAll("SELECT p.*, p.stock_quantity as quantity, c.name as category_name 
                                   FROM products p
                                   LEFT JOIN categories c ON p.category_id = c.id 
                                   WHERE p.stock_quantity < 10 
                                   ORDER BY p.stock_quantity ASC");
    }
    
    /**
     * Update product stock
     * 
     * @param int $id Product ID
     * @param int $quantity Quantity to add (positive) or subtract (negative)
     * @return bool
     */
    public function updateStock($id, $quantity) 
    {
        return $this->db->execute(
            "UPDATE products SET stock_quantity = stock_quantity + ?, updated_at = NOW() 
             WHERE id = ?", 
            [$quantity, $id]
        );
    }
    
    /**
     * Get paginated products
     * 
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @return array
     */
    public function getPaginated($page = 1, $perPage = 10) 
    {
        $offset = ($page - 1) * $perPage;
        
        $products = $this->db->fetchAll(
            "SELECT p.*, p.stock_quantity as quantity, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             ORDER BY p.name
             LIMIT ? OFFSET ?", 
            [$perPage, $offset]
        );
        
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM products");
        $total = $result['count'];
        
        return [
            'data' => $products,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get paginated products by category
     * 
     * @param int $categoryId Category ID
     * @param int $page Current page number
     * @param int $perPage Number of items per page
     * @return array
     */
    public function getByCategoryPaginated($categoryId, $page = 1, $perPage = 10) 
    {
        $offset = ($page - 1) * $perPage;
        
        $products = $this->db->fetchAll(
            "SELECT p.*, p.stock_quantity as quantity, c.name as category_name
             FROM products p
             LEFT JOIN categories c ON p.category_id = c.id
             WHERE p.category_id = ?
             ORDER BY p.name
             LIMIT ? OFFSET ?", 
            [$categoryId, $perPage, $offset]
        );
        
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM products WHERE category_id = ?",
            [$categoryId]
        );
        $total = $result['count'];
        
        return [
            'data' => $products,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
} 