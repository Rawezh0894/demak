<?php
// Select and Load Data Functionality

// Pagination settings
$items_per_page = 10; // 10 projects per page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Load infrastructure projects from database with pagination
$infrastructure_projects = [];
$total_projects = 0;
$total_pages = 0;

try {
    // Get total count
    $count_stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM infrastructure_projects ip
        WHERE ip.is_active = 1
    ");
    $total_projects = $count_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $items_per_page);
    
    // Get paginated projects
    $stmt = $pdo->prepare("
        SELECT ip.*, ic.title as category_title, ic.title_ku as category_title_ku, ic.title_ar as category_title_ar,
               ic.icon as category_icon, ic.color as category_color
        FROM infrastructure_projects ip
        LEFT JOIN infrastructure_categories ic ON ip.category_key = ic.key
        WHERE ip.is_active = 1
        ORDER BY ip.sort_order ASC, ip.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$items_per_page, $offset]);
    $infrastructure_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $error_message = "هەڵەیەک ڕوویدا لە بارکردنی پڕۆژەکان: " . $e->getMessage();
    $total_projects = 0;
    $total_pages = 0;
}

// Load infrastructure categories
$infrastructure_categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM infrastructure_categories WHERE is_active = 1 ORDER BY sort_order ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $category) {
        $infrastructure_categories[$category['key']] = $category;
    }
} catch (Exception $e) {
    // Fallback to static data if database tables don't exist yet
    $infrastructure_categories = require_once '../../config/infrastructure_data.php';
}

// Function to get project by ID
function getProjectById($pdo, $project_id, $admin_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT ip.*, ic.title as category_title, ic.title_ku as category_title_ku, ic.title_ar as category_title_ar,
                   ic.icon as category_icon, ic.color as category_color
            FROM infrastructure_projects ip
            LEFT JOIN infrastructure_categories ic ON ip.category_key = ic.key
            WHERE ip.id = ? AND ip.created_by = ? AND ip.is_active = 1
        ");
        $stmt->execute([$project_id, $admin_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($project) {
            // Load project features
            $stmt = $pdo->prepare("SELECT * FROM project_features WHERE project_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$project_id]);
            $project['features'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Load project images
            $stmt = $pdo->prepare("SELECT * FROM project_images WHERE project_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$project_id]);
            $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $project;
    } catch (Exception $e) {
        return null;
    }
}

// Function to get projects by category
function getProjectsByCategory($pdo, $category_key) {
    try {
        $stmt = $pdo->prepare("
            SELECT ip.*, ic.title as category_title, ic.title_ku as category_title_ku, ic.title_ar as category_title_ar,
                   ic.icon as category_icon, ic.color as category_color
            FROM infrastructure_projects ip
            LEFT JOIN infrastructure_categories ic ON ip.category_key = ic.key
            WHERE ip.category_key = ? AND ip.is_active = 1
            ORDER BY ip.sort_order ASC, ip.created_at DESC
        ");
        $stmt->execute([$category_key]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to search projects
function searchProjects($pdo, $search_term) {
    try {
        $stmt = $pdo->prepare("
            SELECT ip.*, ic.title as category_title, ic.title_ku as category_title_ku, ic.title_ar as category_title_ar,
                   ic.icon as category_icon, ic.color as category_color
            FROM infrastructure_projects ip
            LEFT JOIN infrastructure_categories ic ON ip.category_key = ic.key
            WHERE ip.is_active = 1 AND (
                ip.name LIKE ? OR 
                ip.description LIKE ? OR 
                ic.title LIKE ?
            )
            ORDER BY ip.sort_order ASC, ip.created_at DESC
        ");
        $search_pattern = '%' . $search_term . '%';
        $stmt->execute([$search_pattern, $search_pattern, $search_pattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to get project statistics
function getProjectStatistics($pdo) {
    try {
        $stats = [];
        
        // Total projects
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM infrastructure_projects WHERE is_active = 1");
        $stats['total_projects'] = $stmt->fetchColumn();
        
        // Projects by category
        $stmt = $pdo->query("
            SELECT ic.title, COUNT(ip.id) as count 
            FROM infrastructure_categories ic 
            LEFT JOIN infrastructure_projects ip ON ic.key = ip.category_key AND ip.is_active = 1
            WHERE ic.is_active = 1
            GROUP BY ic.key, ic.title
            ORDER BY count DESC
        ");
        $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recent projects
        $stmt = $pdo->query("
            SELECT COUNT(*) as recent 
            FROM infrastructure_projects 
            WHERE is_active = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stats['recent_projects'] = $stmt->fetchColumn();
        
        return $stats;
    } catch (Exception $e) {
        return [];
    }
}
?>
