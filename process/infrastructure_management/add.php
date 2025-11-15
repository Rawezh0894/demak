<?php
// Add Project Functionality

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

// Set JSON content type
header('Content-Type: application/json');

try {
    // Get form data
    $project_name = trim($_POST['project_name'] ?? '');
    $project_category = trim($_POST['project_category'] ?? '');
    $project_price = trim($_POST['project_price'] ?? '');
    $project_duration = trim($_POST['project_duration'] ?? '');
    $project_description = trim($_POST['project_description'] ?? '');
    $project_features = $_POST['project_features'] ?? [];
    
    // Validate required fields
    if (empty($project_name) || empty($project_category) || empty($project_price) || empty($project_duration) || empty($project_description)) {
        throw new Exception('تکایە هەموو خانە پڕەکان پڕ بکەرەوە');
    }
    
    // Validate project name length
    if (strlen($project_name) > 255) {
        throw new Exception('ناوی پڕۆژە زۆر درێژە (زیاتر لە 255 پیت)');
    }
    
    // Validate price format
    if (!preg_match('/^[0-9,.\s]+$/', $project_price)) {
        throw new Exception('نرخەکە پێویستە تەنها ژمارە بێت');
    }
    
    
    // Handle file uploads
    $main_image_path = '';
    $additional_images = [];
    
    // Main image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/projects/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $main_image_name = 'main_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $main_image_path = $upload_dir . $main_image_name;
        
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $main_image_path)) {
            throw new Exception('هەڵەیەک ڕوویدا لە بارکردنی وێنەی سەرەکی');
        }
        
        // Compress main image (max 1920x1080, quality 85)
        require_once '../../includes/ImageCompressor.php';
        ImageCompressor::compress($main_image_path, null, 85, 1920, 1080);
        
        $main_image_path = 'assets/images/projects/' . $main_image_name;
    }
    
    // Additional images upload
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        $upload_dir = '../../assets/images/projects/gallery/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_count = count($_FILES['additional_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                $file_extension = pathinfo($_FILES['additional_images']['name'][$i], PATHINFO_EXTENSION);
                $image_name = 'gallery_' . time() . '_' . $i . '_' . rand(1000, 9999) . '.' . $file_extension;
                $image_path = $upload_dir . $image_name;
                
                if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $image_path)) {
                    // Compress additional image (max 1200x800, quality 85)
                    if (!class_exists('ImageCompressor')) {
                        require_once '../../includes/ImageCompressor.php';
                    }
                    ImageCompressor::compress($image_path, null, 85, 1200, 800);
                    
                    $additional_images[] = 'assets/images/projects/gallery/' . $image_name;
                }
            }
        }
    }
    
    // Insert project into database
    $stmt = $pdo->prepare("
        INSERT INTO infrastructure_projects 
        (category_key, name, name_ku, name_ar, description, description_ku, description_ar, 
         price, duration, main_image, status, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)
    ");
    
    $stmt->execute([
        $project_category,
        $project_name,
        $project_name, // For now, same as English
        $project_name, // For now, same as English
        $project_description,
        $project_description, // For now, same as English
        $project_description, // For now, same as English
        $project_price,
        $project_duration,
        $main_image_path,
        $_SESSION['admin_id']
    ]);
    
    $project_id = $pdo->lastInsertId();
    
    // Insert project features
    if (!empty($project_features)) {
        $feature_stmt = $pdo->prepare("
            INSERT INTO project_features (project_id, feature_text, feature_text_ku, feature_text_ar, sort_order) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($project_features as $index => $feature) {
            if (!empty(trim($feature))) {
                $feature_stmt->execute([
                    $project_id,
                    trim($feature),
                    trim($feature), // For now, same as English
                    trim($feature), // For now, same as English
                    $index + 1
                ]);
            }
        }
    }
    
    // Insert additional images
    if (!empty($additional_images)) {
        $image_stmt = $pdo->prepare("
            INSERT INTO project_images (project_id, image_path, image_type, sort_order) 
            VALUES (?, ?, 'gallery', ?)
        ");
        
        foreach ($additional_images as $index => $image_path) {
            $image_stmt->execute([$project_id, $image_path, $index + 1]);
        }
    }
    
    // Log the action
    if (function_exists('createDetailedNotification')) {
        createDetailedNotification($pdo, $_SESSION['admin_id'], 'create', 'infrastructure_projects', $project_id, 'New infrastructure project created', null, null, ['project_name' => $project_name], getUserIP());
    }
    
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'پڕۆژە بە سەرکەوتوویی زیاد کرا',
        'project_id' => $project_id
    ]);
    exit;
    
} catch (Exception $e) {
    // Return JSON error response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
