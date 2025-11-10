<?php
/**
 * Logout functionality for admin users
 * This script handles secure logout and session cleanup
 */

session_start();
require_once __DIR__ . '/../config/db_conected.php';

// Check if admin is logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $admin_id = $_SESSION['admin_id'] ?? null;
    
    // Log the logout action
    if ($admin_id) {
        try {
            createDetailedNotification($pdo, $admin_id, 'logout', 'admins', $admin_id, 'Admin logged out successfully', null, null, ['ip_address' => getUserIP()], getUserIP());
        } catch (Exception $e) {
            error_log("Error logging logout: " . $e->getMessage());
        }
    }
}

// Destroy all session data
$_SESSION = array();

// Delete the session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: ../pages/admin/login.php');
exit;
?>