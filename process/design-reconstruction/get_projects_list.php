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
    $items_per_page = 10; // 10 projects per page
    $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;
    
    // Get filter parameters
    $category_filter = isset($_GET['category']) && !empty($_GET['category']) ? $_GET['category'] : null;
    $search_term = isset($_GET['search']) && !empty($_GET['search']) ? trim($_GET['search']) : null;
    
    // Build WHERE clause
    $where_conditions = [];
    $params = [];
    
    if ($category_filter) {
        $where_conditions[] = "drp.category_key = ?";
        $params[] = $category_filter;
    }
    
    if ($search_term) {
        $where_conditions[] = "(drp.name LIKE ? OR drp.description LIKE ? OR drc.title LIKE ?)";
        $search_pattern = '%' . $search_term . '%';
        $params[] = $search_pattern;
        $params[] = $search_pattern;
        $params[] = $search_pattern;
    }
    
    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM design_reconstruction_projects drp
        LEFT JOIN design_reconstruction_categories drc ON drp.category_key = drc.category_key
        {$where_clause}
    ");
    $count_stmt->execute($params);
    $total_projects = $count_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $items_per_page);
    
    // Get paginated projects
    $params[] = $items_per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare("
        SELECT 
            drp.*,
            drc.title as category_title,
            drc.title_ku as category_title_ku,
            drc.title_ar as category_title_ar,
            drc.icon as category_icon,
            drc.color as category_color
        FROM design_reconstruction_projects drp
        LEFT JOIN design_reconstruction_categories drc ON drp.category_key = drc.category_key
        {$where_clause}
        ORDER BY drp.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute($params);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Process projects to include additional data
    foreach ($projects as &$project) {
        // Get project images
        $image_stmt = $pdo->prepare("
            SELECT image_path, is_main 
            FROM design_reconstruction_images 
            WHERE project_id = ? 
            ORDER BY is_main DESC, created_at ASC
        ");
        $image_stmt->execute([$project['id']]);
        $images = $image_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Set main image
        $project['main_image'] = null;
        $project['images'] = [];
        
        foreach ($images as $image) {
            if ($image['is_main']) {
                $project['main_image'] = $image['image_path'];
            }
            $project['images'][] = $image['image_path'];
        }
        
        // Get project features
        $features_stmt = $pdo->prepare("
            SELECT feature_text 
            FROM design_reconstruction_features 
            WHERE project_id = ? 
            ORDER BY created_at ASC
        ");
        $features_stmt->execute([$project['id']]);
        $features = $features_stmt->fetchAll(PDO::FETCH_COLUMN);
        $project['features'] = $features;
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

