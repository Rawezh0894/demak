<?php
session_start();
require_once '../../config/db_conected.php';
require_once '../../includes/translations.php';

// Track website visitors
try {
    require_once '../../includes/visitor_tracker.php';
} catch (Exception $e) {
    error_log("Visitor tracking error: " . $e->getMessage());
}

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];

// Load commercial residential design categories from database
$commercial_residential_categories = [];

try {
    $categories_stmt = $pdo->query("
        SELECT * FROM commercial_residential_design_categories 
        WHERE is_active = 1 
        ORDER BY sort_order ASC, id ASC
    ");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build categories array by category_key
    foreach ($categories as $category) {
        $commercial_residential_categories[$category['category_key']] = [
            'title' => $category['title_' . $current_lang] ?? $category['title'],
            'title_ku' => $category['title_ku'],
            'title_ar' => $category['title_ar'],
            'icon' => $category['icon'],
            'color' => $category['color'],
            'description' => $category['description_' . $current_lang] ?? $category['description'],
            'description_ku' => $category['description_ku'],
            'description_ar' => $category['description_ar'],
            'projects' => []
        ];
    }
} catch (Exception $e) {
    error_log("Error loading commercial residential design categories: " . $e->getMessage());
    $commercial_residential_categories = [];
}

// Load projects from database
try {
    $stmt = $pdo->query("
        SELECT 
            crp.*,
            crc.title as category_title,
            crc.category_key as category_key,
            crc.icon as category_icon,
            crc.color as category_color
        FROM commercial_residential_design_projects crp
        LEFT JOIN commercial_residential_design_categories crc ON crp.category_key = crc.category_key
        WHERE crp.is_active = 1
        ORDER BY crp.sort_order ASC, crp.created_at DESC
    ");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get images and features for each project
    foreach ($projects as &$project) {
        $images_stmt = $pdo->prepare("SELECT * FROM commercial_residential_design_images WHERE project_id = ? ORDER BY is_main DESC, sort_order ASC");
        $images_stmt->execute([$project['id']]);
        $images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Set main image
        $project['image'] = null;
        $project['images'] = [];
        
        foreach ($images as $image) {
            // Fix image path relative to pages/public/ directory
            $imagePath = $image['image_path'];
            if (!preg_match('/^(https?:\/\/|\/)/', $imagePath)) {
                // Relative path - prepend ../../
                $imagePath = '../../' . ltrim($imagePath, '/\\');
            }
            
            if ($image['is_main']) {
                $project['image'] = $imagePath;
            } else {
                $project['images'][] = $imagePath;
            }
        }
        
        // Get features
        $features_stmt = $pdo->prepare("SELECT feature_text FROM commercial_residential_design_features WHERE project_id = ? ORDER BY sort_order ASC");
        $features_stmt->execute([$project['id']]);
        $features = $features_stmt->fetchAll(PDO::FETCH_COLUMN);
        $project['features'] = $features;
        
        // Add project to appropriate category
        if (isset($commercial_residential_categories[$project['category_key']])) {
            $commercial_residential_categories[$project['category_key']]['projects'][] = $project;
        }
    }
} catch (Exception $e) {
    error_log("Error loading commercial residential design projects: " . $e->getMessage());
    foreach ($commercial_residential_categories as &$category) {
        $category['projects'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دیزاین و سەرپەرشتی کردنی بینای بازرگانی و ڤێلا و خانوو - <?php echo t('construction_company'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'rabar': ['Rabar', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/design-reconstruction.css">
    <link rel="stylesheet" href="../../assets/css/responsive-slider.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="دیزاین و سەرپەرشتی کردنی بینای بازرگانی و ڤێلا و خانوو - <?php echo t('construction_company'); ?>">
    <meta name="keywords" content="commercial buildings, villas, houses, design, kurdistan">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="دیزاین و سەرپەرشتی کردنی بینای بازرگانی و ڤێلا و خانوو">
    <meta property="og:description" content="خزمەتگوزاری دیزاین و سەرپەرشتی کردنی بینای بازرگانی و ڤێلا و خانوو">
    <meta property="og:type" content="website">
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Include Navbar -->
    <?php include '../../includes/navbar.php'; ?>
    
    <!-- Include Sidebar -->
    <?php include '../../includes/sidebar.php'; ?>
    
    <!-- Include Floating Contact -->
    <?php include '../../includes/floating-contact.php'; ?>
    
    <!-- Main Content -->
    <div class="main-content-full">
        <!-- Hero Section -->
        <section class="design-hero">
            <!-- Animated Floating Shapes -->
            <div class="floating-shapes">
                <div class="shape shape-circle shape-1"></div>
                <div class="shape shape-square shape-2"></div>
                <div class="shape shape-triangle shape-3"></div>
                <div class="shape shape-circle shape-4"></div>
                <div class="shape shape-square shape-5"></div>
                <div class="shape shape-hexagon shape-6"></div>
                <div class="shape shape-circle shape-7"></div>
                <div class="shape shape-triangle shape-8"></div>
            </div>
            
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h1 class="hero-title">
                        دیزاین و سەرپەرشتی کردنی بینای بازرگانی و ڤێلا و خانوو
                    </h1>
                    <p class="hero-subtitle">
                        خەونەکانت دەکەینە ڕاستی بە دیزاینی جوان و کارامە
                    </p>
                    
                    <!-- Quick Navigation -->
                    <div class="hero-categories-nav">
                        <?php foreach ($commercial_residential_categories as $key => $category): ?>
                        <a href="#<?php echo $key; ?>-section" class="hero-category-link" style="--category-color: <?php echo $category['color']; ?>">
                            <div class="hero-category-icon">
                                <i class="<?php echo $category['icon']; ?>"></i>
                            </div>
                            <span class="hero-category-name"><?php echo $category['title_' . $current_lang] ?? $category['title']; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- Search and Filter Section -->
        <section class="search-filter-section bg-white dark:bg-gray-800 py-12">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                <div class="max-w-6xl mx-auto">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-2xl p-8 shadow-lg">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                            <i class="fas fa-search mr-3 text-blue-600"></i>
                            گەڕان و پاڵاوتن
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Search by Name -->
                            <div class="lg:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-search mr-2"></i>
                                    گەڕان بە ناوی پڕۆژە
                                </label>
                                <input type="text" 
                                       id="searchInput"
                                       placeholder="ناوی پڕۆژە بنووسە..."
                                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            
                            <!-- Filter by Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-filter mr-2"></i>
                                    پۆل
                                </label>
                                <select id="categoryFilter"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">هەموو پۆلەکان</option>
                                    <?php foreach ($commercial_residential_categories as $key => $category): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $category['title_' . $current_lang] ?? $category['title']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <!-- Filter by Area Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-ruler-combined mr-2"></i>
                                    ڕووبەر (م²)
                                </label>
                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           id="areaMin"
                                           placeholder="کەمترین"
                                           min="0"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                    <input type="number" 
                                           id="areaMax"
                                           placeholder="زۆرترین"
                                           min="0"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                            <!-- Filter by Floors Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-layer-group mr-2"></i>
                                    نهۆم
                                </label>
                                <div class="flex items-center space-x-2">
                                    <input type="number" 
                                           id="floorsMin"
                                           placeholder="کەمترین"
                                           min="0"
                                           max="100"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">-</span>
                                    <input type="number" 
                                           id="floorsMax"
                                           placeholder="زۆرترین"
                                           min="0"
                                           max="100"
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                                </div>
                            </div>
                            
                            <!-- Clear Filters Button -->
                            <div class="flex items-end">
                                <button id="clearFiltersBtn" 
                                        class="w-full bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-white px-6 py-3 rounded-xl font-medium transition-all duration-200">
                                    <i class="fas fa-times mr-2"></i>
                                    پاککردنەوەی فلتەرەکان
                                </button>
                            </div>
                        </div>
                        
                        <!-- Results Count -->
                        <div class="mt-6 text-center">
                            <p class="text-gray-600 dark:text-gray-400">
                                <span id="resultsCount">0</span> پڕۆژە دۆزرایەوە
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Sections -->
        <?php foreach ($commercial_residential_categories as $key => $category): ?>
        <section class="category-section" id="<?php echo $key; ?>-section" data-category="<?php echo $key; ?>">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <!-- Category Header -->
                <div class="category-section-header">
                    <div class="category-icon-large" style="background: <?php echo $category['color']; ?>">
                        <i class="<?php echo $category['icon']; ?>"></i>
                    </div>
                    <div class="category-header-text">
                        <h2 class="category-section-title"><?php echo $category['title_' . $current_lang] ?? $category['title']; ?></h2>
                        <p class="category-section-description"><?php echo $category['description_' . $current_lang] ?? $category['description']; ?></p>
                    </div>
                </div>

                <!-- Projects Slider -->
                <?php if (isset($category['projects']) && is_array($category['projects']) && !empty($category['projects'])): ?>

                <!-- Tab Navigation -->
                <div class="tabs-nav-container" id="tabs-<?php echo $key; ?>">
                    <?php foreach ($category['projects'] as $index => $project): ?>
                        <?php 
                            $projectTitle = $project['name_' . $current_lang] ?? $project['name'] ?? '';
                        ?>
                        <button class="tab-button <?php echo $index === 0 ? 'active' : ''; ?>"
                                onclick="showTab('<?php echo $key; ?>', <?php echo $index; ?>)"
                                data-tab-index="<?php echo $index; ?>">
                            <span class="tab-button-icon">
                                <i class="fas fa-building"></i>
                            </span>
                            <span class="tab-button-text"><?php echo mb_substr($projectTitle, 0, 30); ?></span>
                            <?php if (mb_strlen($projectTitle) > 30): ?>
                                <span class="tab-button-ellipsis">...</span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <!-- Projects Slider -->
                <div class="projects-slider-container">
                    <div class="projects-slider-wrapper">
                        <div class="slide-counter" id="counter-<?php echo $key; ?>">
                            <span class="current-slide">1</span>
                            <span class="slide-separator">/</span>
                            <span class="total-slides"><?php echo count($category['projects']); ?></span>
                        </div>

                        <div class="projects-slider" id="slider-<?php echo $key; ?>">
                            <?php foreach ($category['projects'] as $index => $project): ?>
                            <?php
                                $projectName = $project['name_' . $current_lang] ?? $project['name'] ?? '';
                                $projectDescription = $project['description_' . $current_lang] ?? $project['description'] ?? '';
                                $projectPrice = $project['price'] ?? '-';
                                $projectDuration = $project['duration'] ?? '-';
                                $projectArea = $project['area'] ?? '-';
                                $projectFloors = $project['floors'] ?? '-';

                                $galleryImages = [];
                                if (!empty($project['image'])) {
                                    $galleryImages[] = $project['image'];
                                }
                                if (isset($project['images']) && is_array($project['images'])) {
                                    foreach ($project['images'] as $imgPath) {
                                        if (!empty($imgPath) && $imgPath !== $project['image']) {
                                            $galleryImages[] = $imgPath;
                                        }
                                    }
                                }
                                if (empty($galleryImages)) {
                                    $galleryImages[] = 'https://via.placeholder.com/1200x800?text=No+Image';
                                }
                                $totalGalleryImages = count($galleryImages);
                            ?>
                            <div class="project-slide" 
                                 data-project-id="<?php echo $project['id']; ?>"
                                 data-category="<?php echo htmlspecialchars($project['category_key'], ENT_QUOTES, 'UTF-8'); ?>"
                                 data-name="<?php echo htmlspecialchars(strtolower($projectName), ENT_QUOTES, 'UTF-8'); ?>"
                                 data-area="<?php echo htmlspecialchars(preg_replace('/[^\d.,]/', '', $projectArea), ENT_QUOTES, 'UTF-8'); ?>"
                                 data-floors="<?php echo htmlspecialchars($projectFloors, ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="project-slide-image">
                                    <div class="project-image-gallery">
                                        <div class="main-image-container">
                                            <img src="<?php echo htmlspecialchars($galleryImages[0], ENT_QUOTES, 'UTF-8'); ?>"
                                                 alt="<?php echo htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8'); ?>"
                                                 loading="lazy"
                                                 decoding="async"
                                                 class="project-image main-image">
                                            <div class="image-counter">
                                                <span class="current-image">1</span>
                                                <span class="image-separator">/</span>
                                                <span class="total-images"><?php echo $totalGalleryImages; ?></span>
                                            </div>
                                        </div>
                                        <?php if ($totalGalleryImages > 1): ?>
                                            <div class="image-thumbnails" id="thumbnails-<?php echo $project['id']; ?>">
                                                <?php foreach ($galleryImages as $imgIndex => $imagePath): ?>
                                                    <div class="thumbnail-item <?php echo $imgIndex === 0 ? 'active' : ''; ?>"
                                                         onclick="changeMainImage(<?php echo $project['id']; ?>, <?php echo $imgIndex; ?>)">
                                                        <img src="<?php echo htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>"
                                                             alt="<?php echo htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8'); ?> - <?php echo $imgIndex + 1; ?>"
                                                             loading="lazy"
                                                             class="thumbnail-image">
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="project-slide-overlay">
                                        <button class="view-details-btn" onclick="showProjectDetails(<?php echo $project['id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                            <?php echo t('view_details'); ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="project-slide-content">
                                    <h3 class="project-slide-title"><?php echo htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8'); ?></h3>
                                    <div class="project-info-badges">
                                        <div class="info-badge badge-price">
                                            <div class="badge-icon"><i class="fas fa-dollar-sign"></i></div>
                                            <div class="badge-text">
                                                <span class="badge-label"><?php echo t('price'); ?></span>
                                                <span class="badge-value"><?php echo htmlspecialchars($projectPrice, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </div>
                                        <div class="info-badge badge-duration">
                                            <div class="badge-icon"><i class="fas fa-clock"></i></div>
                                            <div class="badge-text">
                                                <span class="badge-label"><?php echo t('duration'); ?></span>
                                                <span class="badge-value"><?php echo htmlspecialchars($projectDuration, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </div>
                                        <div class="info-badge badge-area">
                                            <div class="badge-icon"><i class="fas fa-ruler-combined"></i></div>
                                            <div class="badge-text">
                                                <span class="badge-label">ڕووبەر</span>
                                                <span class="badge-value"><?php echo htmlspecialchars($projectArea, ENT_QUOTES, 'UTF-8'); ?> م²</span>
                                            </div>
                                        </div>
                                        <div class="info-badge badge-floors">
                                            <div class="badge-icon"><i class="fas fa-layer-group"></i></div>
                                            <div class="badge-text">
                                                <span class="badge-label">نهۆم</span>
                                                <span class="badge-value"><?php echo htmlspecialchars($projectFloors, ENT_QUOTES, 'UTF-8'); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="info-badge badge-about">
                                        <div class="badge-icon"><i class="fas fa-info-circle"></i></div>
                                        <div class="badge-text">
                                            <span class="badge-label"><?php echo t('about'); ?></span>
                                            <?php if (isset($project['features']) && is_array($project['features']) && count($project['features']) > 0): ?>
                                                <p class="badge-description"><?php echo htmlspecialchars(implode('. ', array_slice($project['features'], 0, 4)) . '.', ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php else: ?>
                                                <p class="badge-description"><?php echo htmlspecialchars($projectDescription, ENT_QUOTES, 'UTF-8'); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="slider-arrow slider-prev" onclick="prevSlide('<?php echo $key; ?>')">
                        <i class="fas fa-chevron-<?php echo $page_dir === 'rtl' ? 'right' : 'left'; ?>"></i>
                    </button>

                    <button class="slider-arrow slider-next" onclick="nextSlide('<?php echo $key; ?>')">
                        <i class="fas fa-chevron-<?php echo $page_dir === 'rtl' ? 'left' : 'right'; ?>"></i>
                    </button>

                    <div class="slider-dots" id="dots-<?php echo $key; ?>"></div>
                </div>
                <?php else: ?>
                <!-- No Projects Message -->
                <div class="no-projects-message">
                    <div class="no-projects-icon">
                        <i class="<?php echo $category['icon']; ?>" style="color: <?php echo $category['color']; ?>"></i>
                    </div>
                    <h3 class="no-projects-title"><?php echo t('no_projects_available'); ?></h3>
                    <p class="no-projects-description"><?php echo t('no_projects_description'); ?></p>
                    <div class="no-projects-cta">
                        <a href="#contact-section" class="btn btn-primary">
                            <i class="fas fa-envelope"></i>
                            <?php echo t('contact_us_for_custom_project'); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </section>
        <?php endforeach; ?>

        <!-- Back to Top Button -->
        <button id="backToTop" class="back-to-top" onclick="scrollToTop()">
            <i class="fas fa-arrow-up"></i>
        </button>

        <!-- Image Zoom Modal -->
        <div id="imageZoomModal" class="image-zoom-modal">
            <div class="zoom-modal-overlay" onclick="closeImageZoom()"></div>
            <div class="zoom-modal-content">
                <button class="zoom-modal-close" onclick="closeImageZoom()">
                    <i class="fas fa-times"></i>
                </button>
                <div class="zoom-image-container">
                    <img id="zoomImage" src="" alt="Zoomed Image" class="zoom-image">
                </div>
                <div class="zoom-controls">
                    <button class="zoom-btn zoom-out" onclick="zoomOut()">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button class="zoom-btn zoom-reset" onclick="resetZoom()">
                        <i class="fas fa-expand"></i>
                    </button>
                    <button class="zoom-btn zoom-in" onclick="zoomIn()">
                        <i class="fas fa-search-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Project Details Modal -->
        <div id="projectModal" class="project-modal">
            <div class="modal-overlay" onclick="closeProjectModal()"></div>
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title" id="modalTitle"><?php echo t('project_details_title'); ?></h2>
                    <button class="modal-close" onclick="closeProjectModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="project-details-container">
                        <div class="project-image-section">
                            <div class="modal-image-gallery">
                                <div class="modal-main-image-container">
                                    <img id="modalImage" src="" alt="Project" class="main-project-image">
                                    <div class="modal-image-counter">
                                        <span class="modal-current-image">1</span>
                                        <span class="modal-image-separator">/</span>
                                        <span class="modal-total-images">1</span>
                                    </div>
                                    <button class="modal-gallery-toggle-btn" onclick="toggleModalImageGallery()">
                                        <i class="fas fa-images"></i>
                                    </button>
                                    <button class="modal-image-prev" onclick="prevModalImage()">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="modal-image-next" onclick="nextModalImage()">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <div class="modal-image-thumbnails" id="modalThumbnails" style="display: none;">
                                    <!-- Thumbnails will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                        <div class="project-info-section">
                            <div class="project-info-grid">
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div class="info-content">
                                        <h4><?php echo t('price'); ?></h4>
                                        <p id="modalPrice">-</p>
                                    </div>
                                </div>
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="info-content">
                                        <h4><?php echo t('duration'); ?></h4>
                                        <p id="modalDuration">-</p>
                                    </div>
                                </div>
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-ruler-combined"></i>
                                    </div>
                                    <div class="info-content">
                                        <h4>ڕووبەر</h4>
                                        <p id="modalArea">-</p>
                                    </div>
                                </div>
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div class="info-content">
                                        <h4>نهۆم</h4>
                                        <p id="modalFloors">-</p>
                                    </div>
                                </div>
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="info-content">
                                        <h4><?php echo t('project_type'); ?></h4>
                                        <p id="modalType">-</p>
                                    </div>
                                </div>
                            </div>
                            <div class="project-description">
                                <h3><?php echo t('full_description'); ?></h3>
                                <p id="modalDescription">-</p>
                            </div>
                            <div class="project-features">
                                <h3><?php echo t('key_features'); ?></h3>
                                <ul id="modalFeatures">
                                    <!-- Features will be populated by JavaScript -->
                                </ul>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="requestQuote()">
                                    <i class="fas fa-calculator"></i>
                                    <?php echo t('request_quote'); ?>
                                </button>
                                <button class="btn btn-secondary" onclick="contactUs()">
                                    <i class="fas fa-phone"></i>
                                    <?php echo t('contact_us'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="../../assets/js/design-reconstruction/design-reconstruction-public.js"></script>
    <script src="../../assets/js/commercial_residential_design/responsive-slider.js"></script>
    <script src="../../assets/js/commercial_residential_design/commercial_residential_design_public.js"></script>
    
    <!-- Pass projects data to JavaScript -->
    <script>
        // Prepare projects data for JavaScript
        const projectsDataForJS = <?php echo json_encode($commercial_residential_categories, JSON_UNESCAPED_UNICODE); ?>;
        
        // Initialize when script is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof initializeCommercialResidentialDesign === 'function') {
                    initializeCommercialResidentialDesign(projectsDataForJS);
                }
            });
        } else {
            if (typeof initializeCommercialResidentialDesign === 'function') {
                initializeCommercialResidentialDesign(projectsDataForJS);
            }
        }
    </script>
</body>
</html>

