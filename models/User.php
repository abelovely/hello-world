<?php
/**
 * User Model
 * Handles all user-related database operations (Admin and Supervisors)
 */

class User {
    private $conn;
    private $table = 'users';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Authenticate user
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function login($username, $password) {
        $query = "SELECT id, username, password, full_name, email, role, is_active 
                  FROM {$this->table} 
                  WHERE username = :username AND is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password'])) {
                unset($user['password']);
                return $user;
            }
        }
        return false;
    }
    
    /**
     * Get user by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT id, username, full_name, email, role, is_active, created_at 
                  FROM {$this->table} 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get all users
     * @param string $role Optional role filter
     * @return array
     */
    public function getAll($role = null) {
        $query = "SELECT id, username, full_name, email, role, is_active, created_at 
                  FROM {$this->table}";
        
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $query .= " ORDER BY full_name ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all supervisors
     * @return array
     */
    public function getAllSupervisors() {
        return $this->getAll('supervisor');
    }
    
    /**
     * Create new user
     * @param array $data
     * @return int|false User ID or false
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (username, password, full_name, email, role, is_active) 
                  VALUES (:username, :password, :full_name, :email, :role, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        $is_active = $data['is_active'] ?? 1;
        
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':is_active', $is_active);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update user
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET full_name = :full_name, 
                      email = :email, 
                      role = :role, 
                      is_active = :is_active";
        
        // Only update password if provided
        if (!empty($data['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':full_name', $data['full_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':role', $data['role']);
        $stmt->bindParam(':is_active', $data['is_active']);
        
        if (!empty($data['password'])) {
            $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashed_password);
        }
        
        return $stmt->execute();
    }
    
    /**
     * Delete user
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    /**
     * Check if username exists
     * @param string $username
     * @param int $excludeId
     * @return bool
     */
    public function usernameExists($username, $excludeId = null) {
        $query = "SELECT id FROM {$this->table} WHERE username = :username";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Check if email exists
     * @param string $email
     * @param int $excludeId
     * @return bool
     */
    public function emailExists($email, $excludeId = null) {
        $query = "SELECT id FROM {$this->table} WHERE email = :email";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        
        if ($excludeId) {
            $stmt->bindParam(':exclude_id', $excludeId);
        }
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Get user statistics
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_users,
                    SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as total_admins,
                    SUM(CASE WHEN role = 'supervisor' THEN 1 ELSE 0 END) as total_supervisors,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users
                  FROM {$this->table}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
