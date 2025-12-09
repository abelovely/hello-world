<?php
/**
 * Supervisor Dashboard
 * General Wingate Polytechnic College - Employee Management System
 */

if (!defined('ROOT_PATH')) {
    require_once '../config/config.php';
}
require_once CONFIG_PATH . '/database.php';
require_once MODEL_PATH . '/Employee.php';
require_once MODEL_PATH . '/Semester.php';
require_once MODEL_PATH . '/SupervisionComment.php';
require_once MODEL_PATH . '/EfficiencyScore.php';

// Require supervisor access
requireSupervisor();

// Get database connection
$database = new Database();
$db = $database->getConnection();

// Initialize models
$employeeModel = new Employee($db);
$semesterModel = new Semester($db);
$commentModel = new SupervisionComment($db);
$scoreModel = new EfficiencyScore($db);

// Get supervisor's assigned employees
$myEmployees = $employeeModel->getBySupervisor(getCurrentUserId());
$activeSemester = $semesterModel->getActiveSemester();
$recentComments = $commentModel->getBySupervisor(getCurrentUserId());
$recentComments = array_slice($recentComments, 0, 5);

$page_title = 'Supervisor Dashboard';
include VIEW_PATH . '/layouts/header.php';
?>

<div class="container-fluid">
    <div class="page-header">
        <h1><i class="fas fa-tachometer-alt"></i> Supervisor Dashboard</h1>
        <p class="text-muted">Welcome back, <?php echo e(getCurrentUserName()); ?>!</p>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card primary">
                <div class="card-body stat-card">
                    <div class="stat-card-content">
                        <h3><?php echo count($myEmployees); ?></h3>
                        <p>Assigned Employees</p>
                        <small class="text-muted">Under your supervision</small>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card success">
                <div class="card-body stat-card">
                    <div class="stat-card-content">
                        <h3><?php echo count($recentComments); ?></h3>
                        <p>Recent Comments</p>
                        <small class="text-muted">Supervision records</small>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card dashboard-card info">
                <div class="card-body stat-card">
                    <div class="stat-card-content">
                        <?php if ($activeSemester): ?>
                            <h3><?php echo e($activeSemester['semester_name']); ?></h3>
                            <p>Current Semester</p>
                            <small class="text-muted"><?php echo e($activeSemester['academic_year']); ?></small>
                        <?php else: ?>
                            <h3>N/A</h3>
                            <p>No Active Semester</p>
                            <small class="text-muted">Contact admin</small>
                        <?php endif; ?>
                    </div>
                    <div class="card-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <a href="<?php echo SITE_URL; ?>/supervisor/my_employees.php" class="btn btn-primary w-100">
                                <i class="fas fa-users"></i> View My Employees
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="<?php echo SITE_URL; ?>/supervisor/supervision.php" class="btn btn-success w-100">
                                <i class="fas fa-comments"></i> Add Supervision Comment
                            </a>
                        </div>
                        <div class="col-md-4 mb-2">
                            <a href="<?php echo SITE_URL; ?>/supervisor/scoring.php" class="btn btn-warning w-100">
                                <i class="fas fa-star"></i> Submit Efficiency Scores
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- My Employees -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users"></i> My Assigned Employees</h5>
                        <a href="<?php echo SITE_URL; ?>/supervisor/my_employees.php" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($myEmployees) > 0): ?>
                    <div class="row">
                        <?php foreach (array_slice($myEmployees, 0, 6) as $employee): ?>
                        <div class="col-md-4 mb-3">
                            <div class="card employee-card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="employee-avatar me-3">
                                            <?php echo strtoupper(substr($employee['first_name'], 0, 1)); ?>
                                        </div>
                                        <div>
                                            <h6 class="mb-0"><?php echo e($employee['first_name'] . ' ' . $employee['last_name']); ?></h6>
                                            <small class="text-muted"><?php echo e($employee['position']); ?></small><br>
                                            <small class="text-muted"><?php echo e($employee['department']); ?></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>No employees assigned to you yet.</p>
                        <small class="text-muted">Contact your administrator to assign employees.</small>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Supervision Comments -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-comments"></i> Recent Supervision Comments</h5>
                        <a href="<?php echo SITE_URL; ?>/supervisor/supervision.php" class="btn btn-sm btn-outline-primary">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($recentComments) > 0): ?>
                        <?php foreach ($recentComments as $comment): ?>
                        <div class="comment-item">
                            <div class="d-flex justify-content-between">
                                <h6 class="mb-1"><?php echo e($comment['employee_name']); ?></h6>
                                <span class="badge bg-info"><?php echo displayDate($comment['comment_date']); ?></span>
                            </div>
                            <p class="mb-1"><?php echo e(truncate($comment['comment'], 150)); ?></p>
                            <small class="comment-meta">
                                <i class="fas fa-calendar"></i> <?php echo e($comment['semester_name']); ?> 
                                (<?php echo e($comment['academic_year']); ?>)
                            </small>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-comments"></i>
                        <p>No supervision comments yet.</p>
                        <a href="<?php echo SITE_URL; ?>/supervisor/supervision.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add First Comment
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
