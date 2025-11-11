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
<section id="contact" class="py-20 bg-white dark:bg-gray-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                <?php echo t('get_in_touch'); ?>
            </h2>
            <p class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                <?php echo t('contact_us'); ?> - <?php echo t('we_are_here_to_help') ?? 'We are here to help'; ?>
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Phone Card -->
            <div class="service-card group contact-phone-card" style="cursor: pointer;" dir="ltr">
                <!-- Floating Particles -->
                <div class="floating-particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
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
                
                <div class="service-icon">
                    <i class="fas fa-phone text-2xl"></i>
                </div>
                <h3 class="service-title">
                    <?php echo t('phone'); ?>
                </h3>
                <p class="service-description" dir="ltr">
                    <?php
                    // Ensure phone number starts with +964
                    $phone_display = $contact_phone;
                    $phone_link = str_replace([' ', '-', '(', ')'], '', $contact_phone);
                    
                    // If phone doesn't start with +964, add it
                    if (!empty($phone_link) && !str_starts_with($phone_link, '+964') && !str_starts_with($phone_link, '964')) {
                        // Remove any existing country code
                        $phone_link = preg_replace('/^(\+?964|00964)/', '', $phone_link);
                        $phone_link = '+964' . $phone_link;
                        
                        // Update display if it doesn't already have +964
                        if (!str_contains($phone_display, '+964') && !str_contains($phone_display, '964')) {
                            $phone_display = '+964 ' . ltrim($phone_display, '+0');
                        }
                    } elseif (!empty($phone_link) && str_starts_with($phone_link, '964') && !str_starts_with($phone_link, '+964')) {
                        $phone_link = '+' . $phone_link;
                        if (!str_contains($phone_display, '+964')) {
                            $phone_display = '+' . $phone_display;
                        }
                    }
                    ?>
                    <a href="tel:<?php echo htmlspecialchars($phone_link); ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <?php echo htmlspecialchars($phone_display); ?>
                    </a>
                    <?php if (!empty($contact_phone_2)): ?>
                    <br>
                    <?php
                    // Ensure phone_2 number starts with +964
                    $phone_2_display = $contact_phone_2;
                    $phone_2_link = str_replace([' ', '-', '(', ')'], '', $contact_phone_2);
                    
                    // If phone_2 doesn't start with +964, add it
                    if (!empty($phone_2_link) && !str_starts_with($phone_2_link, '+964') && !str_starts_with($phone_2_link, '964')) {
                        // Remove any existing country code
                        $phone_2_link = preg_replace('/^(\+?964|00964)/', '', $phone_2_link);
                        $phone_2_link = '+964' . $phone_2_link;
                        
                        // Update display if it doesn't already have +964
                        if (!str_contains($phone_2_display, '+964') && !str_contains($phone_2_display, '964')) {
                            $phone_2_display = '+964 ' . ltrim($phone_2_display, '+0');
                        }
                    } elseif (!empty($phone_2_link) && str_starts_with($phone_2_link, '964') && !str_starts_with($phone_2_link, '+964')) {
                        $phone_2_link = '+' . $phone_2_link;
                        if (!str_contains($phone_2_display, '+964')) {
                            $phone_2_display = '+' . $phone_2_display;
                        }
                    }
                    ?>
                    <a href="tel:<?php echo htmlspecialchars($phone_2_link); ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                        <?php echo htmlspecialchars($phone_2_display); ?>
                    </a>
                    <?php endif; ?>
                </p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2"><?php echo t('available_24_7') ?? 'Available 24/7'; ?></p>
                <div class="service-arrow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
            </div>

            <!-- Email Card -->
            <div class="service-card group contact-email-card" style="cursor: pointer;">
                <!-- Floating Particles -->
                <div class="floating-particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
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
                
                <div class="service-icon">
                    <i class="fas fa-envelope text-2xl"></i>
                </div>
                <h3 class="service-title">
                    <?php echo t('email'); ?>
                </h3>
                <p class="service-description break-all">
                    <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" class="hover:text-green-600 dark:hover:text-green-400 transition-colors">
                        <?php echo htmlspecialchars($contact_email); ?>
                    </a>
                </p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2"><?php echo t('quick_response') ?? 'Quick Response'; ?></p>
                <div class="service-arrow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </div>
            </div>

            <!-- Address Card -->
            <div class="service-card group contact-address-card" style="cursor: pointer;">
                <!-- Floating Particles -->
                <div class="floating-particles">
                    <div class="particle"></div>
                    <div class="particle"></div>
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
                
                <div class="service-icon">
                    <i class="fas fa-map-marker-alt text-2xl"></i>
                </div>
                <h3 class="service-title">
                    <?php echo t('address'); ?>
                </h3>
                <p class="service-description leading-relaxed">
                    <?php echo htmlspecialchars($display_address); ?>
                </p>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-2"><?php echo t('visit_us') ?? 'Visit Us'; ?></p>
                <div class="service-arrow">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
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
