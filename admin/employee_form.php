<?php
/**
 * Admin - Employee Form (Add/Edit)
 * General Wingate Polytechnic College - Employee Management System
 */

require_once '../config/config.php';
require_once CONFIG_PATH . '/database.php';
require_once MODEL_PATH . '/Employee.php';
require_once MODEL_PATH . '/User.php';

requireAdmin();

$database = new Database();
$db = $database->getConnection();
$employeeModel = new Employee($db);
$userModel = new User($db);

$errors = [];
$employee = null;
$isEdit = false;

// Check if editing
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $employee = $employeeModel->getById($id);
    if (!$employee) {
        setFlashMessage('error', 'Employee not found.');
        redirect('employees.php');
    }
    $isEdit = true;
}

// Handle form submission
if (isPostRequest()) {
    // Validate CSRF token
    if (!verifyCSRFToken(post('csrf_token'))) {
        $errors[] = 'Invalid form submission.';
    }
    
    $data = [
        'employee_id' => sanitize(post('employee_id')),
        'first_name' => sanitize(post('first_name')),
        'last_name' => sanitize(post('last_name')),
        'email' => sanitize(post('email')),
        'phone' => sanitize(post('phone')),
        'department' => sanitize(post('department')),
        'position' => sanitize(post('position')),
        'hire_date' => post('hire_date'),
        'supervisor_id' => post('supervisor_id') ?: null,
        'is_active' => post('is_active', 1)
    ];
    
    // Validation
    if (empty($data['employee_id'])) {
        $errors[] = 'Employee ID is required.';
    }
    if (empty($data['first_name'])) {
        $errors[] = 'First name is required.';
    }
    if (empty($data['last_name'])) {
        $errors[] = 'Last name is required.';
    }
    if (empty($data['email']) || !validateEmail($data['email'])) {
        $errors[] = 'Valid email is required.';
    }
    
    // Check for duplicates
    $excludeId = $isEdit ? $employee['id'] : null;
    if ($employeeModel->employeeIdExists($data['employee_id'], $excludeId)) {
        $errors[] = 'Employee ID already exists.';
    }
    if ($employeeModel->emailExists($data['email'], $excludeId)) {
        $errors[] = 'Email already exists.';
    }
    
    if (empty($errors)) {
        if ($isEdit) {
            if ($employeeModel->update($employee['id'], $data)) {
                setFlashMessage('success', 'Employee updated successfully.');
                redirect('employees.php');
            } else {
                $errors[] = 'Failed to update employee.';
            }
        } else {
            if ($employeeModel->create($data)) {
                setFlashMessage('success', 'Employee created successfully.');
                redirect('employees.php');
            } else {
                $errors[] = 'Failed to create employee.';
            }
        }
    }
}

// Get all supervisors for dropdown
$supervisors = $userModel->getAllSupervisors();

$page_title = $isEdit ? 'Edit Employee' : 'Add New Employee';
include VIEW_PATH . '/layouts/header.php';
?>

<div class="container-fluid">
    <div class="page-header">
        <div>
            <h1><i class="fas fa-user-edit"></i> <?php echo $page_title; ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/admin/employees.php">Employees</a></li>
                    <li class="breadcrumb-item active"><?php echo $isEdit ? 'Edit' : 'Add New'; ?></li>
                </ol>
            </nav>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h6>
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <?php echo csrfField(); ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="employee_id" class="form-label required">Employee ID</label>
                                <input type="text" class="form-control" id="employee_id" name="employee_id" 
                                       value="<?php echo e($employee['employee_id'] ?? post('employee_id', '')); ?>" 
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="hire_date" class="form-label">Hire Date</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                       value="<?php echo e($employee['hire_date'] ?? post('hire_date', '')); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label required">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo e($employee['first_name'] ?? post('first_name', '')); ?>" 
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label required">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo e($employee['last_name'] ?? post('last_name', '')); ?>" 
                                       required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label required">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo e($employee['email'] ?? post('email', '')); ?>" 
                                       required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="<?php echo e($employee['phone'] ?? post('phone', '')); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="department" class="form-label">Department</label>
                                <input type="text" class="form-control" id="department" name="department" 
                                       value="<?php echo e($employee['department'] ?? post('department', '')); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="<?php echo e($employee['position'] ?? post('position', '')); ?>">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supervisor_id" class="form-label">Assign Supervisor</label>
                                <select class="form-select" id="supervisor_id" name="supervisor_id">
                                    <option value="">-- Select Supervisor --</option>
                                    <?php foreach ($supervisors as $supervisor): ?>
                                        <option value="<?php echo $supervisor['id']; ?>"
                                            <?php echo (isset($employee['supervisor_id']) && $employee['supervisor_id'] == $supervisor['id']) || post('supervisor_id') == $supervisor['id'] ? 'selected' : ''; ?>>
                                            <?php echo e($supervisor['full_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <select class="form-select" id="is_active" name="is_active">
                                    <option value="1" <?php echo (!isset($employee['is_active']) || $employee['is_active'] == 1) ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo (isset($employee['is_active']) && $employee['is_active'] == 0) ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo SITE_URL; ?>/admin/employees.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?php echo $isEdit ? 'Update' : 'Create'; ?> Employee
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
