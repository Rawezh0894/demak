<?php
// Select and Load Data Functionality for Commercial & Residential Design

// Pagination settings
$items_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

// Load projects from database with pagination
$commercial_residential_projects = [];
$total_projects = 0;
$total_pages = 0;

try {
    // Get total count
    $count_stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM commercial_residential_design_projects crp
        WHERE crp.is_active = 1
    ");
    $total_projects = $count_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $items_per_page);
    
    // Get paginated projects
    $stmt = $pdo->prepare("
        SELECT crp.*, crc.title as category_title, crc.title_ku as category_title_ku, crc.title_ar as category_title_ar,
               crc.icon as category_icon, crc.color as category_color
        FROM commercial_residential_design_projects crp
        LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
        WHERE crp.is_active = 1
        ORDER BY crp.sort_order ASC, crp.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$items_per_page, $offset]);
    $commercial_residential_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Load images for each project
    foreach ($commercial_residential_projects as &$project) {
        $stmt = $pdo->prepare("SELECT * FROM commercial_residential_design_images WHERE project_id = ? ORDER BY is_main DESC, sort_order ASC");
        $stmt->execute([$project['id']]);
        $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    // Table might not exist yet, set empty array
    $commercial_residential_projects = [];
    $total_projects = 0;
    $total_pages = 0;
}

// Load categories
$commercial_residential_categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM commercial_residential_design_categories WHERE is_active = 1 ORDER BY sort_order ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($categories as $category) {
        $commercial_residential_categories[$category['category_key']] = $category;
    }
} catch (Exception $e) {
    // Table might not exist yet, set empty array
    $commercial_residential_categories = [];
}

// Function to get project by ID
function getCommercialResidentialProjectById($pdo, $project_id, $admin_id) {
    try {
        $stmt = $pdo->prepare("
            SELECT crp.*, crc.title as category_title, crc.title_ku as category_title_ku, crc.title_ar as category_title_ar,
                   crc.icon as category_icon, crc.color as category_color
            FROM commercial_residential_design_projects crp
            LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
            WHERE crp.id = ? AND crp.created_by = ? AND crp.is_active = 1
        ");
        $stmt->execute([$project_id, $admin_id]);
        $project = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($project) {
            // Load project features
            $stmt = $pdo->prepare("SELECT * FROM commercial_residential_design_features WHERE project_id = ? ORDER BY sort_order ASC");
            $stmt->execute([$project_id]);
            $project['features'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Load project images
            $stmt = $pdo->prepare("SELECT * FROM commercial_residential_design_images WHERE project_id = ? ORDER BY is_main DESC, sort_order ASC");
            $stmt->execute([$project_id]);
            $project['images'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $project;
    } catch (Exception $e) {
        return null;
    }
}

// Function to get projects by category
function getCommercialResidentialProjectsByCategory($pdo, $category_key) {
    try {
        $stmt = $pdo->prepare("
            SELECT crp.*, crc.title as category_title, crc.title_ku as category_title_ku, crc.title_ar as category_title_ar,
                   crc.icon as category_icon, crc.color as category_color
            FROM commercial_residential_design_projects crp
            LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
            WHERE crp.category_key = ? AND crp.is_active = 1
            ORDER BY crp.sort_order ASC, crp.created_at DESC
        ");
        $stmt->execute([$category_key]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to search projects
function searchCommercialResidentialProjects($pdo, $search_term) {
    try {
        $stmt = $pdo->prepare("
            SELECT crp.*, crc.title as category_title, crc.title_ku as category_title_ku, crc.title_ar as category_title_ar,
                   crc.icon as category_icon, crc.color as category_color
            FROM commercial_residential_design_projects crp
            LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
            WHERE crp.is_active = 1 AND (
                crp.name LIKE ? OR 
                crp.description LIKE ? OR 
                crc.title LIKE ?
            )
            ORDER BY crp.sort_order ASC, crp.created_at DESC
        ");
        $search_pattern = '%' . $search_term . '%';
        $stmt->execute([$search_pattern, $search_pattern, $search_pattern]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to get project statistics
function getCommercialResidentialStatistics($pdo) {
    try {
        $stats = [];
        
        // Total projects
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM commercial_residential_design_projects WHERE is_active = 1");
        $stats['total_projects'] = $stmt->fetchColumn();
        
        // Projects by category
        $stmt = $pdo->query("
            SELECT crc.title, COUNT(crp.id) as count 
            FROM commercial_residential_design_categories crc 
            LEFT JOIN commercial_residential_design_projects crp ON crc.category_key = crp.category_key AND crp.is_active = 1
            WHERE crc.is_active = 1
            GROUP BY crc.category_key, crc.title
            ORDER BY count DESC
        ");
        $stats['by_category'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Recent projects
        $stmt = $pdo->query("
            SELECT COUNT(*) as recent 
            FROM commercial_residential_design_projects 
            WHERE is_active = 1 AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stats['recent_projects'] = $stmt->fetchColumn();
        
        return $stats;
    } catch (Exception $e) {
        return [];
    }
}
?>

