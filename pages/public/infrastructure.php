<?php
session_start();
require_once '../../config/db_conected.php';
require_once '../../includes/translations.php';
require_once '../../config/infrastructure_loader.php';

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];

// Load infrastructure categories and projects from database
$infrastructure_categories = loadInfrastructureData($pdo);
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('infrastructure_construction'); ?> - <?php echo t('construction_company'); ?></title>
    
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
    <link rel="stylesheet" href="../../assets/css/infrastructure.css">
    <link rel="stylesheet" href="../../assets/css/responsive-slider.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="<?php echo t('infrastructure_construction'); ?> - <?php echo t('construction_company'); ?>">
    <meta name="keywords" content="infrastructure, construction, drawings, architectural, structural, mechanical, electrical">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="<?php echo t('infrastructure_construction'); ?>">
    <meta property="og:description" content="<?php echo t('infrastructure_construction'); ?> - <?php echo t('construction_company'); ?>">
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
        <section class="infrastructure-hero">
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
                        <?php echo t('infrastructure_construction'); ?>
                    </h1>
                    <p class="hero-subtitle">
                        <?php echo t('comprehensive_infrastructure_solutions'); ?>
                    </p>
                    
                    <!-- Quick Navigation -->
                    <div class="hero-categories-nav">
                        <?php foreach ($infrastructure_categories as $key => $category): ?>
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
                    <?php foreach ($infrastructure_categories as $key => $category): ?>
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
                <?php if (empty($category['projects'])): ?>
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
                <?php else: ?>
                <!-- Tab Navigation -->
                <div class="tabs-nav-container" id="tabs-<?php echo $key; ?>">
                    <?php foreach ($category['projects'] as $index => $project): ?>
                    <button class="tab-button <?php echo $index === 0 ? 'active' : ''; ?>" 
                            onclick="showTab('<?php echo $key; ?>', <?php echo $index; ?>)" 
                            data-tab-index="<?php echo $index; ?>">
                        <span class="tab-button-icon">
                            <i class="fas fa-building"></i>
                        </span>
                        <span class="tab-button-text"><?php echo mb_substr($project['name_' . $current_lang] ?? $project['name'], 0, 30); ?></span>
                        <?php if (strlen($project['name_' . $current_lang] ?? $project['name']) > 30): ?>
                        <span class="tab-button-ellipsis">...</span>
                        <?php endif; ?>
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- Projects Slider Container -->
                <div class="projects-slider-wrapper">
                    <button class="slider-arrow slider-prev" onclick="prevSlide('<?php echo $key; ?>')">
                        <i class="fas fa-chevron-<?php echo $page_dir === 'rtl' ? 'right' : 'left'; ?>"></i>
                    </button>
                    
                    <div class="projects-slider" id="slider-<?php echo $key; ?>" data-direction="<?php echo $page_dir; ?>">
                        <?php foreach ($category['projects'] as $index => $project): ?>
                        <div class="project-slide" data-project-id="<?php echo $project['id']; ?>">
                            <div class="project-slide-image">
                                <div class="project-image-gallery">
                                    <div class="main-image-container">
                                        <img src="<?php echo $project['image']; ?>" 
                                             alt="<?php echo $project['name_' . $current_lang] ?? $project['name']; ?>"
                                             loading="lazy"
                                             decoding="async"
                                             class="project-image main-image">
                                        <div class="image-counter">
                                            <span class="current-image">1</span>
                                            <span class="image-separator">/</span>
                                            <span class="total-images"><?php echo (isset($project['images']) ? count($project['images']) : 0) + 1; ?></span>
                                        </div>
                                    </div>
                                    <?php if (isset($project['images']) && count($project['images']) > 0): ?>
                                    <div class="image-thumbnails" id="thumbnails-<?php echo $project['id']; ?>">
                                        <!-- Main image thumbnail -->
                                        <div class="thumbnail-item active" 
                                             onclick="changeMainImage(<?php echo $project['id']; ?>, 0)">
                                            <img src="<?php echo $project['image']; ?>" 
                                                 alt="<?php echo ($project['name_' . $current_lang] ?? $project['name']) . ' - 1'; ?>"
                                                 loading="lazy"
                                                 class="thumbnail-image">
                                        </div>
                                        <!-- Gallery image thumbnails -->
                                        <?php foreach ($project['images'] as $index => $image): ?>
                                        <div class="thumbnail-item" 
                                             onclick="changeMainImage(<?php echo $project['id']; ?>, <?php echo $index + 1; ?>)">
                                            <img src="<?php echo $image; ?>" 
                                                 alt="<?php echo ($project['name_' . $current_lang] ?? $project['name']) . ' - ' . ($index + 2); ?>"
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
                                <h3 class="project-slide-title"><?php echo $project['name_' . $current_lang] ?? $project['name']; ?></h3>
                                <div class="project-info-badges">
                                    <div class="info-badge badge-price">
                                        <div class="badge-icon"><i class="fas fa-dollar-sign"></i></div>
                                        <div class="badge-text">
                                            <span class="badge-label"><?php echo t('price'); ?></span>
                                            <span class="badge-value"><?php echo $project['price']; ?></span>
                                        </div>
                                    </div>
                                    <div class="info-badge badge-duration">
                                        <div class="badge-icon"><i class="fas fa-clock"></i></div>
                                        <div class="badge-text">
                                            <span class="badge-label"><?php echo t('duration'); ?></span>
                                            <span class="badge-value"><?php echo $project['duration']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="info-badge badge-about">
                                    <div class="badge-icon"><i class="fas fa-info-circle"></i></div>
                                    <div class="badge-text">
                                        <span class="badge-label"><?php echo t('about'); ?></span>
                                        <?php if (isset($project['features']) && is_array($project['features']) && count($project['features'])): ?>
                                        <p class="badge-description"><?php echo implode('. ', array_slice($project['features'], 0, 4)); ?>.</p>
                                        <?php else: ?>
                                        <p class="badge-description"><?php echo $project['description_' . $current_lang] ?? $project['description']; ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                    
                    <button class="slider-arrow slider-next" onclick="nextSlide('<?php echo $key; ?>')">
                        <i class="fas fa-chevron-<?php echo $page_dir === 'rtl' ? 'left' : 'right'; ?>"></i>
                    </button>
                </div>

                <!-- Slider Counter -->
                <div class="slide-counter" id="counter-<?php echo $key; ?>">
                    <span class="current-slide">1</span>
                    <span class="slide-separator">/</span>
                    <span class="total-slides"><?php echo count($category['projects']); ?></span>
                </div>

                <!-- Slider Dots -->
                <div class="slider-dots" id="dots-<?php echo $key; ?>">
                    <?php foreach ($category['projects'] as $index => $project): ?>
                    <button class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                            onclick="goToSlide('<?php echo $key; ?>', <?php echo $index; ?>)"></button>
                    <?php endforeach; ?>
                </div>
            </div>
                <?php endif; ?>
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
                    <h2 class="modal-title" id="modalTitle"><?php echo t('project_details'); ?></h2>
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
                                        <h4><?php echo t('materials_used'); ?></h4>
                                        <p id="modalMaterials">-</p>
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
                                <h3><?php echo t('detailed_description'); ?></h3>
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
                                    <?php echo t('get_quote'); ?>
                                </button>
                                <button class="btn btn-secondary" onclick="downloadProject()">
                                    <i class="fas fa-download"></i>
                                    <?php echo t('download_project'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Infrastructure JavaScript - Load scripts first -->
    <script src="../../assets/js/commercial_residential_design/responsive-slider.js"></script>
    <script src="../../assets/js/infrastructure/infrastructure.js"></script>
    
    <!-- Initialize with PHP data -->
    <script>
        // Pass PHP data to JavaScript
        const infrastructureProjectsData = <?php echo json_encode($infrastructure_categories); ?>;
        
        // Wait for scripts to load with multiple retries
        function initializeInfrastructureData() {
            if (typeof window.setProjectsData === 'function') {
                window.setProjectsData(infrastructureProjectsData);
                console.log('Infrastructure projects data set successfully');
            } else {
                // Wait a bit more for script to load (retry up to 10 times)
                let retries = 0;
                const maxRetries = 10;
                const checkInterval = setInterval(function() {
                    retries++;
                    if (typeof window.setProjectsData === 'function') {
                        window.setProjectsData(infrastructureProjectsData);
                        console.log('Infrastructure projects data set after ' + retries + ' retries');
                        clearInterval(checkInterval);
                    } else if (retries >= maxRetries) {
                        console.error('setProjectsData function not found after ' + maxRetries + ' retries');
                        clearInterval(checkInterval);
                    }
                }, 50);
            }
        }
        
        // Initialize immediately (scripts should be loaded synchronously)
        initializeInfrastructureData();
        
        // Also try when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof window.setProjectsData === 'function') {
                    window.setProjectsData(infrastructureProjectsData);
                }
            });
        }
    </script>
    
</body>
</html>
