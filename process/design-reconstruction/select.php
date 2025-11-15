<?php
/**
 * Design Reconstruction Projects Select Operations
 * 
 * This file handles fetching design reconstruction projects from the database
 */

// Pagination settings
$items_per_page = 10; // 10 projects per page
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

$design_reconstruction_projects = [];
$total_projects = 0;
$total_pages = 0;

try {
    // Get total count (all projects for admin panel, regardless of is_active)
    $count_stmt = $pdo->query("
        SELECT COUNT(*) 
        FROM design_reconstruction_projects drp
    ");
    $total_projects = $count_stmt->fetchColumn();
    $total_pages = ceil($total_projects / $items_per_page);
    
    // Get paginated projects (all projects for admin panel, regardless of is_active)
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
        ORDER BY drp.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$items_per_page, $offset]);
    $design_reconstruction_projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debug: Log if no projects found
    if (empty($design_reconstruction_projects) && $total_projects > 0) {
        error_log("Warning: Total projects count is {$total_projects} but no projects returned for page {$current_page}");
    }
    
    // Process projects to include additional data
    foreach ($design_reconstruction_projects as &$project) {
        try {
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
        
        // Category key is already available from the database query
            // Ensure category_title is set even if category doesn't exist
            if (empty($project['category_title']) && !empty($project['category_key'])) {
                $project['category_title'] = $project['category_key'];
            }
        } catch (Exception $e) {
            error_log("Error processing project {$project['id']}: " . $e->getMessage());
            // Set default values if processing fails
            $project['main_image'] = null;
            $project['images'] = [];
            $project['features'] = [];
            if (empty($project['category_title']) && !empty($project['category_key'])) {
                $project['category_title'] = $project['category_key'];
            }
        }
    }
    
} catch (Exception $e) {
    error_log("Error fetching design reconstruction projects: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    // Don't reset variables if they were already set - keep what we have
    if (!isset($design_reconstruction_projects) || empty($design_reconstruction_projects)) {
    $design_reconstruction_projects = [];
    }
    if (!isset($total_projects)) {
    $total_projects = 0;
    }
    if (!isset($total_pages)) {
    $total_pages = 0;
    }
}

// Load design reconstruction categories for the form
$design_reconstruction_categories = [
    'commercial' => [
        'title' => 'Commercial Buildings',
        'title_ku' => 'بینای بازرگانی',
        'title_ar' => 'المباني التجارية',
        'title_en' => 'Commercial Buildings',
        'icon' => 'fas fa-building',
        'color' => '#3b82f6',
        'description' => 'Commercial building design and reconstruction services'
    ],
    'villa' => [
        'title' => 'Villas',
        'title_ku' => 'باڵەخانە',
        'title_ar' => 'الفيلات',
        'title_en' => 'Villas',
        'icon' => 'fas fa-home',
        'color' => '#10b981',
        'description' => 'Luxury villa design and reconstruction services'
    ],
    'house' => [
        'title' => 'Houses',
        'title_ku' => 'خانوو',
        'title_ar' => 'المنازل',
        'title_en' => 'Houses',
        'icon' => 'fas fa-house-user',
        'color' => '#f59e0b',
        'description' => 'Residential house design and reconstruction services'
    ],
    'school' => [
        'title' => 'Schools',
        'title_ku' => 'قوتابخانە',
        'title_ar' => 'المدارس',
        'title_en' => 'Schools',
        'icon' => 'fas fa-school',
        'color' => '#8b5cf6',
        'description' => 'Educational facility design and reconstruction services'
    ]
];
?>
