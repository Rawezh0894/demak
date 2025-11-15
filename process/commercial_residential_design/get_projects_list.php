<?php
// API endpoint for getting projects list
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
    $where_conditions = ["crp.is_active = 1"];
    $params = [];
    
    if ($category_filter) {
        $where_conditions[] = "crp.category_key = ?";
        $params[] = $category_filter;
    }
    
    if ($search_term) {
        $where_conditions[] = "(crp.name LIKE ? OR crp.description LIKE ? OR crc.title LIKE ?)";
        $search_pattern = '%' . $search_term . '%';
        $params[] = $search_pattern;
        $params[] = $search_pattern;
        $params[] = $search_pattern;
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    
    // Get total count
    $count_stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM commercial_residential_design_projects crp
        LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
        WHERE {$where_clause}
    ");
    $count_stmt->execute($params);
    $total_projects = $count_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $items_per_page);
    
    // Get paginated projects
    $params[] = $items_per_page;
    $params[] = $offset;
    
    $stmt = $pdo->prepare("
        SELECT crp.*, crc.title as category_title, crc.title_ku as category_title_ku, crc.title_ar as category_title_ar,
               crc.icon as category_icon, crc.color as category_color
        FROM commercial_residential_design_projects crp
        LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
        WHERE {$where_clause}
        ORDER BY crp.sort_order ASC, crp.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute($params);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get images for each project
    foreach ($projects as &$project) {
        $stmt = $pdo->prepare("SELECT * FROM commercial_residential_design_images WHERE project_id = ? ORDER BY is_main DESC, sort_order ASC");
        $stmt->execute([$project['id']]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get main image
        $main_image = '';
        foreach ($images as $img) {
            if ($img['is_main'] == 1) {
                $main_image = $img['image_path'];
                break;
            }
        }
        if (empty($main_image) && !empty($images)) {
            $main_image = $images[0]['image_path'];
        }
        
        $project['main_image'] = $main_image;
    }
    
    echo json_encode([
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
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error loading projects: ' . $e->getMessage()]);
}
?>


