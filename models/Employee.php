<?php
/**
 * Employee Model
 * Handles all employee-related database operations
 */

class Employee {
    private $conn;
    private $table = 'employees';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get all employees
     * @param bool $activeOnly
     * @return array
     */
    public function getAll($activeOnly = false) {
        $query = "SELECT e.*, u.full_name as supervisor_name 
                  FROM {$this->table} e
                  LEFT JOIN users u ON e.supervisor_id = u.id";
        
        if ($activeOnly) {
            $query .= " WHERE e.is_active = 1";
        }
        
        $query .= " ORDER BY e.last_name, e.first_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get employee by ID
     * @param int $id
     * @return array|false
     */
    public function getById($id) {
        $query = "SELECT e.*, u.full_name as supervisor_name 
                  FROM {$this->table} e
                  LEFT JOIN users u ON e.supervisor_id = u.id
                  WHERE e.id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get employees by supervisor
     * @param int $supervisorId
     * @return array
     */
    public function getBySupervisor($supervisorId) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE supervisor_id = :supervisor_id AND is_active = 1
                  ORDER BY last_name, first_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supervisor_id', $supervisorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Create new employee
     * @param array $data
     * @return int|false Employee ID or false
     */
    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (employee_id, first_name, last_name, email, phone, department, 
                   position, hire_date, supervisor_id, is_active) 
                  VALUES (:employee_id, :first_name, :last_name, :email, :phone, 
                          :department, :position, :hire_date, :supervisor_id, :is_active)";
        
        $stmt = $this->conn->prepare($query);
        
        $is_active = $data['is_active'] ?? 1;
        $supervisor_id = !empty($data['supervisor_id']) ? $data['supervisor_id'] : null;
        
        $stmt->bindParam(':employee_id', $data['employee_id']);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':position', $data['position']);
        $stmt->bindParam(':hire_date', $data['hire_date']);
        $stmt->bindParam(':supervisor_id', $supervisor_id);
        $stmt->bindParam(':is_active', $is_active);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update employee
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data) {
        $query = "UPDATE {$this->table} 
                  SET first_name = :first_name,
                      last_name = :last_name,
                      email = :email,
                      phone = :phone,
                      department = :department,
                      position = :position,
                      hire_date = :hire_date,
                      supervisor_id = :supervisor_id,
                      is_active = :is_active
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $supervisor_id = !empty($data['supervisor_id']) ? $data['supervisor_id'] : null;
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':first_name', $data['first_name']);
        $stmt->bindParam(':last_name', $data['last_name']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':phone', $data['phone']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':position', $data['position']);
        $stmt->bindParam(':hire_date', $data['hire_date']);
        $stmt->bindParam(':supervisor_id', $supervisor_id);
        $stmt->bindParam(':is_active', $data['is_active']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete employee
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
     * Check if employee ID exists
     * @param string $employeeId
     * @param int $excludeId
     * @return bool
     */
    public function employeeIdExists($employeeId, $excludeId = null) {
        $query = "SELECT id FROM {$this->table} WHERE employee_id = :employee_id";
        
        if ($excludeId) {
            $query .= " AND id != :exclude_id";
        }
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':employee_id', $employeeId);
        
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
     * Get employee statistics
     * @return array
     */
    public function getStatistics() {
        $query = "SELECT 
                    COUNT(*) as total_employees,
                    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_employees,
                    COUNT(DISTINCT department) as total_departments,
                    COUNT(DISTINCT supervisor_id) as assigned_supervisors
                  FROM {$this->table}";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get employees by department
     * @param string $department
     * @return array
     */
    public function getByDepartment($department) {
        $query = "SELECT e.*, u.full_name as supervisor_name 
                  FROM {$this->table} e
                  LEFT JOIN users u ON e.supervisor_id = u.id
                  WHERE e.department = :department AND e.is_active = 1
                  ORDER BY e.last_name, e.first_name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':department', $department);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all departments
     * @return array
     */
    public function getAllDepartments() {
        $query = "SELECT DISTINCT department 
                  FROM {$this->table} 
                  WHERE department IS NOT NULL AND department != ''
                  ORDER BY department ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
