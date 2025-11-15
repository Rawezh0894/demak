<?php
/**
 * Design Reconstruction Project Add Operation
 * 
 * Professional project management with validation and error handling
 */

require_once '../../includes/DesignReconstructionValidator.php';
require_once '../../includes/ImageCompressor.php';

try {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(DesignReconstructionValidator::formatErrorResponse(['Invalid request. Please try again.']));
        exit;
    }
    
    // Sanitize input data
    $sanitizedData = DesignReconstructionValidator::sanitizeData($_POST);
    
    // Validate project data
    $validationErrors = DesignReconstructionValidator::validateProjectData($sanitizedData);
    if (!empty($validationErrors)) {
        echo json_encode(DesignReconstructionValidator::formatErrorResponse($validationErrors));
        exit;
    }
    
    // Validate uploaded images
    $imageErrors = DesignReconstructionValidator::validateImages($_FILES);
    if (!empty($imageErrors)) {
        echo json_encode(DesignReconstructionValidator::formatErrorResponse($imageErrors));
        exit;
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert main project
    $stmt = $pdo->prepare("
        INSERT INTO design_reconstruction_projects (
            name, 
            category_key, 
            price, 
            duration, 
            description, 
            created_at, 
            updated_at
        ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([
        $sanitizedData['project_name'],
        $sanitizedData['project_category'],
        $sanitizedData['project_price'],
        $sanitizedData['project_duration'],
        $sanitizedData['project_description']
    ]);
    
    $project_id = $pdo->lastInsertId();
    
    // Handle main image upload
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/projects/design_reconstruction/';
        DesignReconstructionValidator::ensureDirectoryExists($upload_dir);
        
        $filename = DesignReconstructionValidator::generateUniqueFilename(
            $_FILES['main_image']['name'], 
            'main_' . $project_id
        );
        $file_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $file_path)) {
            // Compress main image (max 1920x1080, quality 85)
            $compression_result = ImageCompressor::compress($file_path, null, 85, 1920, 1080);
            
            if ($compression_result && isset($compression_result['success']) && $compression_result['success']) {
                error_log("📊 وێنەی سەرەکی - پێش کۆمپرێس: " . $compression_result['original_size_formatted'] . 
                         " | دوای کۆمپرێس: " . $compression_result['compressed_size_formatted'] . 
                         " | کەمبوونەوە: " . $compression_result['savings_percent'] . "% (" . $compression_result['savings_formatted'] . ")");
            }
            
            // Insert main image record
            $image_stmt = $pdo->prepare("
                INSERT INTO design_reconstruction_images (project_id, image_path, is_main, created_at) 
                VALUES (?, ?, 1, NOW())
            ");
            $image_stmt->execute([$project_id, 'assets/images/projects/design_reconstruction/' . $filename]);
        }
    }
    
    // Handle additional images
    error_log("🔍 Checking for additional images...");
    error_log("🔍 _FILES['additional_images'] exists: " . (isset($_FILES['additional_images']) ? 'yes' : 'no'));
    
    if (isset($_FILES['additional_images'])) {
        error_log("🔍 _FILES['additional_images'] structure: " . json_encode($_FILES['additional_images']));
        error_log("🔍 _FILES['additional_images']['name'] exists: " . (isset($_FILES['additional_images']['name']) ? 'yes' : 'no'));
        if (isset($_FILES['additional_images']['name'])) {
            error_log("🔍 _FILES['additional_images']['name']: " . json_encode($_FILES['additional_images']['name']));
            error_log("🔍 _FILES['additional_images']['name'][0] exists: " . (isset($_FILES['additional_images']['name'][0]) ? 'yes' : 'no'));
            if (isset($_FILES['additional_images']['name'][0])) {
                error_log("🔍 _FILES['additional_images']['name'][0]: " . $_FILES['additional_images']['name'][0]);
                error_log("🔍 _FILES['additional_images']['name'][0] is empty: " . (empty($_FILES['additional_images']['name'][0]) ? 'yes' : 'no'));
            }
        }
    }
    
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        error_log("✅ Processing additional images...");
        $upload_dir = '../../assets/images/projects/design_reconstruction/gallery/';
        DesignReconstructionValidator::ensureDirectoryExists($upload_dir);
        
        $file_count = count($_FILES['additional_images']['name']);
        error_log("🔍 File count: " . $file_count);
        
        for ($i = 0; $i < $file_count; $i++) {
            error_log("🔍 Processing file $i: " . $_FILES['additional_images']['name'][$i]);
            error_log("🔍 File $i error code: " . $_FILES['additional_images']['error'][$i]);
            
            if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                $filename = DesignReconstructionValidator::generateUniqueFilename(
                    $_FILES['additional_images']['name'][$i], 
                    'gallery_' . $project_id . '_' . $i
                );
                $file_path = $upload_dir . $filename;
                
                error_log("🔍 Moving file $i to: " . $file_path);
                
                if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $file_path)) {
                    error_log("✅ File $i moved successfully");
                    
                    // Compress additional image (max 1200x800, quality 85)
                    $compression_result = ImageCompressor::compress($file_path, null, 85, 1200, 800);
                    
                    if ($compression_result && isset($compression_result['success']) && $compression_result['success']) {
                        error_log("📊 وێنەی زیادە #" . ($i + 1) . " - پێش کۆمپرێس: " . $compression_result['original_size_formatted'] . 
                                 " | دوای کۆمپرێس: " . $compression_result['compressed_size_formatted'] . 
                                 " | کەمبوونەوە: " . $compression_result['savings_percent'] . "% (" . $compression_result['savings_formatted'] . ")");
                    }
                    
                    // Insert additional image record
                    $image_stmt = $pdo->prepare("
                        INSERT INTO design_reconstruction_images (project_id, image_path, is_main, created_at) 
                        VALUES (?, ?, 0, NOW())
                    ");
                    $image_path = 'assets/images/projects/design_reconstruction/gallery/' . $filename;
                    $image_stmt->execute([$project_id, $image_path]);
                    error_log("✅ Image record inserted: " . $image_path);
                } else {
                    error_log("❌ Failed to move file $i");
                }
            } else {
                error_log("❌ File $i upload error: " . $_FILES['additional_images']['error'][$i]);
            }
        }
    } else {
        error_log("⚠️ No additional images found or first file is empty");
    }
    
    // Handle project features
    if (isset($sanitizedData['project_features']) && is_array($sanitizedData['project_features'])) {
        foreach ($sanitizedData['project_features'] as $feature) {
            if (!empty(trim($feature))) {
                $feature_stmt = $pdo->prepare("
                    INSERT INTO design_reconstruction_features (project_id, feature_text, created_at) 
                    VALUES (?, ?, NOW())
                ");
                $feature_stmt->execute([$project_id, trim($feature)]);
            }
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Get the complete project data to return
    $project_query = "
        SELECT 
            p.*,
            c.title as category_name,
            c.category_key as category_key
        FROM design_reconstruction_projects p
        LEFT JOIN design_reconstruction_categories c ON p.category_key = c.category_key
        WHERE p.id = ?
    ";
    $project_stmt = $pdo->prepare($project_query);
    $project_stmt->execute([$project_id]);
    $project = $project_stmt->fetch(PDO::FETCH_ASSOC);
    
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
    
    echo json_encode(DesignReconstructionValidator::formatSuccessResponse(
        'Project saved successfully',
        [
            'project_id' => $project_id,
            'project' => $project
        ]
    ));
    exit;
    
} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    error_log("Error adding design reconstruction project: " . $e->getMessage());
    echo json_encode(DesignReconstructionValidator::formatErrorResponse(['Server error occurred. Please try again.']));
    exit;
}
?>
