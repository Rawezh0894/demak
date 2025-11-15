<?php
// Select and Load Data Functionality for Commercial & Residential Design

// Pagination settings
$items_per_page = 10; // 10 projects per page
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
    
    if (!empty($categories)) {
        foreach ($categories as $category) {
            $commercial_residential_categories[$category['category_key']] = $category;
        }
        error_log("✅ Loaded " . count($commercial_residential_categories) . " categories from database");
    } else {
        error_log("⚠️ No categories found in database, using default categories");
        // If no categories in database, use default categories
        $commercial_residential_categories = [
            'commercial' => [
                'category_key' => 'commercial',
                'title' => 'Commercial Buildings',
                'title_ku' => 'بینای بازرگانی',
                'title_ar' => 'المباني التجارية',
                'icon' => 'fas fa-building',
                'color' => '#3b82f6',
                'description' => 'Commercial building design and management services',
                'description_ku' => 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی بینای بازرگانی',
                'description_ar' => 'خدمات التصميم والإدارة للمباني التجارية',
                'sort_order' => 1,
                'is_active' => 1
            ],
            'villa' => [
                'category_key' => 'villa',
                'title' => 'Villas',
                'title_ku' => 'باڵەخانە',
                'title_ar' => 'الفيلات',
                'icon' => 'fas fa-home',
                'color' => '#10b981',
                'description' => 'Luxury villa design and management services',
                'description_ku' => 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی باڵەخانە لۆکس',
                'description_ar' => 'خدمات التصميم والإدارة للفيلات الفاخرة',
                'sort_order' => 2,
                'is_active' => 1
            ],
            'house' => [
                'category_key' => 'house',
                'title' => 'Houses',
                'title_ku' => 'خانوو',
                'title_ar' => 'المنازل',
                'icon' => 'fas fa-house-user',
                'color' => '#f59e0b',
                'description' => 'Residential house design and management services',
                'description_ku' => 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی خانوو',
                'description_ar' => 'خدمات التصميم والإدارة للمنازل السكنية',
                'sort_order' => 3,
                'is_active' => 1
            ]
        ];
    }
} catch (Exception $e) {
    error_log("Error loading commercial residential design categories: " . $e->getMessage());
    // Table might not exist yet, use default categories
    $commercial_residential_categories = [
        'commercial' => [
            'category_key' => 'commercial',
            'title' => 'Commercial Buildings',
            'title_ku' => 'بینای بازرگانی',
            'title_ar' => 'المباني التجارية',
            'icon' => 'fas fa-building',
            'color' => '#3b82f6',
            'description' => 'Commercial building design and management services',
            'description_ku' => 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی بینای بازرگانی',
            'description_ar' => 'خدمات التصميم والإدارة للمباني التجارية',
            'sort_order' => 1,
            'is_active' => 1
        ],
        'villa' => [
            'category_key' => 'villa',
            'title' => 'Villas',
            'title_ku' => 'باڵەخانە',
            'title_ar' => 'الفيلات',
            'icon' => 'fas fa-home',
            'color' => '#10b981',
            'description' => 'Luxury villa design and management services',
            'description_ku' => 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی باڵەخانە لۆکس',
            'description_ar' => 'خدمات التصميم والإدارة للفيلات الفاخرة',
            'sort_order' => 2,
            'is_active' => 1
        ],
        'house' => [
            'category_key' => 'house',
            'title' => 'Houses',
            'title_ku' => 'خانوو',
            'title_ar' => 'المنازل',
            'icon' => 'fas fa-house-user',
            'color' => '#f59e0b',
            'description' => 'Residential house design and management services',
            'description_ku' => 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی خانوو',
            'description_ar' => 'خدمات التصميم والإدارة للمنازل السكنية',
            'sort_order' => 3,
            'is_active' => 1
        ]
    ];
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

