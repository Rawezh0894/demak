<?php
// API endpoint for getting project data
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
if (!isset($_GET['project_id']) || empty($_GET['project_id'])) {
    echo json_encode(['success' => false, 'message' => 'Project ID is required']);
    exit;
}

$project_id = intval($_GET['project_id']);

try {
    // Get project data
    $stmt = $pdo->prepare("
        SELECT ip.*, ic.title as category_title, ic.title_ku as category_title_ku, ic.title_ar as category_title_ar,
               ic.icon as category_icon, ic.color as category_color
        FROM infrastructure_projects ip
        LEFT JOIN infrastructure_categories ic ON ip.category_key = ic.key
        WHERE ip.id = ? AND ip.created_by = ? AND ip.is_active = 1
    ");
    $stmt->execute([$project_id, $_SESSION['admin_id']]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
        exit;
    }
    
    // Get project features
    $stmt = $pdo->prepare("SELECT * FROM project_features WHERE project_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$project_id]);
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get project images
    $stmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$project_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Prepare response data
    $response_data = [
        'success' => true,
        'project' => [
            'id' => $project['id'],
            'name' => $project['name'],
            'category_key' => $project['category_key'],
            'price' => $project['price'],
            'duration' => $project['duration'],
            'description' => $project['description'],
            'main_image' => $project['main_image'],
            'category_title' => $project['category_title']
        ],
        'features' => array_map(function($feature) {
            return $feature['feature_text'];
        }, $features),
        'images' => array_map(function($image) {
            return [
                'id' => $image['id'],
                'path' => $image['image_path'],
                'type' => $image['image_type'],
                'sort_order' => $image['sort_order']
            ];
        }, $images)
    ];
    
    echo json_encode($response_data);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading project data: ' . $e->getMessage()]);
}
?>
