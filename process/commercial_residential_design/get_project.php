<?php
// API endpoint for getting commercial residential design project data
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
        SELECT crp.*, crc.title as category_title, crc.title_ku as category_title_ku, crc.title_ar as category_title_ar,
               crc.icon as category_icon, crc.color as category_color
        FROM commercial_residential_design_projects crp
        LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
        WHERE crp.id = ? AND crp.created_by = ? AND crp.is_active = 1
    ");
    $stmt->execute([$project_id, $_SESSION['admin_id']]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        echo json_encode(['success' => false, 'message' => 'Project not found']);
        exit;
    }
    
    // Get project features
    $stmt = $pdo->prepare("SELECT * FROM commercial_residential_design_features WHERE project_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$project_id]);
    $features = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get project images
    $stmt = $pdo->prepare("SELECT * FROM commercial_residential_design_images WHERE project_id = ? ORDER BY is_main DESC, sort_order ASC");
    $stmt->execute([$project_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get main image
    $main_image = '';
    foreach ($images as $img) {
        if ($img['is_main'] == 1) {
            $main_image = $img['image_path'];
            break;
        }
    }
    if (empty($main_image) && !empty($images)) {
        $main_image = $images[0]['image_path'];
    }
    
    // Clean area value - remove "م²" or any text, keep only numbers
    $area_clean = $project['area'];
    if (!empty($area_clean)) {
        // Remove all non-numeric characters except decimal point and comma
        $area_clean = preg_replace('/[^\d.,]/', '', $area_clean);
        // Remove commas
        $area_clean = str_replace(',', '', $area_clean);
    }
    
    // Prepare response data
    $response_data = [
        'success' => true,
        'project' => [
            'id' => $project['id'],
            'name' => $project['name'],
            'category_key' => $project['category_key'],
            'area' => $area_clean, // Clean area value (numbers only)
            'floors' => $project['floors'],
            'price' => $project['price'],
            'duration' => $project['duration'],
            'description' => $project['description'],
            'main_image' => $main_image,
            'category_title' => $project['category_title']
        ],
        'features' => array_map(function($feature) {
            return $feature['feature_text'];
        }, $features),
        'images' => array_map(function($image) {
            return [
                'id' => $image['id'],
                'path' => $image['image_path'],
                'is_main' => $image['is_main'],
                'sort_order' => $image['sort_order']
            ];
        }, $images)
    ];
    
    echo json_encode($response_data);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading project data: ' . $e->getMessage()]);
}
?>


