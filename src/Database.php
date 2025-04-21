<?php
/**
 * Database Connection Class
 */
class Database
{
    private $connection;
    private $config;
    
    /**
     * Constructor
     * 
     * @param array $config Database configuration
     */
    public function __construct(array $config) 
    {
        $this->config = $config;
        $this->connect();
    }
    
    /**
     * Establish database connection
     */
    private function connect() 
    {
        try {
            $dsn = "mysql:host={$this->config['host']};dbname={$this->config['database']};charset={$this->config['charset']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->config['username'], $this->config['password'], $options);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get database connection
     * 
     * @return PDO
     */
    public function getConnection() 
    {
        return $this->connection;
    }
    
    /**
     * Execute a query
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return PDOStatement
     */
    public function query($query, $params = []) 
    {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
    }
    
    /**
     * Begin a database transaction
     * 
     * @return bool
     */
    public function beginTransaction() 
    {
        return $this->connection->beginTransaction();
    }
    
    /**
     * Commit a database transaction
     * 
     * @return bool
     */
    public function commit() 
    {
        return $this->connection->commit();
    }
    
    /**
     * Rollback a database transaction
     * 
     * @return bool
     */
    public function rollback() 
    {
        return $this->connection->rollBack();
    }
    
    /**
     * Get the last inserted ID
     * 
     * @return string
     */
    public function lastInsertId() 
    {
        return $this->connection->lastInsertId();
    }
    
    /**
     * Execute a query and return success
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return bool
     */
    public function execute($query, $params = []) 
    {
        return $this->query($query, $params) ? true : false;
    }
    
    /**
     * Fetch all records
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return array
     */
    public function fetchAll($query, $params = []) 
    {
        return $this->query($query, $params)->fetchAll();
    }
    
    /**
     * Fetch single record
     * 
     * @param string $query SQL query
     * @param array $params Parameters for prepared statement
     * @return array|null
     */
    public function fetch($query, $params = []) 
    {
        return $this->query($query, $params)->fetch();
    }
    
    /**
     * Insert record
     * 
     * @param string $table Table name
     * @param array $data Data to insert
     * @return int Last insert ID
     */
    public function insert($table, $data) 
    {
        // Create column string
        $columns = implode(', ', array_keys($data));
        
        // Create placeholder string
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        // Create query
        $query = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        
        // Execute query
        $this->query($query, array_values($data));
        
        // Return last insert ID
        return $this->connection->lastInsertId();
    }
    
    /**
     * Update record
     * 
     * @param string $table Table name
     * @param array $data Data to update
     * @param array $where Where condition
     * @return int Number of affected rows
     */
    public function update($table, $data, $where) 
    {
        // Create set string
        $set = implode(' = ?, ', array_keys($data)) . ' = ?';
        
        // Create where string
        $whereClause = implode(' = ? AND ', array_keys($where)) . ' = ?';
        
        // Create query
        $query = "UPDATE {$table} SET {$set} WHERE {$whereClause}";
        
        // Execute query
        $stmt = $this->query($query, array_merge(array_values($data), array_values($where)));
        
        // Return number of affected rows
        return $stmt->rowCount();
    }
    
    /**
     * Delete record
     * 
     * @param string $table Table name
     * @param array $where Where condition
     * @return int Number of affected rows
     */
    public function delete($table, $where) 
    {
        // Create where string
        $whereClause = implode(' = ? AND ', array_keys($where)) . ' = ?';
        
        // Create query
        $query = "DELETE FROM {$table} WHERE {$whereClause}";
        
        // Execute query
        $stmt = $this->query($query, array_values($where));
        
        // Return number of affected rows
        return $stmt->rowCount();
    }
} 