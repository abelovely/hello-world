<?php
/**
 * Database Configuration
 * General Wingate Polytechnic College - Employee Management System
 */

class Database {
    // Database credentials
    private $host = 'localhost';
    private $db_name = 'gwpc_employee_management';
    private $username = 'root';
    private $password = '';
    private $charset = 'utf8mb4';
    
    private $conn = null;
    
    /**
     * Get database connection
     * @return PDO|null
     */
    public function getConnection() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_PERSISTENT         => false
            ];
            
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
            return $this->conn;
            
        } catch(PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
