<?php
// Update Exterior Design Project Functionality

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

// Set JSON content type
header('Content-Type: application/json');

try {
    $project_id = intval($_POST['project_id'] ?? 0);
    if ($project_id <= 0) {
        throw new Exception('پڕۆژەی هەڵە');
    }
    
    // Get form data
    $project_name = trim($_POST['project_name'] ?? '');
    $project_price = trim($_POST['project_price'] ?? '');
    $project_duration = trim($_POST['project_duration'] ?? '');
    $project_description = trim($_POST['project_description'] ?? '');
    
    // Validate required fields
    if (empty($project_name) || empty($project_price) || empty($project_duration) || empty($project_description)) {
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
    
    
    // Handle file uploads (optional for update)
    $main_image_path = '';
    $additional_images = [];
    
    // Main image upload (if new image is provided)
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/projects/exterior_design/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $main_image_name = 'main_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $main_image_path = $upload_dir . $main_image_name;
        
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $main_image_path)) {
            throw new Exception('هەڵەیەک ڕوویدا لە بارکردنی وێنەی سەرەکی');
        }
        
        $main_image_path = 'assets/images/projects/exterior_design/' . $main_image_name;
    }
    
    // Additional images upload (if new images are provided)
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        $upload_dir = '../../assets/images/projects/exterior_design/gallery/';
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
                    $additional_images[] = 'assets/images/projects/exterior_design/gallery/' . $image_name;
                }
            }
        }
    }
    
    // Update project
    $update_fields = "name = ?, name_ku = ?, name_ar = ?, 
                     description = ?, description_ku = ?, description_ar = ?, 
                     price = ?, duration = ?, updated_at = NOW()";
    $update_values = [
        $project_name,
        $project_name,
        $project_name,
        $project_description,
        $project_description,
        $project_description,
        $project_price,
        $project_duration
    ];
    
    // Add main image if provided
    if (!empty($main_image_path)) {
        $update_fields .= ", main_image = ?";
        $update_values[] = $main_image_path;
    }
    
    $update_values[] = $project_id;
    $update_values[] = $_SESSION['admin_id'];
    
    $stmt = $pdo->prepare("
        UPDATE exterior_design_projects 
        SET {$update_fields}
        WHERE id = ? AND created_by = ?
    ");
    
    $stmt->execute($update_values);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('پڕۆژە نەدۆزرایەوە یان دەسەڵاتت نییە');
    }
    
    // Insert additional images if provided
    if (!empty($additional_images)) {
        $image_stmt = $pdo->prepare("
            INSERT INTO exterior_design_images (project_id, image_path, is_main, sort_order) 
            VALUES (?, ?, 0, ?)
        ");
        
        // Get current max sort_order
        $max_stmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order), 0) FROM exterior_design_images WHERE project_id = ?");
        $max_stmt->execute([$project_id]);
        $max_sort = $max_stmt->fetchColumn();
        
        foreach ($additional_images as $index => $image_path) {
            $image_stmt->execute([$project_id, $image_path, $max_sort + $index + 1]);
        }
    }
    
    // Log the action
    if (function_exists('createDetailedNotification')) {
        createDetailedNotification($pdo, $_SESSION['admin_id'], 'update', 'exterior_design_projects', $project_id, 'Exterior design project updated', null, null, ['project_name' => $project_name], getUserIP());
    }
    
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'پڕۆژە بە سەرکەوتوویی نوێ کراەوە',
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
