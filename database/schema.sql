-- General Wingate Polytechnic College Employee Management System
-- Database Schema

-- Drop existing database if exists and create new one
DROP DATABASE IF EXISTS gwpc_employee_management;
CREATE DATABASE gwpc_employee_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gwpc_employee_management;

-- Users table (Admin and Supervisors)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'supervisor') NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_role (role),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Employees table
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id VARCHAR(20) UNIQUE NOT NULL,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    department VARCHAR(100),
    position VARCHAR(100),
    hire_date DATE,
    supervisor_id INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_employee_id (employee_id),
    INDEX idx_supervisor_id (supervisor_id),
    INDEX idx_department (department)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Efficiency Points table (criteria for evaluation)
CREATE TABLE efficiency_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    point_name VARCHAR(200) NOT NULL,
    description TEXT,
    weight DECIMAL(5, 2) NOT NULL DEFAULT 1.00,
    max_score INT NOT NULL DEFAULT 100,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Semesters table
CREATE TABLE semesters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    semester_name VARCHAR(50) NOT NULL,
    academic_year VARCHAR(20) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_academic_year (academic_year),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Supervision comments table
CREATE TABLE supervision_comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    supervisor_id INT NOT NULL,
    semester_id INT NOT NULL,
    comment TEXT NOT NULL,
    comment_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    INDEX idx_employee_id (employee_id),
    INDEX idx_supervisor_id (supervisor_id),
    INDEX idx_semester_id (semester_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Efficiency scores table
CREATE TABLE efficiency_scores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    efficiency_point_id INT NOT NULL,
    semester_id INT NOT NULL,
    supervisor_id INT NOT NULL,
    score DECIMAL(5, 2) NOT NULL,
    weighted_score DECIMAL(10, 2) AS (score * (SELECT weight FROM efficiency_points WHERE id = efficiency_point_id)) STORED,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES employees(id) ON DELETE CASCADE,
    FOREIGN KEY (efficiency_point_id) REFERENCES efficiency_points(id) ON DELETE CASCADE,
    FOREIGN KEY (semester_id) REFERENCES semesters(id) ON DELETE CASCADE,
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_score (employee_id, efficiency_point_id, semester_id),
    INDEX idx_employee_id (employee_id),
    INDEX idx_efficiency_point_id (efficiency_point_id),
    INDEX idx_semester_id (semester_id),
    INDEX idx_supervisor_id (supervisor_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin@gwpc.edu', 'admin');

-- Insert sample supervisors (password: supervisor123)
INSERT INTO users (username, password, full_name, email, role) VALUES
('supervisor1', '$2y$10$R5K9Z5f5Z5Z5Z5Z5Z5Z5ZeVmP5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Zu', 'Dr. John Smith', 'j.smith@gwpc.edu', 'supervisor'),
('supervisor2', '$2y$10$R5K9Z5f5Z5Z5Z5Z5Z5Z5ZeVmP5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Zu', 'Prof. Mary Johnson', 'm.johnson@gwpc.edu', 'supervisor'),
('supervisor3', '$2y$10$R5K9Z5f5Z5Z5Z5Z5Z5Z5ZeVmP5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Z5Zu', 'Dr. Robert Williams', 'r.williams@gwpc.edu', 'supervisor');

-- Insert sample employees
INSERT INTO employees (employee_id, first_name, last_name, email, phone, department, position, hire_date, supervisor_id) VALUES
('EMP001', 'Alice', 'Anderson', 'a.anderson@gwpc.edu', '555-0101', 'Computer Science', 'Lecturer', '2020-01-15', 2),
('EMP002', 'Bob', 'Brown', 'b.brown@gwpc.edu', '555-0102', 'Engineering', 'Senior Lecturer', '2019-08-20', 3),
('EMP003', 'Carol', 'Davis', 'c.davis@gwpc.edu', '555-0103', 'Business Studies', 'Lecturer', '2021-03-10', 4),
('EMP004', 'David', 'Evans', 'd.evans@gwpc.edu', '555-0104', 'Computer Science', 'Assistant Lecturer', '2022-01-05', 2),
('EMP005', 'Emma', 'Foster', 'e.foster@gwpc.edu', '555-0105', 'Engineering', 'Lecturer', '2020-09-15', 3);

-- Insert sample efficiency points
INSERT INTO efficiency_points (point_name, description, weight, max_score) VALUES
('Teaching Quality', 'Quality of teaching delivery and student engagement', 3.00, 100),
('Punctuality', 'Timeliness in attending classes and meetings', 2.00, 100),
('Student Assessment', 'Quality and timeliness of student assessments and feedback', 2.50, 100),
('Professional Development', 'Participation in training and professional growth activities', 1.50, 100),
('Research & Publications', 'Research activities and publications', 2.00, 100),
('Administrative Tasks', 'Completion of administrative responsibilities', 1.50, 100),
('Student Support', 'Availability and quality of student support and mentoring', 2.00, 100),
('Collaboration', 'Teamwork and collaboration with colleagues', 1.50, 100);

-- Insert sample semesters
INSERT INTO semesters (semester_name, academic_year, start_date, end_date, is_active) VALUES
('Fall 2023', '2023/2024', '2023-09-01', '2023-12-31', 0),
('Spring 2024', '2023/2024', '2024-01-15', '2024-05-15', 0),
('Fall 2024', '2024/2025', '2024-09-01', '2024-12-31', 1);

-- Insert sample supervision comments
INSERT INTO supervision_comments (employee_id, supervisor_id, semester_id, comment, comment_date) VALUES
(1, 2, 1, 'Excellent teaching performance. Students show high engagement in classes.', '2023-10-15'),
(1, 2, 1, 'Demonstrated strong commitment to curriculum development.', '2023-11-20'),
(2, 3, 1, 'Good collaboration with department members. Needs improvement in punctuality.', '2023-10-20'),
(3, 4, 1, 'Outstanding student feedback. Highly recommended for promotion consideration.', '2023-11-05');

-- Insert sample efficiency scores
INSERT INTO efficiency_scores (employee_id, efficiency_point_id, semester_id, supervisor_id, score, remarks) VALUES
-- Scores for Employee 1 (Alice Anderson) - Fall 2023
(1, 1, 1, 2, 92.00, 'Excellent teaching delivery'),
(1, 2, 1, 2, 88.00, 'Consistently punctual'),
(1, 3, 1, 2, 90.00, 'Timely and thorough assessments'),
(1, 4, 1, 2, 85.00, 'Attended two workshops'),
(1, 5, 1, 2, 78.00, 'Published one conference paper'),
(1, 6, 1, 2, 90.00, 'Completed all tasks on time'),
(1, 7, 1, 2, 95.00, 'Excellent student support'),
(1, 8, 1, 2, 88.00, 'Good team player'),

-- Scores for Employee 2 (Bob Brown) - Fall 2023
(2, 1, 1, 3, 85.00, 'Good teaching quality'),
(2, 2, 1, 3, 75.00, 'Some punctuality issues'),
(2, 3, 1, 3, 88.00, 'Good assessment practices'),
(2, 4, 1, 3, 82.00, 'Participated in one training'),
(2, 5, 1, 3, 90.00, 'Published two journal articles'),
(2, 6, 1, 3, 85.00, 'Satisfactory admin work'),
(2, 7, 1, 3, 80.00, 'Available for students'),
(2, 8, 1, 3, 92.00, 'Excellent collaboration');

-- Create view for employee efficiency summary
CREATE OR REPLACE VIEW v_employee_efficiency_summary AS
SELECT 
    e.id AS employee_id,
    e.employee_id,
    CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
    e.department,
    e.position,
    s.id AS semester_id,
    s.semester_name,
    s.academic_year,
    u.full_name AS supervisor_name,
    SUM(es.score * ep.weight) / SUM(ep.weight) AS average_score,
    SUM(es.weighted_score) AS total_weighted_score,
    COUNT(DISTINCT es.efficiency_point_id) AS points_evaluated,
    (SELECT COUNT(*) FROM efficiency_points WHERE is_active = 1) AS total_points
FROM employees e
LEFT JOIN efficiency_scores es ON e.id = es.employee_id
LEFT JOIN efficiency_points ep ON es.efficiency_point_id = ep.id
LEFT JOIN semesters s ON es.semester_id = s.id
LEFT JOIN users u ON e.supervisor_id = u.id
GROUP BY e.id, s.id, u.id;

-- Create view for supervisor assigned employees
CREATE OR REPLACE VIEW v_supervisor_employees AS
SELECT 
    u.id AS supervisor_id,
    u.full_name AS supervisor_name,
    e.id AS employee_id,
    e.employee_id,
    CONCAT(e.first_name, ' ', e.last_name) AS employee_name,
    e.email,
    e.department,
    e.position,
    e.is_active
FROM users u
INNER JOIN employees e ON u.id = e.supervisor_id
WHERE u.role = 'supervisor' AND e.is_active = 1;

-- Create view for efficiency point statistics
CREATE OR REPLACE VIEW v_efficiency_point_stats AS
SELECT 
    ep.id AS point_id,
    ep.point_name,
    ep.weight,
    ep.max_score,
    COUNT(es.id) AS total_evaluations,
    AVG(es.score) AS average_score,
    MIN(es.score) AS min_score,
    MAX(es.score) AS max_score
FROM efficiency_points ep
LEFT JOIN efficiency_scores es ON ep.id = es.efficiency_point_id
WHERE ep.is_active = 1
GROUP BY ep.id;
