<?php
// Add Exterior Design Project Functionality

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

// Set JSON content type
header('Content-Type: application/json');

try {
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
    
    // Price and duration can contain both text and numbers - no strict validation needed
    // Just ensure they are not empty (already checked above)
    
    
    // Handle file uploads
    $main_image_path = '';
    $additional_images = [];
    
    // Main image upload
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
        
        // Compress main image (max 1920x1080, quality 85)
        require_once '../../includes/ImageCompressor.php';
        ImageCompressor::compress($main_image_path, null, 85, 1920, 1080);
        
        $main_image_path = 'assets/images/projects/exterior_design/' . $main_image_name;
    } else {
        throw new Exception('وێنەی سەرەکی پێویستە');
    }
    
    // Additional images upload
    // Get main image name to exclude it from additional images
    $main_image_uploaded_name = isset($_FILES['main_image']['name']) ? $_FILES['main_image']['name'] : '';
    
    // Debug: Log additional images
    error_log("🔍 Additional images check:");
    error_log("🔍 _FILES['additional_images'] exists: " . (isset($_FILES['additional_images']) ? 'yes' : 'no'));
    if (isset($_FILES['additional_images'])) {
        error_log("🔍 _FILES['additional_images']['name']: " . print_r($_FILES['additional_images']['name'], true));
        error_log("🔍 _FILES['additional_images']['error']: " . print_r($_FILES['additional_images']['error'], true));
        error_log("🔍 First file name: " . (isset($_FILES['additional_images']['name'][0]) ? $_FILES['additional_images']['name'][0] : 'not set'));
    }
    
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        $gallery_upload_dir = '../../assets/images/projects/exterior_design/gallery/';
        if (!file_exists($gallery_upload_dir)) {
            mkdir($gallery_upload_dir, 0755, true);
        }
        
        $file_count = count($_FILES['additional_images']['name']);
        error_log("🔍 File count: " . $file_count);
        
        for ($i = 0; $i < $file_count; $i++) {
            error_log("🔍 Processing file $i:");
            error_log("🔍   - Name: " . ($_FILES['additional_images']['name'][$i] ?? 'not set'));
            error_log("🔍   - Error: " . ($_FILES['additional_images']['error'][$i] ?? 'not set'));
            
            if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                // Skip if this is the main image (check by name)
                $uploaded_file_name = $_FILES['additional_images']['name'][$i];
                
                // Skip if this file is the same as main image
                if (!empty($main_image_uploaded_name) && $uploaded_file_name === $main_image_uploaded_name) {
                    error_log("🔍   - Skipped (same as main image)");
                    continue;
                }
                
                $file_extension = pathinfo($_FILES['additional_images']['name'][$i], PATHINFO_EXTENSION);
                $image_name = 'gallery_' . time() . '_' . $i . '_' . rand(1000, 9999) . '.' . $file_extension;
                $image_path = $gallery_upload_dir . $image_name;
                
                error_log("🔍   - Moving to: " . $image_path);
                if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $image_path)) {
                    error_log("✅   - File moved successfully");
                    // Compress additional image (max 1200x800, quality 85)
                    if (!class_exists('ImageCompressor')) {
                        require_once '../../includes/ImageCompressor.php';
                    }
                    ImageCompressor::compress($image_path, null, 85, 1200, 800);
                    
                    $additional_images[] = 'assets/images/projects/exterior_design/gallery/' . $image_name;
                    error_log("✅   - Added to additional_images array: " . $additional_images[count($additional_images) - 1]);
                } else {
                    error_log("❌   - Failed to move file");
                }
            } else {
                error_log("❌   - Upload error: " . $_FILES['additional_images']['error'][$i]);
            }
        }
        
        error_log("🔍 Total additional images processed: " . count($additional_images));
    }
    
    // Insert project into database
    $stmt = $pdo->prepare("
        INSERT INTO exterior_design_projects 
        (name, name_ku, name_ar, description, description_ku, description_ar, 
         price, duration, main_image, status, created_by) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active', ?)
    ");
    
    $stmt->execute([
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
    
    // Insert additional images
    error_log("🔍 Inserting additional images into database. Count: " . count($additional_images));
    if (!empty($additional_images)) {
        $image_stmt = $pdo->prepare("
            INSERT INTO exterior_design_images (project_id, image_path, is_main, sort_order) 
            VALUES (?, ?, 0, ?)
        ");
        
        foreach ($additional_images as $index => $image_path) {
            error_log("🔍 Inserting image $index: " . $image_path);
            $image_stmt->execute([$project_id, $image_path, $index + 1]);
            error_log("✅ Image $index inserted successfully");
        }
        error_log("✅ All additional images inserted into database");
    } else {
        error_log("⚠️ No additional images to insert");
    }
    
    // Log the action
    if (function_exists('createDetailedNotification')) {
        createDetailedNotification($pdo, $_SESSION['admin_id'], 'create', 'exterior_design_projects', $project_id, 'New exterior design project created', null, null, ['project_name' => $project_name], getUserIP());
    }
    
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    $response = [
        'success' => true,
        'message' => 'پڕۆژە بە سەرکەوتوویی زیاد کرا',
        'project_id' => $project_id,
        'additional_images_count' => count($additional_images),
        'additional_images' => $additional_images
    ];
    
    error_log("✅ Final response: " . json_encode($response));
    echo json_encode($response);
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
