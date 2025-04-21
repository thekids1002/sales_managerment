<?php
/**
 * Customer Model
 */
class Customer 
{
    private $db;
    
    public function __construct($db) 
    {
        $this->db = $db;
    }
    
    /**
     * Get all customers
     * 
     * @return array
     */
    public function getAll() 
    {
        return $this->db->fetchAll("SELECT * FROM customers ORDER BY name");
    }
    
    /**
     * Get customer by ID
     * 
     * @param int $id Customer ID
     * @return array|false
     */
    public function getById($id) 
    {
        return $this->db->fetch("SELECT * FROM customers WHERE id = ?", [$id]);
    }
} 