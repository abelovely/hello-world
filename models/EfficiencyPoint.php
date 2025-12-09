<?php
/**
 * EfficiencyPoint Model
 * Handles efficiency points (evaluation criteria) database operations
 */

class EfficiencyPoint {
    private $conn;
    private $table = 'efficiency_points';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all efficiency points
     * @param bool $activeOnly
     * @return array
     */
    public function getAll($activeOnly = false) {
        $query = "SELECT * FROM {$this->table}";
        
        if ($activeOnly) {
            $query .= " WHERE is_active = 1";
        }
        
        $query .= " ORDER BY point_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get efficiency point by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create new efficiency point
     * @param array $data
     * @return int|false Efficiency point ID or false
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (point_name, description, weight, max_score, is_active) 
                  VALUES (:point_name, :description, :weight, :max_score, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        $is_active = $data['is_active'] ?? 1;
        $max_score = $data['max_score'] ?? 100;
        
        $stmt->bindParam(':point_name', $data['point_name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':weight', $data['weight']);
        $stmt->bindParam(':max_score', $max_score);
        $stmt->bindParam(':is_active', $is_active);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update efficiency point
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET point_name = :point_name,
                      description = :description,
                      weight = :weight,
                      max_score = :max_score,
                      is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':point_name', $data['point_name']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':weight', $data['weight']);
        $stmt->bindParam(':max_score', $data['max_score']);
        $stmt->bindParam(':is_active', $data['is_active']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete efficiency point
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
     * Get total weight of all active efficiency points
     * @return float
     */
    public function getTotalWeight() {
        $query = "SELECT SUM(weight) as total_weight FROM {$this->table} WHERE is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total_weight'] ?? 0;
    }
    
    /**
     * Get efficiency point statistics
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_points,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_points,
                    SUM(weight) as total_weight,
                    AVG(weight) as average_weight,
                    AVG(max_score) as average_max_score
                  FROM {$this->table}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
}
?>
