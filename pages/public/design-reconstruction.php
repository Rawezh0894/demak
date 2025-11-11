<?php
session_start();
require_once '../../config/db_conected.php';
require_once '../../includes/translations.php';

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];

// Load design & reconstruction categories from database
$design_reconstruction_categories = [];

try {
    $categories_stmt = $pdo->query("
        SELECT * FROM design_reconstruction_categories 
        WHERE is_active = 1 
        ORDER BY sort_order ASC, id ASC
    ");
    $categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Build categories array by category_key
    foreach ($categories as $category) {
        $design_reconstruction_categories[$category['category_key']] = [
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
    error_log("Error loading design reconstruction categories: " . $e->getMessage());
    // Fallback to empty categories array
    $design_reconstruction_categories = [];
}

// Load projects from database
try {
    $stmt = $pdo->query("
        SELECT 
            p.*,
            c.title as category_name,
            c.category_key as category_key
        FROM design_reconstruction_projects p
        LEFT JOIN design_reconstruction_categories c ON p.category_key = c.category_key
        WHERE p.is_active = 1
        ORDER BY p.sort_order ASC, p.created_at DESC
    ");
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get images for each project
    foreach ($projects as &$project) {
        $images_stmt = $pdo->prepare("SELECT * FROM design_reconstruction_images WHERE project_id = ? ORDER BY is_main DESC, id ASC");
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
        $features_stmt = $pdo->prepare("SELECT feature_text FROM design_reconstruction_features WHERE project_id = ?");
        $features_stmt->execute([$project['id']]);
        $features = $features_stmt->fetchAll(PDO::FETCH_COLUMN);
        $project['features'] = $features;
        
        // Add project to appropriate category
        if (isset($design_reconstruction_categories[$project['category_key']])) {
            $design_reconstruction_categories[$project['category_key']]['projects'][] = $project;
        }
    }
} catch (Exception $e) {
    error_log("Error loading design reconstruction projects: " . $e->getMessage());
    // If there's an error, ensure all categories have empty projects arrays
    foreach ($design_reconstruction_categories as &$category) {
        $category['projects'] = [];
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دیزاین و دووبارە دروستکردنەوە - <?php echo t('construction_company'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
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
    <meta name="description" content="دیزاین و دووبارە دروستکردنەوە - <?php echo t('construction_company'); ?>">
    <meta name="keywords" content="design, reconstruction, commercial, villa, house, school, kurdistan">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="دیزاین و دووبارە دروستکردنەوە">
    <meta property="og:description" content="خزمەتگوزاری دیزاین و دووبارە دروستکردنەوە بۆ بینا بازرگانی، باڵەخانە، خانوو و قوتابخانە">
    <meta property="og:type" content="website">
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Include Floating Contact -->
    <?php include '../../includes/floating-contact.php'; ?>
    <!-- Back to Home Button -->
    <a href="../../index.php" class="back-to-home-btn">
        <i class="fas fa-arrow-<?php echo $page_dir === 'rtl' ? 'right' : 'left'; ?>"></i>
        <span><?php echo t('back_to_home'); ?></span>
    </a>
    
    <!-- Dark Mode Toggle Button -->
    <button onclick="toggleDarkMode()" class="dark-mode-toggle-btn">
        <i id="lightModeIcon" class="fas fa-sun"></i>
        <i id="darkModeIcon" class="fas fa-moon hidden"></i>
    </button>
    
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
                        <?php echo t('design_reconstruction'); ?>
                    </h1>
                    <p class="hero-subtitle">
                        <?php echo t('design_dreams_reality'); ?>
                    </p>
                    
                    <!-- Quick Navigation -->
                    <div class="hero-categories-nav">
                        <?php foreach ($design_reconstruction_categories as $key => $category): ?>
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

        <!-- Categories Sections -->
        <?php foreach ($design_reconstruction_categories as $key => $category): ?>
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
                            <div class="project-slide" data-project-id="<?php echo $project['id']; ?>">
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
                                        <p id="modalPrice">$0</p>
                                    </div>
                                </div>
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="info-content">
                                        <h4><?php echo t('duration'); ?></h4>
                                        <p id="modalDuration">0 <?php echo t('days'); ?></p>
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
                                <div class="info-card">
                                    <div class="info-icon">
                                        <i class="fas fa-user-tie"></i>
                                    </div>
                                    <div class="info-content">
                                        <h4><?php echo t('engineer'); ?></h4>
                                        <p id="modalEngineer">-</p>
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
    <script src="../../assets/js/commercial_residential_design/responsive-slider.js"></script>
    <script src="../../assets/js/design-reconstruction/design-reconstruction-public.js"></script>
    
    <!-- Pass projects data to JavaScript -->
    <script>
        // Prepare projects data for JavaScript
        const projectsDataForJS = <?php echo json_encode($design_reconstruction_categories); ?>;
        
        // Call setProjectsData when DOM is ready
        if (typeof setProjectsData === 'function') {
            setProjectsData(projectsDataForJS);
        } else {
            // If function not loaded yet, wait for it
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof setProjectsData === 'function') {
                    setProjectsData(projectsDataForJS);
                }
            });
        }
    </script>
    
    <!-- Dark Mode Toggle Script -->
    <script>
        // Dark mode toggle function
        function toggleDarkMode() {
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
                localStorage.setItem('darkMode', 'false');
            } else {
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
                localStorage.setItem('darkMode', 'true');
            }
        }
        
        // Initialize dark mode from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode');
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            
            if (darkMode === 'true') {
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            } else {
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            }
        });
    </script>
</body>
</html>

