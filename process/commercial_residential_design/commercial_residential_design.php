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
        // Initialize processed_actions array if not exists
        if (!isset($_SESSION['processed_actions'])) {
            $_SESSION['processed_actions'] = [];
        }
        
        // Clean old processed actions (older than 10 seconds) before checking
        $current_time = time();
        foreach ($_SESSION['processed_actions'] as $key => $timestamp) {
            if ($current_time - $timestamp > 10) { // 10 seconds
                unset($_SESSION['processed_actions'][$key]);
            }
        }
        
        // Prevent duplicate submissions by checking if already processed
        // For add_project: allow multiple submissions but prevent rapid duplicates (within 3 seconds)
        // For edit/delete: use project_id to prevent duplicate operations on same project
        $recent_action_found = false;
        $action_key = '';
        
        if ($_POST['action'] === 'add_project') {
            // For add_project, check if same form was submitted within last 3 seconds
            // Use a simple key based on action only (not form data, as files make it unique each time)
            $action_key = $_POST['action'] . '_last';
            if (isset($_SESSION['processed_actions'][$action_key])) {
                $action_timestamp = $_SESSION['processed_actions'][$action_key];
                if (($current_time - $action_timestamp) < 3) {
                    $recent_action_found = true;
                }
            }
            
            if (!$recent_action_found) {
                $_SESSION['processed_actions'][$action_key] = $current_time;
            }
        } else {
            // For edit/delete, use project_id
            $unique_id = isset($_POST['project_id']) && !empty($_POST['project_id']) ? $_POST['project_id'] : 'unknown';
            $action_key = $_POST['action'] . '_' . $unique_id;
            
            // Check if same action was processed recently (within last 3 seconds)
            if (isset($_SESSION['processed_actions'][$action_key])) {
                $action_timestamp = $_SESSION['processed_actions'][$action_key];
                if (($current_time - $action_timestamp) < 3) {
                    $recent_action_found = true;
                }
            }
            
            if (!$recent_action_found) {
                $_SESSION['processed_actions'][$action_key] = $current_time;
            }
        }
        
        if (!$recent_action_found) {
            
            try {
                switch ($_POST['action']) {
                    case 'add_project':
                        require_once '../../process/commercial_residential_design/add.php';
                        break;
                    case 'edit_project':
                        require_once '../../process/commercial_residential_design/update.php';
                        break;
                    case 'delete_project':
                        require_once '../../process/commercial_residential_design/delete.php';
                        break;
                    default:
                        echo json_encode(['success' => false, 'message' => 'Invalid action']);
                        exit;
                }
                
                // Keep the action key for 3 seconds to prevent immediate duplicate submissions
                // It will be automatically cleaned up after 10 seconds
            } catch (Exception $e) {
                error_log('Form processing error: ' . $e->getMessage());
                // Remove action key on error so user can retry
                if (isset($_SESSION['processed_actions'][$action_key])) {
                    unset($_SESSION['processed_actions'][$action_key]);
                }
                echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
                exit;
            }
        } else {
            // Action already processed recently
            echo json_encode(['success' => false, 'message' => 'Action already processed. Please wait a moment and try again.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No action specified']);
        exit;
    }
}

// Clean old processed actions (older than 10 seconds) - already done above, but keep this as backup
if (isset($_SESSION['processed_actions'])) {
    $current_time = time();
    foreach ($_SESSION['processed_actions'] as $key => $timestamp) {
        if ($current_time - $timestamp > 10) {
            unset($_SESSION['processed_actions'][$key]);
        }
    }
}

// Load data for display
require_once '../../process/commercial_residential_design/select.php';

// Clear output buffer before including template
ob_end_clean();
?>

