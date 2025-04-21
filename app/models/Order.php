<?php
/**
 * Order Model
 */
class Order 
{
    private $db;
    
    public function __construct($db) 
    {
        $this->db = $db;
    }
    
    /**
     * Get all orders
     * 
     * @return array
     */
    public function getAll() 
    {
        return $this->db->fetchAll(
            "SELECT o.*, c.name as customer_name
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             ORDER BY o.order_date DESC"
        );
    }
    
    /**
     * Get order by ID
     * 
     * @param int $id Order ID
     * @return array|false
     */
    public function getById($id) 
    {
        return $this->db->fetch(
            "SELECT o.*, c.name as customer_name, c.email as customer_email, c.phone as customer_phone, c.address as customer_address
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             WHERE o.id = ?", 
            [$id]
        );
    }
    
    /**
     * Create new order
     * 
     * @param array $data Order data
     * @return int|false ID of the new order or false on failure
     */
    public function create($data) 
    {
        try {
            $this->db->beginTransaction();
            
            // Insert order
            $orderSql = "INSERT INTO orders (customer_id, order_date, total_amount, notes, created_at) 
                    VALUES (?, NOW(), ?, ?, NOW())";
                    
            $orderParams = [
                $data['customer_id'], 
                $data['total_amount'],
                isset($data['notes']) ? $data['notes'] : ''
            ];
            
            $this->db->execute($orderSql, $orderParams);
            $orderId = $this->db->lastInsertId();
            
            // Insert order items
            foreach ($data['items'] as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $unitPrice = $item['unit_price'];
                
                $subtotal = $quantity * $unitPrice;
                
                $itemSql = "INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal) 
                        VALUES (?, ?, ?, ?, ?)";
                        
                $itemParams = [
                    $orderId,
                    $productId,
                    $quantity,
                    $unitPrice,
                    $subtotal
                ];
                
                $this->db->execute($itemSql, $itemParams);
                
                // Update product stock
                $this->db->execute(
                    "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?",
                    [$quantity, $productId]
                );
            }
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            return false;
        }
    }

    
    /**
     * Get order items
     * 
     * @param int $orderId Order ID
     * @return array
     */
    public function getOrderItems($orderId) 
    {
        return $this->db->fetchAll(
            "SELECT oi.*, p.name as product_name
             FROM order_items oi
             LEFT JOIN products p ON oi.product_id = p.id
             WHERE oi.order_id = ?",
            [$orderId]
        );
    }
    
    /**
     * Get orders by date range
     * 
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array
     */
    public function getByDateRange($startDate, $endDate) 
    {
        return $this->db->fetchAll(
            "SELECT o.*, c.name as customer_name
             FROM orders o
             LEFT JOIN customers c ON o.customer_id = c.id
             WHERE DATE(o.order_date) BETWEEN ? AND ?
             ORDER BY o.order_date DESC", 
            [$startDate, $endDate]
        );
    }
    
    /**
     * Get sales summary by date
     * 
     * @param string $groupBy Group by 'day', 'month', or 'year'
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array
     */
    public function getSalesSummary($groupBy = 'day', $startDate = null, $endDate = null) 
    {
        $dateFormat = $groupBy === 'day' ? '%Y-%m-%d' : ($groupBy === 'month' ? '%Y-%m' : '%Y');
        
        $where = "";
        $params = [];
        
        if ($startDate && $endDate) {
            $where = "WHERE DATE(o.order_date) BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        $sql = "SELECT 
                DATE_FORMAT(o.order_date, '{$dateFormat}') as date_group,
                COUNT(o.id) as order_count,
                SUM(o.total_amount) as total_sales
                FROM orders o
                {$where}
                GROUP BY date_group
                ORDER BY date_group";
                
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get customer purchase history
     * 
     * @param int $customerId Customer ID
     * @param string $startDate Start date (YYYY-MM-DD)
     * @param string $endDate End date (YYYY-MM-DD)
     * @return array
     */
    public function getCustomerPurchaseHistory($customerId, $startDate, $endDate) 
    {
        // Get orders
        $orders = $this->db->fetchAll(
            "SELECT o.*
             FROM orders o
             WHERE o.customer_id = ? 
             AND DATE(o.order_date) BETWEEN ? AND ?
             ORDER BY o.order_date DESC", 
            [$customerId, $startDate, $endDate]
        );
        
        // Calculate summary
        $summary = [
            'total_amount' => 0,
            'order_count' => count($orders),
            'product_count' => 0
        ];
        
        $products = [];
        
        // Get detailed order items
        foreach ($orders as $key => $order) {
            $orderItems = $this->getOrderItems($order['id']);
            $orders[$key]['items'] = $orderItems;
            
            // Update summary
            $summary['total_amount'] += $order['total_amount'];
            
            // Track products
            foreach ($orderItems as $item) {
                $productId = $item['product_id'];
                if (!isset($products[$productId])) {
                    $products[$productId] = [
                        'product_id' => $productId,
                        'product_name' => $item['product_name'],
                        'quantity' => 0,
                        'total' => 0
                    ];
                }
                
                $products[$productId]['quantity'] += $item['quantity'];
                $products[$productId]['total'] += $item['subtotal'];
            }
        }
        
        $summary['product_count'] = count($products);
        
        return [
            'orders' => $orders,
            'summary' => $summary,
            'products' => array_values($products)
        ];
    }
    
} 