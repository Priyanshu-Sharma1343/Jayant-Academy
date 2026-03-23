<?php
/**
 * Authentication Check
 * File: admin/check-auth.php
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: /login.php');
    exit;
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];
$username = $_SESSION['username'];

// Define role permissions
$permissions = [
    'admin' => ['view_all', 'manage_accounts', 'view_payments', 'view_students', 'change_codes'],
    'staff' => ['view_students', 'view_payments']
];

// Check if user has permission
function hasPermission($permission) {
    global $role, $permissions;
    return in_array($permission, $permissions[$role] ?? []);
}

// Helper function to check permission and redirect
function requirePermission($permission) {
    if (!hasPermission($permission)) {
        http_response_code(403);
        die('❌ Access Denied! You do not have permission to access this page.');
    }
}
?>
