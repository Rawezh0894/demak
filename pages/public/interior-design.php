<?php
session_start();
require_once '../../config/db_conected.php';
require_once '../../includes/translations.php';
require_once '../../config/interior_design_loader.php';

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];

// Load interior design projects from database
$interior_design_projects = loadInteriorDesignData($pdo);
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دیزاین و جێبەجێکردنی ناوەوە - <?php echo t('construction_company'); ?></title>
    
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
    <meta name="description" content="دیزاین و جێبەجێکردنی ناوەوەی بینا - <?php echo t('construction_company'); ?>">
    <meta name="keywords" content="interior design, building interior, room design, architecture">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="دیزاین و جێبەجێکردنی ناوەوە">
    <meta property="og:description" content="دیزاین و جێبەجێکردنی ناوەوەی بینا - <?php echo t('construction_company'); ?>">
    <meta property="og:type" content="website">
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Include Navbar -->
    <?php include '../../includes/navbar.php'; ?>
    
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
                        دیزاین و جێبەجێکردنی ناوەوە
                    </h1>
                    <p class="hero-subtitle">
                        کارێکی نایاب - تیمێکی پیشەیی
                    </p>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section class="category-section" id="projects-section">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <!-- Section Header -->
                <div class="category-section-header">
                    <div class="category-icon-large" style="background: #3b82f6;">
                        <i class="fas fa-couch"></i>
                    </div>
                    <div class="category-header-text">
                        <h2 class="category-section-title">پڕۆژەکانی دیزاینی ناوەوە</h2>
                        <p class="category-section-description">دیزاینی ناوەوەی پیشەیی بۆ بیناکان</p>
                    </div>
                </div>

                <!-- Projects Slider -->
                <?php if (empty($interior_design_projects)): ?>
                <!-- No Projects Message -->
                <div class="no-projects-message">
                    <div class="no-projects-icon">
                        <i class="fas fa-couch" style="color: #3b82f6;"></i>
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
                <div class="tabs-nav-container" id="tabs-projects">
                    <?php foreach ($interior_design_projects as $index => $project): ?>
                    <button class="tab-button <?php echo $index === 0 ? 'active' : ''; ?>" 
                            onclick="showTab('projects', <?php echo $index; ?>)" 
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
                <div class="slider-container">
                    <button class="slider-arrow slider-prev" onclick="prevSlide('projects')">
                        <i class="fas fa-chevron-<?php echo $page_dir === 'rtl' ? 'right' : 'left'; ?>"></i>
                    </button>
                    
                    <div class="projects-slider" id="slider-projects">
                        <?php foreach ($interior_design_projects as $index => $project): ?>
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
                                        <?php foreach ($project['images'] as $imgIndex => $image): ?>
                                        <div class="thumbnail-item" 
                                             onclick="changeMainImage(<?php echo $project['id']; ?>, <?php echo $imgIndex + 1; ?>)">
                                            <img src="<?php echo $image; ?>" 
                                                 alt="<?php echo ($project['name_' . $current_lang] ?? $project['name']) . ' - ' . ($imgIndex + 2); ?>"
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
                                        <p class="badge-description"><?php echo $project['description_' . $current_lang] ?? $project['description']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button class="slider-arrow slider-next" onclick="nextSlide('projects')">
                        <i class="fas fa-chevron-<?php echo $page_dir === 'rtl' ? 'left' : 'right'; ?>"></i>
                    </button>
                </div>

                <!-- Slider Dots -->
                <div class="slider-dots" id="dots-projects">
                    <?php foreach ($interior_design_projects as $index => $project): ?>
                    <button class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                            onclick="goToSlide('projects', <?php echo $index; ?>)"></button>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </section>

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
                            </div>
                            <div class="project-description">
                                <h3><?php echo t('detailed_description'); ?></h3>
                                <p id="modalDescription">-</p>
                            </div>
                            <div class="action-buttons">
                                <button class="btn btn-primary" onclick="requestQuote()">
                                    <i class="fas fa-calculator"></i>
                                    <?php echo t('get_quote'); ?>
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

    <!-- Interior Design JavaScript -->
    <script src="../../assets/js/commercial_residential_design/responsive-slider.js"></script>
    <script src="../../assets/js/interior_design/interior_design.js"></script>
    
    <!-- Initialize with PHP data -->
    <script>
        // Prepare projects data for JavaScript (convert array to object format for compatibility)
        const interiorDesignProjectsData = {
            'projects': {
                'title': 'پڕۆژەکانی دیزاینی ناوەوە',
                'projects': <?php echo json_encode($interior_design_projects); ?>
            }
        };
        
        // Set projects data immediately
        if (typeof setProjectsData === 'function') {
            setProjectsData(interiorDesignProjectsData);
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof setProjectsData === 'function') {
                    setProjectsData(interiorDesignProjectsData);
                }
            });
        }
    </script>
    
</body>
</html>
