<?php
// Contact Section
?>
<!-- Contact Section -->
<section id="contact" class="py-20 bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 relative overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 opacity-5 dark:opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-purple-500 rounded-full blur-3xl"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <div class="inline-block mb-4">
                <span class="text-4xl md:text-5xl font-extrabold bg-gradient-to-r from-blue-600 via-purple-600 to-blue-600 dark:from-blue-400 dark:via-purple-400 dark:to-blue-400 bg-clip-text text-transparent">
                    <?php echo t('get_in_touch'); ?>
                </span>
            </div>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto mt-4">
                <?php echo t('contact_us'); ?>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Phone Card -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-bl-3xl opacity-10 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-phone text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3"><?php echo t('phone'); ?></h3>
                    <p class="text-gray-600 dark:text-gray-300 text-lg mb-2">+964 770 924 0894</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm"><?php echo t('available_24_7') ?? 'Available 24/7'; ?></p>
                </div>
            </div>

            <!-- Email Card -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-bl-3xl opacity-10 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-envelope text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3"><?php echo t('email'); ?></h3>
                    <p class="text-gray-600 dark:text-gray-300 text-lg mb-2 break-all">info@construction-kurdistan.com</p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm"><?php echo t('quick_response') ?? 'Quick Response'; ?></p>
                </div>
            </div>

            <!-- Address Card -->
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-gray-700 md:col-span-2 lg:col-span-1">
                <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-bl-3xl opacity-10 group-hover:opacity-20 transition-opacity"></div>
                <div class="relative z-10">
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 transform group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <i class="fas fa-map-marker-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3"><?php echo t('address'); ?></h3>
                    <p class="text-gray-600 dark:text-gray-300 text-lg leading-relaxed">
                        <?php 
                        if ($current_lang === 'ku') echo 'سەنتەری بازرگانی، هەولێر، کوردستان';
                        elseif ($current_lang === 'ar') echo 'المركز التجاري، أربيل، كردستان';
                        else echo 'Business Center, Erbil, Kurdistan';
                        ?>
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-2"><?php echo t('visit_us') ?? 'Visit Us'; ?></p>
                </div>
            </div>
        </div>

        <!-- Social Media Links (Optional) -->
        <div class="mt-16 text-center">
            <p class="text-gray-600 dark:text-gray-300 mb-6 text-lg"><?php echo t('follow_us') ?? 'Follow Us'; ?></p>
            <div class="flex justify-center gap-6">
                <a href="#" class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl">
                    <i class="fab fa-twitter"></i>
                </a>
                <a href="#" class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl">
                    <i class="fab fa-linkedin-in"></i>
                </a>
            </div>
        </div>
    </div>
</section>
