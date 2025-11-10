<?php
/**
 * Design Reconstruction Get Project API
 * 
 * API endpoint for fetching a single project data for editing
 */

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

session_start();
require_once '../../config/db_conected.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Check if project ID is provided
$project_id = isset($_GET['id']) ? intval($_GET['id']) : (isset($_GET['project_id']) ? intval($_GET['project_id']) : null);

if (!$project_id) {
    echo json_encode(['success' => false, 'message' => 'Project ID is required']);
    exit;
}

try {
    // Get project data
    $stmt = $pdo->prepare("
        SELECT 
            drp.*,
            drc.title as category_title,
            drc.icon as category_icon,
            drc.color as category_color
        FROM design_reconstruction_projects drp
        LEFT JOIN design_reconstruction_categories drc ON drp.category_key = drc.category_key
        WHERE drp.id = ?
    ");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
        exit;
    }
    
    // Get project images
    $images_stmt = $pdo->prepare("
        SELECT image_path, is_main 
        FROM design_reconstruction_images 
        WHERE project_id = ? 
        ORDER BY is_main DESC, created_at ASC
    ");
    $images_stmt->execute([$project_id]);
    $images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Set main image and additional images
    $project['main_image'] = null;
    $project['images'] = [];
    
    foreach ($images as $image) {
        if ($image['is_main']) {
            $project['main_image'] = $image['image_path'];
        }
        $project['images'][] = $image['image_path'];
    }
    
    // Get project features
    $features_stmt = $pdo->prepare("
        SELECT feature_text 
        FROM design_reconstruction_features 
        WHERE project_id = ? 
        ORDER BY created_at ASC
    ");
    $features_stmt->execute([$project_id]);
    $features = $features_stmt->fetchAll(PDO::FETCH_COLUMN);
    $project['features'] = $features;
    
    // Prepare response
    echo json_encode([
        'success' => true,
        'project' => $project
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching design reconstruction project: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error loading project data: ' . $e->getMessage()]);
}
?>





