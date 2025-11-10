<?php
// Get Interior Design Project by ID

session_start();
require_once '../../config/db_conected.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

try {
    $project_id = intval($_GET['id'] ?? 0);
    if ($project_id <= 0) {
        throw new Exception('پڕۆژەی هەڵە');
    }
    
    $stmt = $pdo->prepare("
        SELECT * FROM interior_design_projects
        WHERE id = ? AND created_by = ? AND is_active = 1
    ");
    $stmt->execute([$project_id, $_SESSION['admin_id']]);
    $project = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$project) {
        throw new Exception('پڕۆژە نەدۆزرایەوە یان دەسەڵاتت نییە');
    }
    
    // Load project images
    $stmt = $pdo->prepare("SELECT * FROM interior_design_images WHERE project_id = ? ORDER BY sort_order ASC");
    $stmt->execute([$project_id]);
    $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'project' => $project
    ]);
    exit;
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>
