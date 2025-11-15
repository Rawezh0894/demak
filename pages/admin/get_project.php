<?php
/**
 * Get single design reconstruction project
 */

// Start session
session_start();

// Include database connection
require_once '../../config/db_conected.php';

// Set JSON content type
header('Content-Type: application/json');

try {
    // Check if project ID is provided
    if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
        exit;
    }
    
    $project_id = (int)$_GET['id'];
    
    // Get project data
    $project_query = "
        SELECT 
            p.*,
            c.title as category_name,
            c.title_ku as category_name_ku,
            c.title_ar as category_name_ar,
            c.category_key as category_key
        FROM design_reconstruction_projects p
        LEFT JOIN design_reconstruction_categories c ON p.category_key = c.category_key
        WHERE p.id = ?
    ";
    $project_stmt = $pdo->prepare($project_query);
    $project_stmt->execute([$project_id]);
    $project = $project_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
        exit;
    }
    
    // Get project images
    $images_query = "SELECT * FROM design_reconstruction_images WHERE project_id = ? ORDER BY is_main DESC, id ASC";
    $images_stmt = $pdo->prepare($images_query);
    $images_stmt->execute([$project_id]);
    $images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
    $project['images'] = $images;
    
    // Set main image
    $project['main_image'] = null;
    foreach ($images as $image) {
        if ($image['is_main']) {
            $project['main_image'] = $image['image_path'];
            break;
        }
    }
    
    // Get project features
    $features_query = "SELECT feature_text FROM design_reconstruction_features WHERE project_id = ?";
    $features_stmt = $pdo->prepare($features_query);
    $features_stmt->execute([$project_id]);
    $features = $features_stmt->fetchAll(PDO::FETCH_COLUMN);
    $project['features'] = $features;
    
    echo json_encode([
        'success' => true,
        'project' => $project
    ]);
    
} catch (Exception $e) {
    error_log("Error fetching design reconstruction project: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error fetching project: ' . $e->getMessage()]);
}
?>

