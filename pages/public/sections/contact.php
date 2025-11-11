<?php
// Contact Section
// Load contact settings from database
// Make sure we have database connection and current language
if (!isset($pdo)) {
    // Try to load database connection if not already loaded
    $db_path = __DIR__ . '/../../../config/db_conected.php';
    if (file_exists($db_path)) {
        require_once $db_path;
    }
}

if (!isset($current_lang)) {
    // Try to load translations if not already loaded
    $translations_path = __DIR__ . '/../../../includes/translations.php';
    if (file_exists($translations_path)) {
        require_once $translations_path;
    } else {
        $current_lang = 'ku'; // Default to Kurdish
    }
}

// Load settings functions
$select_path = __DIR__ . '/../../../process/settings_management/select.php';
if (file_exists($select_path)) {
    require_once $select_path;
}

// Get contact information from settings (with fallback values)
$contact_phone = '';
$contact_phone_2 = '';
$contact_email = '';
$contact_address = '';
$contact_address_ku = '';
$contact_address_ar = '';
$facebook_url = '';
$twitter_url = '';
$instagram_url = '';
$linkedin_url = '';
$youtube_url = '';
$whatsapp_number = '';

if (isset($pdo) && function_exists('getSettingValue')) {
    try {
        $contact_phone = getSettingValue($pdo, 'contact_phone', '+964 770 924 0894');
        $contact_phone_2 = getSettingValue($pdo, 'contact_phone_2', '');
        $contact_email = getSettingValue($pdo, 'contact_email', 'info@construction-kurdistan.com');
        $contact_address = getSettingValue($pdo, 'contact_address', 'Business Center, Erbil, Kurdistan');
        $contact_address_ku = getSettingValue($pdo, 'contact_address_ku', 'سەنتەری بازرگانی، هەولێر، کوردستان');
        $contact_address_ar = getSettingValue($pdo, 'contact_address_ar', 'المركز التجاري، أربيل، كردستان');
        
        // Get social media links
        $facebook_url = getSettingValue($pdo, 'facebook_url', '');
        $twitter_url = getSettingValue($pdo, 'twitter_url', '');
        $instagram_url = getSettingValue($pdo, 'instagram_url', '');
        $linkedin_url = getSettingValue($pdo, 'linkedin_url', '');
        $youtube_url = getSettingValue($pdo, 'youtube_url', '');
        $whatsapp_number = getSettingValue($pdo, 'whatsapp_number', '');
    } catch (Exception $e) {
        // Use default values if there's an error
        error_log("Error loading contact settings: " . $e->getMessage());
        $contact_phone = '+964 770 924 0894';
        $contact_email = 'info@construction-kurdistan.com';
        $contact_address = 'Business Center, Erbil, Kurdistan';
        $contact_address_ku = 'سەنتەری بازرگانی، هەولێر، کوردستان';
        $contact_address_ar = 'المركز التجاري، أربيل، كردستان';
    }
} else {
    // Fallback values if database connection is not available
    $contact_phone = '+964 770 924 0894';
    $contact_email = 'info@construction-kurdistan.com';
    $contact_address = 'Business Center, Erbil, Kurdistan';
    $contact_address_ku = 'سەنتەری بازرگانی، هەولێر، کوردستان';
    $contact_address_ar = 'المركز التجاري، أربيل، كردستان';
}

// Determine which address to show based on language
$display_address = '';
if (isset($current_lang) && $current_lang === 'ku') {
    $display_address = !empty($contact_address_ku) ? $contact_address_ku : $contact_address;
} elseif (isset($current_lang) && $current_lang === 'ar') {
    $display_address = !empty($contact_address_ar) ? $contact_address_ar : $contact_address;
} else {
    $display_address = $contact_address;
}
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
                    <p class="text-gray-600 dark:text-gray-300 text-lg mb-2">
                        <a href="tel:<?php echo str_replace([' ', '-', '(', ')'], '', $contact_phone); ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <?php echo htmlspecialchars($contact_phone); ?>
                        </a>
                    </p>
                    <?php if (!empty($contact_phone_2)): ?>
                    <p class="text-gray-600 dark:text-gray-300 text-lg mb-2">
                        <a href="tel:<?php echo str_replace([' ', '-', '(', ')'], '', $contact_phone_2); ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            <?php echo htmlspecialchars($contact_phone_2); ?>
                        </a>
                    </p>
                    <?php endif; ?>
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
                    <p class="text-gray-600 dark:text-gray-300 text-lg mb-2 break-all">
                        <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" class="hover:text-green-600 dark:hover:text-green-400 transition-colors">
                            <?php echo htmlspecialchars($contact_email); ?>
                        </a>
                    </p>
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
                        <?php echo htmlspecialchars($display_address); ?>
                    </p>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mt-2"><?php echo t('visit_us') ?? 'Visit Us'; ?></p>
                </div>
            </div>
        </div>

        <!-- Social Media Links -->
        <?php if (!empty($facebook_url) || !empty($instagram_url) || !empty($twitter_url) || !empty($linkedin_url) || !empty($youtube_url) || !empty($whatsapp_number)): ?>
        <div class="mt-16 text-center">
            <p class="text-gray-600 dark:text-gray-300 mb-6 text-lg"><?php echo t('follow_us') ?? 'Follow Us'; ?></p>
            <div class="flex justify-center gap-6 flex-wrap">
                <?php if (!empty($facebook_url) && $facebook_url !== '#'): ?>
                <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl" title="Facebook">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <?php endif; ?>
                
                <?php if (!empty($instagram_url) && $instagram_url !== '#'): ?>
                <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl" title="Instagram">
                    <i class="fab fa-instagram"></i>
                </a>
                <?php endif; ?>
                
                <?php if (!empty($twitter_url) && $twitter_url !== '#'): ?>
                <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-500 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl" title="Twitter">
                    <i class="fab fa-twitter"></i>
                </a>
                <?php endif; ?>
                
                <?php if (!empty($linkedin_url) && $linkedin_url !== '#'): ?>
                <a href="<?php echo htmlspecialchars($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gradient-to-br from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl" title="LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <?php endif; ?>
                
                <?php if (!empty($youtube_url) && $youtube_url !== '#'): ?>
                <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl" title="YouTube">
                    <i class="fab fa-youtube"></i>
                </a>
                <?php endif; ?>
                
                <?php if (!empty($whatsapp_number)): ?>
                <a href="https://wa.me/<?php echo str_replace([' ', '-', '(', ')', '+'], '', $whatsapp_number); ?>" target="_blank" rel="noopener noreferrer" class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center text-white hover:scale-110 transition-transform duration-300 shadow-lg hover:shadow-xl" title="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>
