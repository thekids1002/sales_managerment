<?php
/**
 * Category Model
 */
class Category 
{
    private $db;
    
    public function __construct($db) 
    {
        $this->db = $db;
    }
    
    /**
     * Get all categories
     * 
     * @return array
     */
    public function getAll() 
    {
        return $this->db->fetchAll("SELECT * FROM categories ORDER BY name");
    }
    
    /**
     * Get category by ID
     * 
     * @param int $id Category ID
     * @return array|false
     */
    public function getById($id) 
    {
        return $this->db->fetch("SELECT * FROM categories WHERE id = ?", [$id]);
    }
    
    /**
     * Get all categories with product count
     * 
     * @return array
     */
    public function getAllWithProductCount() 
    {
        return $this->db->fetchAll(
            "SELECT c.*, COUNT(p.id) as product_count 
             FROM categories c
             LEFT JOIN products p ON c.id = p.category_id
             GROUP BY c.id
             ORDER BY c.name"
        );
    }
    
    /**
     * Get products in category
     * 
     * @param int $categoryId Category ID
     * @return array
     */
    public function getProducts($categoryId) 
    {
        return $this->db->fetchAll(
            "SELECT *, stock_quantity as quantity 
             FROM products 
             WHERE category_id = ?
             ORDER BY name", 
            [$categoryId]
        );
    }
    
    /**
     * Delete category
     * 
     * @param int $id Category ID
     * @return bool
     */
    public function delete($id) 
    {
        return $this->db->execute("DELETE FROM categories WHERE id = ?", [$id]);
    }
} 