<?php
// Projects Section
?>
<!-- Projects Section -->
<section id="projects" class="section-animate py-20 bg-gray-50 dark:bg-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                <?php echo t('our_projects'); ?>
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                <?php echo t('we_build_dreams'); ?> - <?php echo t('excellence_in_construction'); ?>
            </p>
        </div>

        <!-- Project Filter Tabs -->
        <div class="flex flex-wrap justify-center gap-4 mb-12">
            <button onclick="filterProjects('all')" class="filter-btn active px-6 py-2 rounded-full bg-blue-600 text-white">
                <?php echo t('view_all_projects'); ?>
            </button>
            <button onclick="filterProjects('residential')" class="filter-btn px-6 py-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white transition-colors">
                <?php echo t('residential'); ?>
            </button>
            <button onclick="filterProjects('commercial')" class="filter-btn px-6 py-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white transition-colors">
                <?php echo t('commercial'); ?>
            </button>
            <button onclick="filterProjects('industrial')" class="filter-btn px-6 py-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-blue-600 hover:text-white transition-colors">
                <?php echo t('industrial'); ?>
            </button>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php foreach ($projects as $project): ?>
                <div class="project-card project-item" data-type="<?php echo $project['type']; ?>">
                    <!-- Floating Particles -->
                    <div class="floating-particles">
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                        <div class="particle"></div>
                    </div>
                    
                    <!-- Animated Lines -->
                    <div class="animated-lines">
                        <div class="line"></div>
                        <div class="line"></div>
                        <div class="line"></div>
                    </div>
                    
                    <img src="<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="project-image">
                    <div class="p-6" style="position: relative; z-index: 2;">
                        <div class="flex items-center justify-between mb-4">
                            <span class="status-badge px-3 py-1 text-xs font-semibold rounded-full 
                                <?php 
                                switch($project['status']) {
                                    case 'completed': echo 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'; break;
                                    case 'active': echo 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300'; break;
                                    case 'upcoming': echo 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'; break;
                                }
                                ?>">
                                <?php echo t('status_' . $project['status']); ?>
                            </span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 font-semibold">
                                <?php echo $project['budget']; ?>
                            </span>
                        </div>
                        
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                            <?php 
                            if ($current_lang === 'ku') echo $project['title_ku'];
                            elseif ($current_lang === 'ar') echo $project['title_ar'];
                            else echo $project['title'];
                            ?>
                        </h3>
                        
                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            <?php 
                            if ($current_lang === 'ku') echo $project['description_ku'];
                            elseif ($current_lang === 'ar') echo $project['description_ar'];
                            else echo $project['description'];
                            ?>
                        </p>
                        
                        <div class="project-info space-y-2 text-sm text-gray-500 dark:text-gray-400 mb-4">
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                                <span>
                                    <?php 
                                    if ($current_lang === 'ku') echo $project['location_ku'];
                                    elseif ($current_lang === 'ar') echo $project['location_ar'];
                                    else echo $project['location'];
                                    ?>
                                </span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-user w-4 mr-2"></i>
                                <span>
                                    <?php 
                                    if ($current_lang === 'ku') echo $project['client_ku'];
                                    elseif ($current_lang === 'ar') echo $project['client_ar'];
                                    else echo $project['client'];
                                    ?>
                                </span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-calendar w-4 mr-2"></i>
                                <span><?php echo date('M Y', strtotime($project['completion_date'])); ?></span>
                            </div>
                        </div>
                        
                        <button class="w-full btn btn-primary">
                            <?php echo t('learn_more'); ?>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
