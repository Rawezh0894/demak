<?php

if (!function_exists('renderProjectShowcase')) {
    function renderProjectShowcase(array $config): void
    {
        $id = $config['id'] ?? uniqid('project_');
        $projects = $config['projects'] ?? [];
        $title = $config['title'] ?? '';
        $description = $config['description'] ?? '';
        $color = $config['color'] ?? '#2563eb';
        $icon = $config['icon'] ?? 'fas fa-building';
        $pageDir = $config['page_dir'] ?? 'rtl';
        $currentLang = $config['current_lang'] ?? 'ku';
        $categoryKey = $config['category_key'] ?? $id;
        $eyebrow = $config['eyebrow'] ?? t('our_projects');
        $emptyIcon = $config['empty_icon'] ?? $icon;
        $emptyColor = $config['empty_color'] ?? $color;
        $showContactCta = $config['show_contact_cta'] ?? true;

        $totalProjects = count($projects);
        $activeProjects = count(array_filter($projects, function ($project) {
            return isset($project['status']) && $project['status'] === 'active';
        }));
        $completedProjects = count(array_filter($projects, function ($project) {
            return isset($project['status']) && $project['status'] === 'completed';
        }));

        if ($totalProjects === 0) {
            ?>
            <div class="project-showcase empty-state" style="--accent-color: <?php echo htmlspecialchars($color, ENT_QUOTES, 'UTF-8'); ?>;">
                <div class="no-projects-message elevated">
                    <div class="no-projects-icon">
                        <i class="<?php echo htmlspecialchars($emptyIcon, ENT_QUOTES, 'UTF-8'); ?>" style="color: <?php echo htmlspecialchars($emptyColor, ENT_QUOTES, 'UTF-8'); ?>"></i>
                    </div>
                    <h3 class="no-projects-title"><?php echo t('no_projects_available'); ?></h3>
                    <p class="no-projects-description"><?php echo t('no_projects_description'); ?></p>
                    <?php if ($showContactCta): ?>
                        <div class="no-projects-cta">
                            <a href="#contact-section" class="btn btn-primary">
                                <i class="fas fa-envelope"></i>
                                <?php echo t('contact_us_for_custom_project'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php
            return;
        }

        $safeId = htmlspecialchars($id, ENT_QUOTES, 'UTF-8');
        $safeIcon = htmlspecialchars($icon, ENT_QUOTES, 'UTF-8');
        $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        $safeDescription = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
        $safeColor = htmlspecialchars($color, ENT_QUOTES, 'UTF-8');
        $safeEyebrow = htmlspecialchars($eyebrow, ENT_QUOTES, 'UTF-8');
        $safeCategoryKey = htmlspecialchars($categoryKey, ENT_QUOTES, 'UTF-8');
        ?>
        <div class="project-showcase" data-category="<?php echo $safeCategoryKey; ?>" style="--accent-color: <?php echo $safeColor; ?>;">
            <div class="project-showcase-header">
                <div class="project-showcase-icon">
                    <i class="<?php echo $safeIcon; ?>"></i>
                </div>
                <div class="project-showcase-text">
                    <p class="project-eyebrow"><?php echo $safeEyebrow; ?></p>
                    <h3><?php echo $safeTitle; ?></h3>
                    <?php if (!empty($description)): ?>
                        <p><?php echo $safeDescription; ?></p>
                    <?php endif; ?>
                </div>
                <div class="project-metrics">
                    <div class="project-metric">
                        <span class="metric-label"><?php echo t('total_projects'); ?></span>
                        <span class="metric-value"><?php echo $totalProjects; ?></span>
                    </div>
                    <div class="project-metric">
                        <span class="metric-label"><?php echo t('active_projects'); ?></span>
                        <span class="metric-value"><?php echo $activeProjects; ?></span>
                    </div>
                    <div class="project-metric">
                        <span class="metric-label"><?php echo t('completed_projects'); ?></span>
                        <span class="metric-value"><?php echo $completedProjects; ?></span>
                    </div>
                </div>
            </div>

            <div class="projects-slider-container project-showcase-shell">
                <div class="projects-slider-wrapper">
                    <div class="slide-counter" id="counter-<?php echo $safeId; ?>">
                        <div class="counter-label"><?php echo t('projects'); ?></div>
                        <div class="counter-values">
                            <span class="current-slide">1</span>
                            <span class="slide-separator">/</span>
                            <span class="total-slides"><?php echo $totalProjects; ?></span>
                        </div>
                        <div class="slide-progress">
                            <span class="slide-progress-fill"></span>
                        </div>
                    </div>

                    <div class="projects-slider" id="slider-<?php echo $safeId; ?>">
                        <?php foreach ($projects as $project): ?>
                            <?php
                            $projectId = $project['id'] ?? uniqid('project_');
                            $projectName = $project['name_' . $currentLang] ?? $project['name'] ?? '';
                            $projectDescription = $project['description_' . $currentLang] ?? $project['description'] ?? '';
                            $projectPrice = $project['price'] ?? '-';
                            $projectDuration = $project['duration'] ?? '-';
                            $projectStatus = strtolower((string)($project['status'] ?? ''));
                            $projectLocation = $project['location_' . $currentLang] ?? $project['location'] ?? '';
                            $projectArea = $project['area'] ?? '';
                            $projectFloors = $project['floors'] ?? '';
                            $projectCategory = $project['category_key'] ?? $categoryKey;
                            $mainImage = !empty($project['image']) ? $project['image'] : 'https://via.placeholder.com/1200x800?text=Project';

                            $galleryImages = [];
                            if (isset($project['images'])) {
                                if (is_array($project['images'])) {
                                    $galleryImages = array_filter($project['images']);
                                } elseif (is_string($project['images'])) {
                                    $decodedGallery = json_decode($project['images'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedGallery)) {
                                        $galleryImages = array_filter($decodedGallery);
                                    }
                                }
                            }

                            $features = [];
                            if (isset($project['features'])) {
                                if (is_array($project['features'])) {
                                    $features = $project['features'];
                                } elseif (is_string($project['features'])) {
                                    $decodedFeatures = json_decode($project['features'], true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decodedFeatures)) {
                                        $features = $decodedFeatures;
                                    } else {
                                        $features = array_filter(array_map('trim', explode(',', $project['features'])));
                                    }
                                }
                            }

                            $normalizedName = function_exists('mb_strtolower')
                                ? mb_strtolower($projectName, 'UTF-8')
                                : strtolower($projectName);

                            $safeProjectId = htmlspecialchars($projectId, ENT_QUOTES, 'UTF-8');
                            $safeProjectName = htmlspecialchars($projectName, ENT_QUOTES, 'UTF-8');
                            $safeProjectDescription = htmlspecialchars($projectDescription, ENT_QUOTES, 'UTF-8');
                            $safeProjectPrice = htmlspecialchars((string)$projectPrice, ENT_QUOTES, 'UTF-8');
                            $safeProjectDuration = htmlspecialchars((string)$projectDuration, ENT_QUOTES, 'UTF-8');
                            $safeProjectLocation = htmlspecialchars($projectLocation, ENT_QUOTES, 'UTF-8');
                            $safeMainImage = htmlspecialchars($mainImage, ENT_QUOTES, 'UTF-8');
                            $safeStatus = htmlspecialchars($projectStatus, ENT_QUOTES, 'UTF-8');
                            $safeCategory = htmlspecialchars($projectCategory, ENT_QUOTES, 'UTF-8');
                            $safeArea = htmlspecialchars(preg_replace('/[^\d.]/', '', (string)$projectArea), ENT_QUOTES, 'UTF-8');
                            $safeFloors = htmlspecialchars(preg_replace('/[^\d.]/', '', (string)$projectFloors), ENT_QUOTES, 'UTF-8');
                            $safeNormalizedName = htmlspecialchars($normalizedName, ENT_QUOTES, 'UTF-8');

                            $totalImages = count($galleryImages) + 1;
                            ?>
                            <div class="project-slide"
                                 data-project-id="<?php echo $safeProjectId; ?>"
                                 data-category="<?php echo $safeCategory; ?>"
                                 data-name="<?php echo $safeNormalizedName; ?>"
                                 <?php if ($safeArea !== ''): ?>data-area="<?php echo $safeArea; ?>"<?php endif; ?>
                                 <?php if ($safeFloors !== ''): ?>data-floors="<?php echo $safeFloors; ?>"<?php endif; ?>>
                                <div class="project-slide-image">
                                <div class="project-image-gallery">
                                    <div class="main-image-container">
                                        <img src="<?php echo $safeMainImage; ?>"
                                             alt="<?php echo $safeProjectName; ?>"
                                             loading="lazy"
                                             decoding="async"
                                             class="project-image main-image">
                                        <div class="image-counter">
                                            <span class="current-image">1</span>
                                            <span class="image-separator">/</span>
                                            <span class="total-images"><?php echo $totalImages; ?></span>
                                        </div>
                                    </div>
                                    <?php if (!empty($galleryImages)): ?>
                                        <div class="image-thumbnails" id="thumbnails-<?php echo $safeProjectId; ?>">
                                            <div class="thumbnail-item active"
                                                 onclick="changeMainImage(<?php echo (int)$projectId; ?>, 0)">
                                                <img src="<?php echo $safeMainImage; ?>"
                                                     alt="<?php echo $safeProjectName; ?> - 1"
                                                     loading="lazy"
                                                     class="thumbnail-image">
                                            </div>
                                            <?php foreach ($galleryImages as $imgIndex => $imagePath): ?>
                                                <?php $safeGalleryImage = htmlspecialchars($imagePath, ENT_QUOTES, 'UTF-8'); ?>
                                                <div class="thumbnail-item"
                                                     onclick="changeMainImage(<?php echo (int)$projectId; ?>, <?php echo $imgIndex + 1; ?>)">
                                                    <img src="<?php echo $safeGalleryImage; ?>"
                                                         alt="<?php echo $safeProjectName; ?> - <?php echo $imgIndex + 2; ?>"
                                                         loading="lazy"
                                                         class="thumbnail-image">
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="project-slide-overlay">
                                    <button class="view-details-btn" onclick="showProjectDetails(<?php echo (int)$projectId; ?>)">
                                        <i class="fas fa-eye"></i>
                                        <?php echo t('view_details'); ?>
                                    </button>
                                </div>
                                </div>
                                <div class="project-slide-content">
                                    <div class="project-slide-topline">
                                        <span class="project-chip">
                                            <i class="<?php echo $safeIcon; ?>"></i>
                                            <?php echo $safeTitle; ?>
                                        </span>
                                        <?php if (!empty($projectStatus)): ?>
                                            <span class="status-pill status-<?php echo $safeStatus; ?>">
                                                <?php echo t('status_' . $projectStatus) ?? ucfirst($projectStatus); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <h3 class="project-slide-title"><?php echo $safeProjectName; ?></h3>
                                    <?php if (!empty($projectLocation)): ?>
                                        <div class="project-location">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?php echo $safeProjectLocation; ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <p class="project-lede">
                                        <?php
                                        if (!empty($features)) {
                                            echo htmlspecialchars(implode(' • ', array_slice($features, 0, 3)), ENT_QUOTES, 'UTF-8');
                                        } else {
                                            echo $safeProjectDescription;
                                        }
                                        ?>
                                    </p>
                                    <div class="project-info-grid">
                                        <?php if (!empty($projectPrice) && $projectPrice !== '-'): ?>
                                            <div class="info-pill">
                                                <span><?php echo t('price'); ?></span>
                                                <strong><?php echo $safeProjectPrice; ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($projectDuration) && $projectDuration !== '-'): ?>
                                            <div class="info-pill">
                                                <span><?php echo t('duration'); ?></span>
                                                <strong><?php echo $safeProjectDuration; ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($projectArea)): ?>
                                            <div class="info-pill">
                                                <span><?php echo t('area'); ?></span>
                                                <strong><?php echo htmlspecialchars($projectArea, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (!empty($projectFloors)): ?>
                                            <div class="info-pill">
                                                <span><?php echo t('floors'); ?></span>
                                                <strong><?php echo htmlspecialchars($projectFloors, ENT_QUOTES, 'UTF-8'); ?></strong>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($features)): ?>
                                        <ul class="project-feature-list">
                                            <?php foreach (array_slice($features, 0, 4) as $feature): ?>
                                                <li>
                                                    <i class="fas fa-check-circle"></i>
                                                    <span><?php echo htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                    <div class="project-cta-row">
                                        <button class="btn btn-primary" onclick="showProjectDetails(<?php echo (int)$projectId; ?>)">
                                            <?php echo t('view_details'); ?>
                                            <i class="fas fa-arrow-<?php echo $pageDir === 'rtl' ? 'left' : 'right'; ?>"></i>
                                        </button>
                                        <button class="btn btn-secondary ghost" onclick="requestQuote()">
                                            <i class="fas fa-calculator"></i>
                                            <?php echo t('get_quote'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="project-slider-nav">
                    <button type="button" class="slider-arrow slider-prev" onclick="prevSlide('<?php echo $safeId; ?>')">
                        <i class="fas fa-arrow-<?php echo $pageDir === 'rtl' ? 'right' : 'left'; ?>"></i>
                    </button>
                    <button type="button" class="slider-arrow slider-next" onclick="nextSlide('<?php echo $safeId; ?>')">
                        <i class="fas fa-arrow-<?php echo $pageDir === 'rtl' ? 'left' : 'right'; ?>"></i>
                    </button>
                </div>

                <div class="slider-dots" id="dots-<?php echo $safeId; ?>"></div>
            </div>
        </div>
        <?php
    }
}

