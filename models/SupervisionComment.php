<?php
/**
 * SupervisionComment Model
 * Handles supervision comments database operations
 */

class SupervisionComment {
    private $conn;
    private $table = 'supervision_comments';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all supervision comments
     * @return array
     */
    public function getAll() {
        $query = "SELECT sc.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         u.full_name as supervisor_name,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} sc
                  INNER JOIN employees e ON sc.employee_id = e.id
                  INNER JOIN users u ON sc.supervisor_id = u.id
                  INNER JOIN semesters s ON sc.semester_id = s.id
                  ORDER BY sc.comment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get supervision comment by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT sc.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         u.full_name as supervisor_name,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} sc
                  INNER JOIN employees e ON sc.employee_id = e.id
                  INNER JOIN users u ON sc.supervisor_id = u.id
                  INNER JOIN semesters s ON sc.semester_id = s.id
                  WHERE sc.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get comments by employee
     * @param int $employeeId
     * @return array
     */
    public function getByEmployee($employeeId) {
        $query = "SELECT sc.*, 
                         u.full_name as supervisor_name,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} sc
                  INNER JOIN users u ON sc.supervisor_id = u.id
                  INNER JOIN semesters s ON sc.semester_id = s.id
                  WHERE sc.employee_id = :employee_id
                  ORDER BY sc.comment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get comments by supervisor
     * @param int $supervisorId
     * @return array
     */
    public function getBySupervisor($supervisorId) {
        $query = "SELECT sc.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} sc
                  INNER JOIN employees e ON sc.employee_id = e.id
                  INNER JOIN semesters s ON sc.semester_id = s.id
                  WHERE sc.supervisor_id = :supervisor_id
                  ORDER BY sc.comment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supervisor_id', $supervisorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get comments by employee and semester
     * @param int $employeeId
     * @param int $semesterId
     * @return array
     */
    public function getByEmployeeAndSemester($employeeId, $semesterId) {
        $query = "SELECT sc.*, 
                         u.full_name as supervisor_name
                  FROM {$this->table} sc
                  INNER JOIN users u ON sc.supervisor_id = u.id
                  WHERE sc.employee_id = :employee_id AND sc.semester_id = :semester_id
                  ORDER BY sc.comment_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':semester_id', $semesterId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Create new supervision comment
     * @param array $data
     * @return int|false Comment ID or false
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (employee_id, supervisor_id, semester_id, comment, comment_date) 
                  VALUES (:employee_id, :supervisor_id, :semester_id, :comment, :comment_date)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':employee_id', $data['employee_id']);
        $stmt->bindParam(':supervisor_id', $data['supervisor_id']);
        $stmt->bindParam(':semester_id', $data['semester_id']);
        $stmt->bindParam(':comment', $data['comment']);
        $stmt->bindParam(':comment_date', $data['comment_date']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update supervision comment
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET employee_id = :employee_id,
                      semester_id = :semester_id,
                      comment = :comment,
                      comment_date = :comment_date
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':employee_id', $data['employee_id']);
        $stmt->bindParam(':semester_id', $data['semester_id']);
        $stmt->bindParam(':comment', $data['comment']);
        $stmt->bindParam(':comment_date', $data['comment_date']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete supervision comment
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
     * Get comment count by employee
     * @param int $employeeId
     * @return int
     */
    public function getCountByEmployee($employeeId) {
        $query = "SELECT COUNT(*) as count FROM {$this->table} WHERE employee_id = :employee_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>
