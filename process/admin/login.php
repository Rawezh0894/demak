<?php
/**
 * Admin Login Process Handler
 * Handles login form submission and authentication
 */

session_start();
require_once __DIR__ . '/../../config/db_conected.php';
require_once __DIR__ . '/../../includes/translations.php';

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: admin_panel.php');
    exit;
}

$error_message = '';
$success_message = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = t('please_fill_all_fields');
    } else {
        try {
            // Check if admin exists
            $stmt = $pdo->prepare("SELECT id, username, password, full_name, email, role, is_active, last_login FROM admins WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Update last login
                $update_stmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = ?");
                $update_stmt->execute([$admin['id']]);
                
                // Set session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                
                // Log the login
                createDetailedNotification($pdo, $admin['id'], 'login', 'admins', $admin['id'], 'Admin logged in successfully', null, null, ['ip_address' => getUserIP()], getUserIP());
                
                // Redirect to admin panel
                header('Location: admin_panel.php');
                exit;
            } else {
                $error_message = t('invalid_credentials');
                
                // Log failed login attempt
                if ($admin) {
                    createDetailedNotification($pdo, $admin['id'], 'failed_login', 'admins', $admin['id'], 'Failed login attempt', null, null, ['ip_address' => getUserIP(), 'reason' => 'invalid_password'], getUserIP());
                }
            }
        } catch (Exception $e) {
            $error_message = t('login_error');
            error_log("Login error: " . $e->getMessage());
        }
    }
}

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];
?>
