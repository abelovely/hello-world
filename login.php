<?php
/**
 * Login Page
 * General Wingate Polytechnic College - Employee Management System
 */

require_once 'config/config.php';
require_once 'config/database.php';
require_once 'models/User.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('index.php');
}

$error = '';

// Handle login form submission
if (isPostRequest()) {
    $username = sanitize(post('username'));
    $password = post('password');
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        $database = new Database();
        $db = $database->getConnection();
        
        if ($db) {
            $userModel = new User($db);
            $user = $userModel->login($username, $password);
            
            if ($user) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                
                setFlashMessage('success', 'Welcome back, ' . $user['full_name'] . '!');
                redirect('index.php');
            } else {
                $error = 'Invalid username or password.';
            }
        } else {
            $error = 'Database connection failed. Please try again later.';
        }
    }
}

$page_title = 'Login';
include VIEW_PATH . '/layouts/header.php';
?>

<div class="login-container">
    <div class="card login-card">
        <div class="login-header">
            <i class="fas fa-university"></i>
            <h3 class="mb-0">GWPC Employee Management</h3>
            <p class="mb-0">General Wingate Polytechnic College</p>
        </div>
        <div class="login-body">
            <h4 class="text-center mb-4">Sign In</h4>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           placeholder="Enter your username"
                           value="<?php echo isset($username) ? e($username) : ''; ?>"
                           required 
                           autofocus>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           placeholder="Enter your password"
                           required>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember">
                    <label class="form-check-label" for="remember">
                        Remember me
                    </label>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </div>
            </form>
            
            <hr class="my-4">
            
            <div class="text-center">
                <p class="text-muted mb-2">Default Credentials:</p>
                <small class="text-muted">
                    Admin: <strong>admin</strong> / <strong>admin123</strong><br>
                    Supervisor: <strong>supervisor1</strong> / <strong>supervisor123</strong>
                </small>
            </div>
        </div>
    </div>
</div>

<?php include VIEW_PATH . '/layouts/footer.php'; ?>
