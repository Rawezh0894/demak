<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to catch any unexpected output
ob_start();

session_start();
require_once '../../config/db_conected.php';
require_once '../../includes/translations.php';

// Prevent caching of POST requests
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Return JSON error instead of redirect
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Handle form submissions
$success_message = '';
$error_message = '';

// Handle success messages from URL parameters
if (isset($_GET['success'])) {
    switch ($_GET['success']) {
        case 'project_added':
            $success_message = t('project_saved_successfully');
            break;
        case 'project_updated':
            $success_message = t('project_updated_successfully');
            break;
        case 'project_deleted':
            $success_message = t('project_deleted_successfully');
            break;
    }
}

// Handle duplicate action message
if (isset($_GET['duplicate_action']) && $_GET['duplicate_action'] == '1') {
    $error_message = 'ئەم کردارە پێشتر جێبەجێ کراوە. تکایە دووبارە هەوڵ مەدە.';
}

// Process form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clear any output buffer content
    ob_clean();
    
    // Set JSON content type for AJAX requests
    header('Content-Type: application/json');
    
    // CSRF Protection
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Please try again.']);
        exit;
    } else if (isset($_POST['action'])) {
        // Prevent duplicate submissions by checking if already processed
        $action_key = $_POST['action'] . '_' . md5(serialize($_POST));
        if (!isset($_SESSION['processed_actions'][$action_key])) {
            $_SESSION['processed_actions'][$action_key] = time();
            
            try {
                switch ($_POST['action']) {
                    case 'add_project':
                        require_once '../../process/interior_design/add.php';
                        break;
                    case 'edit_project':
                        require_once '../../process/interior_design/update.php';
                        break;
                    case 'delete_project':
                        require_once '../../process/interior_design/delete.php';
                        break;
                    default:
                        echo json_encode(['success' => false, 'message' => 'Invalid action']);
                        exit;
                }
            } catch (Exception $e) {
                error_log('Form processing error: ' . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
                exit;
            }
        } else {
            // Action already processed
            echo json_encode(['success' => false, 'message' => 'Action already processed']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
        exit;
    }
}

// Clean old processed actions (older than 1 hour)
if (isset($_SESSION['processed_actions'])) {
    $current_time = time();
    foreach ($_SESSION['processed_actions'] as $key => $timestamp) {
        if ($current_time - $timestamp > 3600) { // 1 hour
            unset($_SESSION['processed_actions'][$key]);
        }
    }
}

// Load data
require_once '../../process/interior_design/select.php';
?>
