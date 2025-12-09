<?php
/**
 * Admin - Employees List
 * General Wingate Polytechnic College - Employee Management System
 */

require_once '../config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once MODEL_PATH . '/Employee.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$employeeModel = new Employee($db);

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($employeeModel->delete($id)) {
        setFlashMessage('success', 'Employee deleted successfully.');
    } else {
        setFlashMessage('error', 'Failed to delete employee.');
    }
    redirect('employees.php');
}

// Get all employees
$employees = $employeeModel->getAll();

$page_title = 'Manage Employees';
include VIEW_PATH . '/layouts/header.php';
?>

<div class="container-fluid">
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="fas fa-users"></i> Manage Employees</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Employees</li>
                    </ol>
                </nav>
            </div>
            <a href="<?php echo SITE_URL; ?>/admin/employee_form.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Employee
            </a>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if (count($employees) > 0): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover data-table">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Supervisor</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $employee): ?>
                        <tr>
                            <td><strong><?php echo e($employee['employee_id']); ?></strong></td>
                            <td><?php echo e($employee['first_name'] . ' ' . $employee['last_name']); ?></td>
                            <td><?php echo e($employee['email']); ?></td>
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
                            <td class="action-buttons">
                                <a href="employee_form.php?id=<?php echo $employee['id']; ?>" 
                                   class="btn btn-sm btn-info" 
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="?action=delete&id=<?php echo $employee['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirmDelete('Are you sure you want to delete this employee?');"
                                   title="Delete">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <p>No employees found.</p>
                <a href="employee_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add First Employee
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
