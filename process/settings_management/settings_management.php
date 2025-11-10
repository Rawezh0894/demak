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
        case 'settings_updated':
            $success_message = t('settings_updated_successfully') ?? 'ڕێکخستنەکان بە سەرکەوتوویی نوێ کراونەوە';
            break;
        case 'setting_added':
            $success_message = t('setting_added_successfully') ?? 'ڕێکخستن بە سەرکەوتوویی زیاد کرا';
            break;
        case 'setting_deleted':
            $success_message = t('setting_deleted_successfully') ?? 'ڕێکخستن بە سەرکەوتوویی سڕایەوە';
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
        $unique_id = isset($_POST['setting_id']) && !empty($_POST['setting_id']) ? $_POST['setting_id'] : 'new';
        $action_key = $_POST['action'] . '_' . $unique_id;
        
        // Check if same action was processed recently (within last 3 seconds)
        $recent_action_found = false;
        if (isset($_SESSION['processed_actions'][$action_key])) {
            $action_timestamp = $_SESSION['processed_actions'][$action_key];
            if (($current_time - $action_timestamp) < 3) {
                $recent_action_found = true;
            }
        }
        
        if (!$recent_action_found) {
            $_SESSION['processed_actions'][$action_key] = $current_time;
            
            try {
                $result = null;
                switch ($_POST['action']) {
                    case 'update_settings':
                        require_once '../../process/settings_management/update.php';
                        break;
                    case 'add_setting':
                        require_once '../../process/settings_management/add.php';
                        break;
                    case 'delete_setting':
                        require_once '../../process/settings_management/delete.php';
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

// Clean old processed actions (older than 1 minute) - done in POST handler above

// Load data
require_once '../../process/settings_management/select.php';
?>




