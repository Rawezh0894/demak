<?php
// Update Project Functionality for Commercial & Residential Design

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
    $project_category = trim($_POST['project_category'] ?? '');
    $project_area = trim($_POST['project_area'] ?? ''); // ڕووبەر (Area)
    $project_floors = intval($_POST['project_floors'] ?? 0); // نهۆم (Floors)
    $project_price = trim($_POST['project_price'] ?? '');
    $project_duration = trim($_POST['project_duration'] ?? '');
    $project_description = trim($_POST['project_description'] ?? '');
    $project_features = $_POST['project_features'] ?? [];
    
    // Validate required fields
    if (empty($project_name) || empty($project_category) || empty($project_area) || empty($project_price) || empty($project_duration) || empty($project_description)) {
        throw new Exception('تکایە هەموو خانە پڕەکان پڕ بکەرەوە');
    }
    
    // Validate area format
    if (!preg_match('/^[0-9]+$/', $project_area)) {
        throw new Exception('ڕووبەرەکە پێویستە تەنها ژمارە بێت');
    }
    
    // Validate floors
    if ($project_floors < 0 || $project_floors > 100) {
        throw new Exception('نهۆمەکە پێویستە لە نێوان 0-100 بێت');
    }
    
    // Handle file uploads (optional for update)
    $main_image_path = '';
    $additional_images = [];
    
    // Main image upload (if new image is provided)
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/projects/commercial_residential_design/';
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
        $compression_result = ImageCompressor::compress($main_image_path, null, 85, 1920, 1080);
        
        if ($compression_result && isset($compression_result['success']) && $compression_result['success']) {
            error_log("📊 وێنەی سەرەکی - پێش کۆمپرێس: " . $compression_result['original_size_formatted'] . 
                     " | دوای کۆمپرێس: " . $compression_result['compressed_size_formatted'] . 
                     " | کەمبوونەوە: " . $compression_result['savings_percent'] . "% (" . $compression_result['savings_formatted'] . ")");
        }
        
        $main_image_path = 'assets/images/projects/commercial_residential_design/' . $main_image_name;
    }
    
    // Additional images upload
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        $upload_dir = '../../assets/images/projects/commercial_residential_design/gallery/';
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
                    $compression_result = ImageCompressor::compress($image_path, null, 85, 1200, 800);
                    
                    if ($compression_result && isset($compression_result['success']) && $compression_result['success']) {
                        error_log("📊 وێنەی زیادە #" . ($i + 1) . " - پێش کۆمپرێس: " . $compression_result['original_size_formatted'] . 
                                 " | دوای کۆمپرێس: " . $compression_result['compressed_size_formatted'] . 
                                 " | کەمبوونەوە: " . $compression_result['savings_percent'] . "% (" . $compression_result['savings_formatted'] . ")");
                    }
                    
                    $additional_images[] = 'assets/images/projects/commercial_residential_design/gallery/' . $image_name;
                }
            }
        }
    }
    
    // Update project in database
    if (!empty($main_image_path)) {
        // Update with new main image
        $stmt = $pdo->prepare("
            UPDATE commercial_residential_design_projects 
            SET category_key = ?, name = ?, name_ku = ?, name_ar = ?, 
                description = ?, description_ku = ?, description_ar = ?,
                area = ?, area_ku = ?, area_ar = ?, floors = ?,
                price = ?, duration = ?
            WHERE id = ? AND created_by = ?
        ");
        $stmt->execute([
            $project_category,
            $project_name,
            $project_name,
            $project_name,
            $project_description,
            $project_description,
            $project_description,
            $project_area, // Store only number (unit will be added in display)
            $project_area, // Store only number
            $project_area, // Store only number
            $project_floors,
            $project_price,
            $project_duration,
            $project_id,
            $_SESSION['admin_id']
        ]);
        
        // Update main image
        $stmt = $pdo->prepare("
            UPDATE commercial_residential_design_images 
            SET image_path = ? 
            WHERE project_id = ? AND is_main = 1
        ");
        $stmt->execute([$main_image_path, $project_id]);
        
        if ($stmt->rowCount() === 0) {
            // Insert if doesn't exist
            $stmt = $pdo->prepare("
                INSERT INTO commercial_residential_design_images (project_id, image_path, is_main, sort_order) 
                VALUES (?, ?, 1, 0)
            ");
            $stmt->execute([$project_id, $main_image_path]);
        }
    } else {
        // Update without changing main image
        $stmt = $pdo->prepare("
            UPDATE commercial_residential_design_projects 
            SET category_key = ?, name = ?, name_ku = ?, name_ar = ?, 
                description = ?, description_ku = ?, description_ar = ?,
                area = ?, area_ku = ?, area_ar = ?, floors = ?,
                price = ?, duration = ?
            WHERE id = ? AND created_by = ?
        ");
        $stmt->execute([
            $project_category,
            $project_name,
            $project_name,
            $project_name,
            $project_description,
            $project_description,
            $project_description,
            $project_area, // Store only number (unit will be added in display)
            $project_area, // Store only number
            $project_area, // Store only number
            $project_floors,
            $project_price,
            $project_duration,
            $project_id,
            $_SESSION['admin_id']
        ]);
    }
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('پڕۆژە نەدۆزرایەوە یان دەسەڵاتت نییە');
    }
    
    // Delete existing features and insert new ones
    $stmt = $pdo->prepare("DELETE FROM commercial_residential_design_features WHERE project_id = ?");
    $stmt->execute([$project_id]);
    
    if (!empty($project_features)) {
        $feature_stmt = $pdo->prepare("
            INSERT INTO commercial_residential_design_features (project_id, feature_text, feature_text_ku, feature_text_ar, sort_order) 
            VALUES (?, ?, ?, ?, ?)
        ");
        
        foreach ($project_features as $index => $feature) {
            if (!empty(trim($feature))) {
                $feature_stmt->execute([
                    $project_id,
                    trim($feature),
                    trim($feature),
                    trim($feature),
                    $index + 1
                ]);
            }
        }
    }
    
    // Insert additional images
    if (!empty($additional_images)) {
        $image_stmt = $pdo->prepare("
            INSERT INTO commercial_residential_design_images (project_id, image_path, is_main, sort_order) 
            VALUES (?, ?, 0, ?)
        ");
        
        $current_max_sort = $pdo->query("SELECT MAX(sort_order) FROM commercial_residential_design_images WHERE project_id = $project_id AND is_main = 0")->fetchColumn() ?? 0;
        
        foreach ($additional_images as $index => $image_path) {
            $image_stmt->execute([$project_id, $image_path, $current_max_sort + $index + 1]);
        }
    }
    
    // Log the action
    if (function_exists('createDetailedNotification')) {
        createDetailedNotification($pdo, $_SESSION['admin_id'], 'update', 'commercial_residential_design_projects', $project_id, 'Commercial/residential design project updated', null, null, ['project_name' => $project_name], getUserIP());
    }
    
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'پڕۆژە بە سەرکەوتوویی نوێ کرا',
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

