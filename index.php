<?php
/**
 * Dashboard Index Page
 * General Wingate Polytechnic College - Employee Management System
 */

require_once 'config/config.php';
require_once 'config/database.php';

// Require login
requireLogin();

// Redirect to appropriate dashboard based on role
if (isAdmin()) {
    require_once 'admin/dashboard.php';
} elseif (isSupervisor()) {
    require_once 'supervisor/dashboard.php';
} else {
    // Invalid role, logout
    redirect('logout.php');
}
?>
