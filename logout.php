<?php
/**
 * Logout Page
 * General Wingate Polytechnic College - Employee Management System
 */

require_once 'config/config.php';

// Destroy session and redirect to login
session_unset();
session_destroy();

setFlashMessage('success', 'You have been logged out successfully.');
redirect('login.php');
?>
