<?php
// Select and Load Data Functionality for Interior Design

// Pagination settings
$items_per_page = 10; // 10 projects per page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Load interior design projects from database with pagination
$interior_design_projects = [];
$total_projects = 0;
$total_pages = 0;

try {
    // Get total count
    $count_stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM interior_design_projects
        WHERE is_active = 1
    ");
    $total_projects = $count_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $items_per_page);
    
    // Get paginated projects
    $stmt = $pdo->prepare("
        SELECT * FROM interior_design_projects
        WHERE is_active = 1
        ORDER BY sort_order ASC, created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$items_per_page, $offset]);
    $interior_design_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Load additional images for each project
    foreach ($interior_design_projects as &$project) {
        $stmt = $pdo->prepare("SELECT * FROM interior_design_images WHERE project_id = ? ORDER BY sort_order ASC");
        $stmt->execute([$project['id']]);
        $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    unset($project);
} catch (Exception $e) {
    $error_message = "هەڵەیەک ڕوویدا لە بارکردنی پڕۆژەکان: " . $e->getMessage();
    $total_projects = 0;
    $total_pages = 0;
}

// Function to get project by ID
function getInteriorProjectById($pdo, $project_id, $admin_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM interior_design_projects
            WHERE id = ? AND created_by = ? AND is_active = 1
        ");
        $stmt->execute([$project_id, $admin_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($project) {
            // Load project images
            $stmt = $pdo->prepare("SELECT * FROM interior_design_images WHERE project_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$project_id]);
            $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $project;
    } catch (Exception $e) {
        return null;
    }
}

// Function to search projects
function searchInteriorProjects($pdo, $search_term) {
    try {
        $stmt = $pdo->prepare("
            SELECT * FROM interior_design_projects
            WHERE is_active = 1 AND (
                name LIKE ? OR 
                description LIKE ?
            )
            ORDER BY sort_order ASC, created_at DESC
        ");
        $search_pattern = '%' . $search_term . '%';
        $stmt->execute([$search_pattern, $search_pattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to get project statistics
function getInteriorProjectStatistics($pdo) {
    try {
        $stats = [];
        
        // Total projects
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM interior_design_projects WHERE is_active = 1");
        $stats['total_projects'] = $stmt->fetchColumn();
        
        // Recent projects
        $stmt = $pdo->query("
            SELECT COUNT(*) as recent 
            FROM interior_design_projects 
            WHERE is_active = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stats['recent_projects'] = $stmt->fetchColumn();
        
        return $stats;
    } catch (Exception $e) {
        return [];
    }
}
?>
