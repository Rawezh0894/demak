<?php
session_start();
require_once '../../config/db_conected.php';
require_once '../../includes/translations.php';
require_once '../../includes/project-showcase.php';
require_once '../../config/exterior_design_loader.php';

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];

// Load exterior design projects from database
$exterior_design_projects = loadExteriorDesignData($pdo);
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>دیزاین و جێبەجێکردنی دەرەوە - <?php echo t('construction_company'); ?></title>
    
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
    <link rel="stylesheet" href="../../assets/css/project-showcase.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="دیزاین و جێبەجێکردنی دەرەوەی بینا - <?php echo t('construction_company'); ?>">
    <meta name="keywords" content="exterior design, building exterior, facade design, architecture">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="دیزاین و جێبەجێکردنی دەرەوە">
    <meta property="og:description" content="دیزاین و جێبەجێکردنی دەرەوەی بینا - <?php echo t('construction_company'); ?>">
    <meta property="og:type" content="website">
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Include Navbar -->
    <?php include '../../includes/navbar.php'; ?>
    
    <!-- Include Sidebar -->
    <?php include '../../includes/sidebar.php'; ?>
    
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
                        دیزاین و جێبەجێکردنی دەرەوە
                    </h1>
                    <p class="hero-subtitle">
                        کارێکی نایاب - تیمێکی پیشەیی
                    </p>
                </div>
            </div>
        </section>

        <!-- Projects Section -->
        <section class="category-section" id="projects-section" data-category="projects">
            <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <!-- Section Header -->
                <div class="category-section-header">
                    <div class="category-icon-large" style="background: #10b981;">
                        <i class="fas fa-tree"></i>
                    </div>
                    <div class="category-header-text">
                        <h2 class="category-section-title">پڕۆژەکانی دیزاینی دەرەوە</h2>
                        <p class="category-section-description">دیزاینی دەرەوەی پیشەیی بۆ بیناکان</p>
                    </div>
                </div>

                <?php
                renderProjectShowcase([
                    'id' => 'projects',
                    'projects' => $exterior_design_projects,
                    'title' => t('exterior_design_implementation'),
                    'description' => 'دیزاینی دەرەوەی پیشەیی بۆ بیناکان',
                    'color' => '#10b981',
                    'icon' => 'fas fa-tree',
                    'page_dir' => $page_dir,
                    'current_lang' => $current_lang,
                    'category_key' => 'projects'
                ]);
                ?>
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

    <!-- Exterior Design JavaScript -->
    <script src="../../assets/js/commercial_residential_design/responsive-slider.js"></script>
    <script src="../../assets/js/exterior_design/exterior_design.js"></script>
    
    <!-- Initialize with PHP data -->
    <script>
        // Prepare projects data for JavaScript (convert array to object format for compatibility)
        const exteriorDesignProjectsData = {
            'projects': {
                'title': 'پڕۆژەکانی دیزاینی دەرەوە',
                'projects': <?php echo json_encode($exterior_design_projects); ?>
            }
        };
        
        // Set projects data immediately
        if (typeof setProjectsData === 'function') {
            setProjectsData(exteriorDesignProjectsData);
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof setProjectsData === 'function') {
                    setProjectsData(exteriorDesignProjectsData);
                }
            });
        }
    </script>
    
</body>
</html>
