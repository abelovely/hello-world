<?php
/**
 * EfficiencyScore Model
 * Handles efficiency scores database operations
 */

class EfficiencyScore {
    private $conn;
    private $table = 'efficiency_scores';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all efficiency scores
     * @return array
     */
    public function getAll() {
        $query = "SELECT es.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         ep.point_name,
                         ep.weight,
                         u.full_name as supervisor_name,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} es
                  INNER JOIN employees e ON es.employee_id = e.id
                  INNER JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
                  INNER JOIN users u ON es.supervisor_id = u.id
                  INNER JOIN semesters s ON es.semester_id = s.id
                  ORDER BY es.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get efficiency score by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT es.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         ep.point_name,
                         ep.weight,
                         u.full_name as supervisor_name,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} es
                  INNER JOIN employees e ON es.employee_id = e.id
                  INNER JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
                  INNER JOIN users u ON es.supervisor_id = u.id
                  INNER JOIN semesters s ON es.semester_id = s.id
                  WHERE es.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get scores by employee
     * @param int $employeeId
     * @return array
     */
    public function getByEmployee($employeeId) {
        $query = "SELECT es.*, 
                         ep.point_name,
                         ep.weight,
                         u.full_name as supervisor_name,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} es
                  INNER JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
                  INNER JOIN users u ON es.supervisor_id = u.id
                  INNER JOIN semesters s ON es.semester_id = s.id
                  WHERE es.employee_id = :employee_id
                  ORDER BY s.start_date DESC, ep.point_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get scores by employee and semester
     * @param int $employeeId
     * @param int $semesterId
     * @return array
     */
    public function getByEmployeeAndSemester($employeeId, $semesterId) {
        $query = "SELECT es.*, 
                         ep.point_name,
                         ep.weight,
                         ep.max_score,
                         u.full_name as supervisor_name
                  FROM {$this->table} es
                  INNER JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
                  INNER JOIN users u ON es.supervisor_id = u.id
                  WHERE es.employee_id = :employee_id AND es.semester_id = :semester_id
                  ORDER BY ep.point_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':semester_id', $semesterId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get scores by supervisor
     * @param int $supervisorId
     * @return array
     */
    public function getBySupervisor($supervisorId) {
        $query = "SELECT es.*, 
                         CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                         ep.point_name,
                         ep.weight,
                         s.semester_name,
                         s.academic_year
                  FROM {$this->table} es
                  INNER JOIN employees e ON es.employee_id = e.id
                  INNER JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
                  INNER JOIN semesters s ON es.semester_id = s.id
                  WHERE es.supervisor_id = :supervisor_id
                  ORDER BY es.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supervisor_id', $supervisorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Create or update efficiency score
     * @param array $data
     * @return int|false Score ID or false
     */
    public function createOrUpdate($data) {
        // Check if score already exists
        $existing = $this->getByEmployeePointSemester(
            $data['employee_id'],
            $data['efficiency_point_id'],
            $data['semester_id']
        );
        
        if ($existing) {
            // Update existing score
            return $this->update($existing['id'], $data);
        } else {
            // Create new score
            return $this->create($data);
        }
    }
    
    /**
     * Create new efficiency score
     * @param array $data
     * @return int|false Score ID or false
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (employee_id, efficiency_point_id, semester_id, supervisor_id, score, remarks) 
                  VALUES (:employee_id, :efficiency_point_id, :semester_id, :supervisor_id, :score, :remarks)";
        
        $stmt = $this->conn->prepare($query);
        
        $remarks = $data['remarks'] ?? null;
        
        $stmt->bindParam(':employee_id', $data['employee_id']);
        $stmt->bindParam(':efficiency_point_id', $data['efficiency_point_id']);
        $stmt->bindParam(':semester_id', $data['semester_id']);
        $stmt->bindParam(':supervisor_id', $data['supervisor_id']);
        $stmt->bindParam(':score', $data['score']);
        $stmt->bindParam(':remarks', $remarks);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update efficiency score
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET score = :score,
                      remarks = :remarks
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $remarks = $data['remarks'] ?? null;
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':score', $data['score']);
        $stmt->bindParam(':remarks', $remarks);
        
        return $stmt->execute();
    }
    
    /**
     * Delete efficiency score
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
     * Get score by employee, point and semester
     * @param int $employeeId
     * @param int $pointId
     * @param int $semesterId
     * @return array|false
     */
    public function getByEmployeePointSemester($employeeId, $pointId, $semesterId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE employee_id = :employee_id 
                  AND efficiency_point_id = :point_id 
                  AND semester_id = :semester_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':point_id', $pointId);
        $stmt->bindParam(':semester_id', $semesterId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get employee total score for semester
     * @param int $employeeId
     * @param int $semesterId
     * @return array
     */
    public function getEmployeeTotalScore($employeeId, $semesterId) {
        $query = "SELECT 
                    SUM(es.score * ep.weight) / SUM(ep.weight) as average_score,
                    SUM(es.score * ep.weight) as total_weighted_score,
                    COUNT(es.id) as points_evaluated
                  FROM {$this->table} es
                  INNER JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
                  WHERE es.employee_id = :employee_id AND es.semester_id = :semester_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':semester_id', $semesterId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get employee score summary across all semesters
     * @param int $employeeId
     * @return array
     */
    public function getEmployeeScoreSummary($employeeId) {
        $query = "SELECT 
                    s.semester_name,
                    s.academic_year,
                    SUM(es.score * ep.weight) / SUM(ep.weight) as average_score,
                    SUM(es.score * ep.weight) as total_weighted_score,
                    COUNT(es.id) as points_evaluated
                  FROM {$this->table} es
                  INNER JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
                  INNER JOIN semesters s ON es.semester_id = s.id
                  WHERE es.employee_id = :employee_id
                  GROUP BY s.id
                  ORDER BY s.start_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Delete scores by semester
     * @param int $semesterId
     * @return bool
     */
    public function deleteBySemester($semesterId) {
        $query = "DELETE FROM {$this->table} WHERE semester_id = :semester_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':semester_id', $semesterId);
        return $stmt->execute();
    }
    
    /**
     * Get completion status for employee in semester
     * @param int $employeeId
     * @param int $semesterId
     * @return array
     */
    public function getCompletionStatus($employeeId, $semesterId) {
        $query = "SELECT 
                    (SELECT COUNT(*) FROM efficiency_points WHERE is_active = 1) as total_points,
                    COUNT(es.id) as completed_points
                  FROM efficiency_points ep
                  LEFT JOIN {$this->table} es ON ep.id = es.efficiency_point_id 
                    AND es.employee_id = :employee_id 
                    AND es.semester_id = :semester_id
                  WHERE ep.is_active = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        $stmt->bindParam(':semester_id', $semesterId);
        $stmt->execute();
        
        return $stmt->fetch();
    }
}
?>
