<?php
/**
 * Admin Dashboard
 * General Wingate Polytechnic College - Employee Management System
 */

if (!defined('ROOT_PATH')) {
    require_once '../config/config.php';
}
require_once CONFIG_PATH . '/database.php';
require_once MODEL_PATH . '/User.php';
require_once MODEL_PATH . '/Employee.php';
require_once MODEL_PATH . '/EfficiencyPoint.php';
require_once MODEL_PATH . '/Semester.php';

// Require admin access
requireAdmin();

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize models
$employeeModel = new Employee($db);
$userModel = new User($db);
$efficiencyModel = new EfficiencyPoint($db);
$semesterModel = new Semester($db);

// Get statistics
$employeeStats = $employeeModel->getStatistics();
$userStats = $userModel->getStatistics();
$efficiencyStats = $efficiencyModel->getStatistics();
$activeSemester = $semesterModel->getActiveSemester();

$page_title = 'Admin Dashboard';
include VIEW_PATH . '/layouts/header.php';
?>

<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <p class="text-muted">Welcome back, <?php echo e(getCurrentUserName()); ?>!</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card primary">
                <div class="card-body stat-card">
                    <div class="stat-card-content">
                        <h3><?php echo $employeeStats['total_employees']; ?></h3>
                        <p>Total Employees</p>
                        <small class="text-success">
                            <?php echo $employeeStats['active_employees']; ?> Active
                        </small>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card success">
                <div class="card-body stat-card">
                    <div class="stat-card-content">
                        <h3><?php echo $userStats['total_supervisors']; ?></h3>
                        <p>Supervisors</p>
                        <small class="text-success">
                            <?php echo $userStats['active_users']; ?> Active Users
                        </small>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card warning">
                <div class="card-body stat-card">
                    <div class="stat-card-content">
                        <h3><?php echo $efficiencyStats['active_points']; ?></h3>
                        <p>Efficiency Points</p>
                        <small class="text-muted">
                            Total Weight: <?php echo formatNumber($efficiencyStats['total_weight'], 1); ?>
                        </small>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-4">
            <div class="card dashboard-card info">
                <div class="card-body stat-card">
                    <div class="stat-card-content">
                        <h3><?php echo $employeeStats['total_departments']; ?></h3>
                        <p>Departments</p>
                        <small class="text-muted">
                            Across college
                        </small>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Current Semester -->
    <?php if ($activeSemester): ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-calendar-check text-success"></i> Current Semester
                    </h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><?php echo e($activeSemester['semester_name']); ?></h4>
                            <p class="mb-0 text-muted">
                                Academic Year: <?php echo e($activeSemester['academic_year']); ?>
                            </p>
                            <small class="text-muted">
                                <?php echo displayDate($activeSemester['start_date']); ?> - 
                                <?php echo displayDate($activeSemester['end_date']); ?>
                            </small>
                        </div>
                        <a href="<?php echo SITE_URL; ?>/admin/semesters.php" class="btn btn-outline-primary">
                            <i class="fas fa-cog"></i> Manage Semesters
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                No active semester. Please <a href="<?php echo SITE_URL; ?>/admin/semesters.php">create and activate a semester</a>.
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/admin/employee_form.php" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus"></i> Add Employee
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/admin/supervisor_form.php" class="btn btn-success w-100">
                                <i class="fas fa-user-tie"></i> Add Supervisor
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/admin/efficiency_point_form.php" class="btn btn-warning w-100">
                                <i class="fas fa-plus-circle"></i> Add Efficiency Point
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="<?php echo SITE_URL; ?>/admin/semester_form.php" class="btn btn-info w-100">
                                <i class="fas fa-calendar-plus"></i> Add Semester
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Employees -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users"></i> Recent Employees</h5>
                        <a href="<?php echo SITE_URL; ?>/admin/employees.php" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    $employees = $employeeModel->getAll(true);
                    $recentEmployees = array_slice($employees, 0, 5);
                    
                    if (count($recentEmployees) > 0):
                    ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Employee ID</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Position</th>
                                    <th>Supervisor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentEmployees as $employee): ?>
                                <tr>
                                    <td><strong><?php echo e($employee['employee_id']); ?></strong></td>
                                    <td><?php echo e($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                                    <td><?php echo e($employee['department']); ?></td>
                                    <td><?php echo e($employee['position']); ?></td>
                                    <td><?php echo e($employee['supervisor_name'] ?? 'Not Assigned'); ?></td>
                                    <td>
                                        <?php if ($employee['is_active']): ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No employees found. Add your first employee to get started.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
