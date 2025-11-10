<?php
// Footer Section
?>
<!-- Footer -->
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <h3 class="text-xl font-bold mb-4"><?php echo t('construction_company'); ?></h3>
                <p class="text-gray-300 mb-4">
                    <?php echo t('we_build_dreams'); ?> - <?php echo t('excellence_in_construction'); ?>
                </p>
                <div class="flex space-x-4 rtl:space-x-reverse">
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <a href="#" class="text-gray-300 hover:text-white">
                        <i class="fab fa-instagram"></i>
                    </a>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo t('our_services'); ?></h4>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="#" class="hover:text-white"><?php echo t('project_management'); ?></a></li>
                    <li><a href="#" class="hover:text-white"><?php echo t('design_construction'); ?></a></li>
                    <li><a href="#" class="hover:text-white"><?php echo t('renovation'); ?></a></li>
                    <li><a href="#" class="hover:text-white"><?php echo t('maintenance'); ?></a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo t('quick_links'); ?></h4>
                <ul class="space-y-2 text-gray-300">
                    <li><a href="#projects" class="hover:text-white"><?php echo t('our_projects'); ?></a></li>
                    <li><a href="#about" class="hover:text-white"><?php echo t('about_us'); ?></a></li>
                    <li><a href="#contact" class="hover:text-white"><?php echo t('contact_us'); ?></a></li>
                    <li><a href="#" class="hover:text-white"><?php echo t('get_quote'); ?></a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo t('contact_info'); ?></h4>
                <div class="space-y-2 text-gray-300">
                    <p><i class="fas fa-phone mr-2"></i> +964 750 123 4567</p>
                    <p><i class="fas fa-envelope mr-2"></i> info@construction-kurdistan.com</p>
                    <p><i class="fas fa-map-marker-alt mr-2"></i> 
                        <?php 
                        if ($current_lang === 'ku') echo 'هەولێر، کوردستان';
                        elseif ($current_lang === 'ar') echo 'أربيل، كردستان';
                        else echo 'Erbil, Kurdistan';
                        ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
            <p>&copy; 2024 <?php echo t('construction_company'); ?>. <?php echo t('all_rights_reserved'); ?></p>
        </div>
    </div>
</footer>
