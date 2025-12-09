<?php
/**
 * Semester Model
 * Handles semester/academic period database operations
 */

class Semester {
    private $conn;
    private $table = 'semesters';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all semesters
     * @param bool $activeOnly
     * @return array
     */
    public function getAll($activeOnly = false) {
        $query = "SELECT * FROM {$this->table}";
        
        if ($activeOnly) {
            $query .= " WHERE is_active = 1";
        }
        
        $query .= " ORDER BY start_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get semester by ID
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
     * Get active semester
     * @return array|false
     */
    public function getActiveSemester() {
        $query = "SELECT * FROM {$this->table} WHERE is_active = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Create new semester
     * @param array $data
     * @return int|false Semester ID or false
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (semester_name, academic_year, start_date, end_date, is_active) 
                  VALUES (:semester_name, :academic_year, :start_date, :end_date, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        $is_active = $data['is_active'] ?? 0;
        
        $stmt->bindParam(':semester_name', $data['semester_name']);
        $stmt->bindParam(':academic_year', $data['academic_year']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':is_active', $is_active);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update semester
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET semester_name = :semester_name,
                      academic_year = :academic_year,
                      start_date = :start_date,
                      end_date = :end_date,
                      is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':semester_name', $data['semester_name']);
        $stmt->bindParam(':academic_year', $data['academic_year']);
        $stmt->bindParam(':start_date', $data['start_date']);
        $stmt->bindParam(':end_date', $data['end_date']);
        $stmt->bindParam(':is_active', $data['is_active']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete semester
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
     * Set active semester
     * @param int $id
     * @return bool
     */
    public function setActive($id) {
        try {
            // Start transaction
            $this->conn->beginTransaction();
            
            // Deactivate all semesters
            $query1 = "UPDATE {$this->table} SET is_active = 0";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->execute();
            
            // Activate the selected semester
            $query2 = "UPDATE {$this->table} SET is_active = 1 WHERE id = :id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':id', $id);
            $stmt2->execute();
            
            // Commit transaction
            $this->conn->commit();
            return true;
            
        } catch (Exception $e) {
            // Rollback on error
            $this->conn->rollBack();
            return false;
        }
    }
    
    /**
     * Get semesters by academic year
     * @param string $academicYear
     * @return array
     */
    public function getByAcademicYear($academicYear) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE academic_year = :academic_year
                  ORDER BY start_date ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':academic_year', $academicYear);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all academic years
     * @return array
     */
    public function getAllAcademicYears() {
        $query = "SELECT DISTINCT academic_year 
                  FROM {$this->table} 
                  ORDER BY academic_year DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
