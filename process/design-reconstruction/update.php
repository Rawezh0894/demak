<?php
/**
 * Design Reconstruction Project Update Operation
 * 
 * This file handles updating existing design reconstruction projects
 */

require_once '../../includes/ImageCompressor.php';

try {
    // Validate required fields
    $required_fields = ['project_id', 'project_name', 'project_category', 'project_price', 'project_duration', 'project_description'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => ucfirst(str_replace('_', ' ', $field)) . ' is required']);
            exit;
        }
    }
    
    $project_id = $_POST['project_id'];
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Update main project
    $stmt = $pdo->prepare("
        UPDATE design_reconstruction_projects 
        SET 
            name = ?, 
            category_key = ?, 
            price = ?, 
            duration = ?, 
            description = ?, 
            updated_at = NOW()
        WHERE id = ?
    ");
    
    $stmt->execute([
        $_POST['project_name'],
        $_POST['project_category'],
        $_POST['project_price'],
        $_POST['project_duration'],
        $_POST['project_description'],
        $project_id
    ]);
    
    // Handle main image upload (if new image provided)
    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        // Delete old main image
        $old_image_stmt = $pdo->prepare("SELECT image_path FROM design_reconstruction_images WHERE project_id = ? AND is_main = 1");
        $old_image_stmt->execute([$project_id]);
        $old_image = $old_image_stmt->fetchColumn();
        
        if ($old_image && file_exists('../../' . $old_image)) {
            unlink('../../' . $old_image);
        }
        
        // Delete old main image record
        $delete_stmt = $pdo->prepare("DELETE FROM design_reconstruction_images WHERE project_id = ? AND is_main = 1");
        $delete_stmt->execute([$project_id]);
        
        // Upload new main image
        $upload_dir = '../../assets/images/projects/design_reconstruction/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['main_image']['name'], PATHINFO_EXTENSION);
        $filename = 'main_' . $project_id . '_' . time() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $file_path)) {
            // Compress main image (max 1920x1080, quality 85)
            ImageCompressor::compress($file_path, null, 85, 1920, 1080);
            
            // Insert new main image record
            $image_stmt = $pdo->prepare("
                INSERT INTO design_reconstruction_images (project_id, image_path, is_main, created_at) 
                VALUES (?, ?, 1, NOW())
            ");
            $image_stmt->execute([$project_id, 'assets/images/projects/design_reconstruction/' . $filename]);
        }
    }
    
    // Handle additional images (if new images provided)
    if (isset($_FILES['additional_images']) && !empty($_FILES['additional_images']['name'][0])) {
        // Delete old additional images
        $old_images_stmt = $pdo->prepare("SELECT image_path FROM design_reconstruction_images WHERE project_id = ? AND is_main = 0");
        $old_images_stmt->execute([$project_id]);
        $old_images = $old_images_stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($old_images as $old_image) {
            if (file_exists('../../' . $old_image)) {
                unlink('../../' . $old_image);
            }
        }
        
        // Delete old additional image records
        $delete_stmt = $pdo->prepare("DELETE FROM design_reconstruction_images WHERE project_id = ? AND is_main = 0");
        $delete_stmt->execute([$project_id]);
        
        // Upload new additional images
        $upload_dir = '../../assets/images/projects/design_reconstruction/gallery/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_count = count($_FILES['additional_images']['name']);
        for ($i = 0; $i < $file_count; $i++) {
            if ($_FILES['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                $file_extension = pathinfo($_FILES['additional_images']['name'][$i], PATHINFO_EXTENSION);
                $filename = 'gallery_' . $project_id . '_' . $i . '_' . time() . '.' . $file_extension;
                $file_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['additional_images']['tmp_name'][$i], $file_path)) {
                    // Compress additional image (max 1200x800, quality 85)
                    ImageCompressor::compress($file_path, null, 85, 1200, 800);
                    
                    // Insert new additional image record
                    $image_stmt = $pdo->prepare("
                        INSERT INTO design_reconstruction_images (project_id, image_path, is_main, created_at) 
                        VALUES (?, ?, 0, NOW())
                    ");
                    $image_stmt->execute([$project_id, 'assets/images/projects/design_reconstruction/gallery/' . $filename]);
                }
            }
        }
    }
    
    // Handle project features
    // Delete old features
    $delete_features_stmt = $pdo->prepare("DELETE FROM design_reconstruction_features WHERE project_id = ?");
    $delete_features_stmt->execute([$project_id]);
    
    // Insert new features
    if (isset($_POST['project_features']) && is_array($_POST['project_features'])) {
        foreach ($_POST['project_features'] as $feature) {
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
    
    // Get the complete updated project data to return
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
    
    echo json_encode([
        'success' => true, 
        'message' => 'Project updated successfully',
        'project_id' => $project_id,
        'project' => $project
    ]);
    exit; // Important: Stop execution after sending JSON response
    
} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    error_log("Error updating design reconstruction project: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error updating project: ' . $e->getMessage()]);
    exit; // Important: Stop execution after sending JSON response
}
?>
