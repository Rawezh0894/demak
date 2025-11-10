<?php
// Delete Project Functionality for Commercial & Residential Design

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
    
    // Get project name for logging
    $stmt = $pdo->prepare("SELECT name FROM commercial_residential_design_projects WHERE id = ? AND created_by = ?");
    $stmt->execute([$project_id, $_SESSION['admin_id']]);
    $project_name = $stmt->fetchColumn();
    
    if (!$project_name) {
        throw new Exception('پڕۆژە نەدۆزرایەوە یان دەسەڵاتت نییە');
    }
    
    // Delete project images from filesystem
    $stmt = $pdo->prepare("SELECT image_path FROM commercial_residential_design_images WHERE project_id = ?");
    $stmt->execute([$project_id]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($images as $image_path) {
        $full_path = '../../' . $image_path;
        if (file_exists($full_path)) {
            unlink($full_path);
        }
    }
    
    // Delete from database (cascade will handle related records)
    $stmt = $pdo->prepare("DELETE FROM commercial_residential_design_projects WHERE id = ? AND created_by = ?");
    $stmt->execute([$project_id, $_SESSION['admin_id']]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('پڕۆژە نەدۆزرایەوە یان دەسەڵاتت نییە');
    }
    
    // Log the action
    if (function_exists('createDetailedNotification')) {
        createDetailedNotification($pdo, $_SESSION['admin_id'], 'delete', 'commercial_residential_design_projects', $project_id, 'Commercial/residential design project deleted', null, null, ['project_name' => $project_name], getUserIP());
    }
    
    // Return JSON response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'پڕۆژە بە سەرکەوتوویی سڕایەوە',
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

