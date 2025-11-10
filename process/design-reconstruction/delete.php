<?php
/**
 * Design Reconstruction Project Delete Operation
 * 
 * This file handles deleting design reconstruction projects
 */

try {
    // Validate required fields
    if (empty($_POST['project_id'])) {
        $response = ['success' => false, 'message' => 'Project ID is required'];
        
        // If called directly (not via design-reconstruction.php), echo and exit
        if (!isset($GLOBALS['delete_response_capture'])) {
            echo json_encode($response);
            exit;
        }
        
        // If called via design-reconstruction.php, return response
        return $response;
    }
    
    $project_id = $_POST['project_id'];
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Get project images to delete files
    $images_stmt = $pdo->prepare("SELECT image_path FROM design_reconstruction_images WHERE project_id = ?");
    $images_stmt->execute([$project_id]);
    $images = $images_stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Delete image files
    foreach ($images as $image_path) {
        if (file_exists('../../' . $image_path)) {
            unlink('../../' . $image_path);
        }
    }
    
    // Delete project images records
    $delete_images_stmt = $pdo->prepare("DELETE FROM design_reconstruction_images WHERE project_id = ?");
    $delete_images_stmt->execute([$project_id]);
    
    // Delete project features
    $delete_features_stmt = $pdo->prepare("DELETE FROM design_reconstruction_features WHERE project_id = ?");
    $delete_features_stmt->execute([$project_id]);
    
    // Delete main project
    $delete_project_stmt = $pdo->prepare("DELETE FROM design_reconstruction_projects WHERE id = ?");
    $delete_project_stmt->execute([$project_id]);
    
    // Commit transaction
    $pdo->commit();
    
    $response = [
        'success' => true, 
        'message' => 'Project deleted successfully',
        'project_id' => $project_id
    ];
    
    // If called directly (not via design-reconstruction.php), echo and exit
    if (!isset($GLOBALS['delete_response_capture'])) {
        echo json_encode($response);
        exit;
    }
    
    // If called via design-reconstruction.php, return response
    return $response;
    
} catch (Exception $e) {
    // Rollback transaction on error
    $pdo->rollBack();
    error_log("Error deleting design reconstruction project: " . $e->getMessage());
    
    $response = ['success' => false, 'message' => 'Error deleting project: ' . $e->getMessage()];
    
    // If called directly (not via design-reconstruction.php), echo and exit
    if (!isset($GLOBALS['delete_response_capture'])) {
        echo json_encode($response);
        exit;
    }
    
    // If called via design-reconstruction.php, return response
    return $response;
}
?>
