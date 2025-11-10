<?php
// API endpoint for getting projects list

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

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

try {
    // Pagination settings
    $items_per_page = 12;
    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;
    
    // Get filter parameters
    $search_term = isset($_GET['search']) && !empty($_GET['search']) ? trim($_GET['search']) : null;
    
    // Build WHERE clause
    $where_conditions = ["is_active = 1"];
    $params = [];
    
    if ($search_term) {
        $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
        $search_pattern = '%' . $search_term . '%';
        $params[] = $search_pattern;
        $params[] = $search_pattern;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM interior_design_projects
        WHERE {$where_clause}
    ");
    $count_stmt->execute($params);
    $total_projects = $count_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $items_per_page);
    
    // Get paginated projects
    $params[] = $items_per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare("
        SELECT * FROM interior_design_projects
        WHERE {$where_clause}
        ORDER BY sort_order ASC, created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute($params);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Load images for each project
    foreach ($projects as &$project) {
        $stmt = $pdo->prepare("SELECT * FROM interior_design_images WHERE project_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$project['id']]);
        $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Prepare response data
    $response_data = [
        'success' => true,
        'projects' => $projects,
        'pagination' => [
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'total_projects' => $total_projects,
            'items_per_page' => $items_per_page,
            'has_prev' => $current_page > 1,
            'has_next' => $current_page < $total_pages
        ]
    ];
    
    echo json_encode($response_data);
    
} catch (Exception $e) {
    error_log("❌ get_projects_list.php error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error loading projects: ' . $e->getMessage()]);
}
?>

