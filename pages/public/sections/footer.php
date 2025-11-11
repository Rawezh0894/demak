<?php
// Footer Section
// Load contact settings from database
require_once __DIR__ . '/../../../process/settings_management/select.php';

// Get company information from settings (with fallback values)
$site_name = t('construction_company');
$site_description = t('we_build_dreams') . ' - ' . t('excellence_in_construction');

// Get company name based on language
if (isset($pdo) && function_exists('getSettingValue')) {
    try {
        if ($current_lang === 'ku') {
            $site_name = getSettingValue($pdo, 'site_name_ku', t('construction_company'));
            $site_description = getSettingValue($pdo, 'site_description_ku', t('we_build_dreams') . ' - ' . t('excellence_in_construction'));
        } elseif ($current_lang === 'ar') {
            $site_name = getSettingValue($pdo, 'site_name_ar', t('construction_company'));
            $site_description = getSettingValue($pdo, 'site_description_ar', t('we_build_dreams') . ' - ' . t('excellence_in_construction'));
        } else {
            $site_name = getSettingValue($pdo, 'site_name', t('construction_company'));
            $site_description = getSettingValue($pdo, 'site_description', t('we_build_dreams') . ' - ' . t('excellence_in_construction'));
        }
    } catch (Exception $e) {
        error_log("Error loading footer settings: " . $e->getMessage());
    }
}

// Get contact information from settings
$contact_phone = '+964 750 123 4567';
$contact_email = 'info@construction-kurdistan.com';
$contact_address = 'Erbil, Kurdistan';
$contact_address_ku = 'هەولێر، کوردستان';
$contact_address_ar = 'أربيل، كردستان';

if (isset($pdo) && function_exists('getSettingValue')) {
    try {
        $contact_phone = getSettingValue($pdo, 'contact_phone', '+964 750 123 4567');
        $contact_email = getSettingValue($pdo, 'contact_email', 'info@construction-kurdistan.com');
        $contact_address = getSettingValue($pdo, 'contact_address', 'Erbil, Kurdistan');
        $contact_address_ku = getSettingValue($pdo, 'contact_address_ku', 'هەولێر، کوردستان');
        $contact_address_ar = getSettingValue($pdo, 'contact_address_ar', 'أربيل، كردستان');
    } catch (Exception $e) {
        error_log("Error loading footer contact settings: " . $e->getMessage());
    }
}

// Determine which address to show based on language
$display_address = '';
if ($current_lang === 'ku') {
    $display_address = !empty($contact_address_ku) ? $contact_address_ku : $contact_address;
} elseif ($current_lang === 'ar') {
    $display_address = !empty($contact_address_ar) ? $contact_address_ar : $contact_address;
} else {
    $display_address = $contact_address;
}

// Get social media links
$facebook_url = '';
$twitter_url = '';
$instagram_url = '';
$linkedin_url = '';
$youtube_url = '';
$whatsapp_number = '';

if (isset($pdo) && function_exists('getSettingValue')) {
    try {
        $facebook_url = getSettingValue($pdo, 'facebook_url', '');
        $twitter_url = getSettingValue($pdo, 'twitter_url', '');
        $instagram_url = getSettingValue($pdo, 'instagram_url', '');
        $linkedin_url = getSettingValue($pdo, 'linkedin_url', '');
        $youtube_url = getSettingValue($pdo, 'youtube_url', '');
        $whatsapp_number = getSettingValue($pdo, 'whatsapp_number', '');
    } catch (Exception $e) {
        error_log("Error loading footer social media settings: " . $e->getMessage());
    }
}

// Get current year for copyright
$current_year = date('Y');
?>
<!-- Footer -->
<footer class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 text-white overflow-hidden">
    <!-- Decorative Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-0 left-0 w-96 h-96 bg-blue-500 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-green-500 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-purple-500 rounded-full blur-3xl"></div>
    </div>
    
    <!-- Animated Grid Pattern -->
    <div class="absolute inset-0 opacity-5" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
    
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <!-- Main Footer Content -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12 mb-12">
            <!-- Company Info -->
            <div class="lg:col-span-1">
                <div class="mb-6">
                    <!-- Logo/Brand -->
                    <div class="flex items-center space-x-3 rtl:space-x-reverse mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-600 to-green-600 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-400 to-green-400 bg-clip-text text-transparent">
                            <?php echo htmlspecialchars($site_name); ?>
                        </h3>
                    </div>
                    <p class="text-gray-300 leading-relaxed mb-6 text-sm">
                        <?php echo htmlspecialchars($site_description); ?>
                    </p>
                </div>
                
                <!-- Social Media Links -->
                <?php if (!empty($facebook_url) || !empty($instagram_url) || !empty($twitter_url) || !empty($linkedin_url) || !empty($youtube_url) || !empty($whatsapp_number)): ?>
                <div class="flex flex-wrap gap-3">
                    <?php if (!empty($facebook_url) && $facebook_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" rel="noopener noreferrer" 
                       class="group relative w-11 h-11 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-blue-500/50 transition-all duration-300 transform hover:-translate-y-1" 
                       title="Facebook">
                        <i class="fab fa-facebook-f text-sm"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($twitter_url) && $twitter_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" rel="noopener noreferrer" 
                       class="group relative w-11 h-11 bg-gradient-to-br from-sky-500 to-sky-600 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-sky-500/50 transition-all duration-300 transform hover:-translate-y-1" 
                       title="Twitter">
                        <i class="fab fa-twitter text-sm"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($linkedin_url) && $linkedin_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" 
                       class="group relative w-11 h-11 bg-gradient-to-br from-blue-700 to-blue-800 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-blue-700/50 transition-all duration-300 transform hover:-translate-y-1" 
                       title="LinkedIn">
                        <i class="fab fa-linkedin-in text-sm"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($instagram_url) && $instagram_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" rel="noopener noreferrer" 
                       class="group relative w-11 h-11 bg-gradient-to-br from-pink-500 via-purple-500 to-orange-500 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-pink-500/50 transition-all duration-300 transform hover:-translate-y-1" 
                       title="Instagram">
                        <i class="fab fa-instagram text-sm"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($youtube_url) && $youtube_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" rel="noopener noreferrer" 
                       class="group relative w-11 h-11 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-red-500/50 transition-all duration-300 transform hover:-translate-y-1" 
                       title="YouTube">
                        <i class="fab fa-youtube text-sm"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($whatsapp_number)): ?>
                    <a href="https://wa.me/<?php echo str_replace([' ', '-', '(', ')', '+'], '', $whatsapp_number); ?>" target="_blank" rel="noopener noreferrer" 
                       class="group relative w-11 h-11 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center text-white hover:scale-110 hover:shadow-lg hover:shadow-green-500/50 transition-all duration-300 transform hover:-translate-y-1" 
                       title="WhatsApp">
                        <i class="fab fa-whatsapp text-sm"></i>
                        <span class="absolute inset-0 rounded-xl bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></span>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Services -->
            <div>
                <h4 class="text-lg font-bold mb-6 text-white relative inline-block">
                    <?php echo t('our_services'); ?>
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-blue-500 to-green-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></span>
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="pages/public/commercial-residential-design.php" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <span class="w-1.5 h-1.5 bg-blue-500 rounded-full mr-3 rtl:ml-3 rtl:mr-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                            <span class="flex-1"><?php echo t('commercial_design_management'); ?></span>
                            <i class="fas fa-chevron-left text-xs opacity-0 group-hover:opacity-100 transform group-hover:translate-x-1 transition-all duration-300 rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/infrastructure.php" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-3 rtl:ml-3 rtl:mr-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                            <span class="flex-1"><?php echo t('infrastructure_construction'); ?></span>
                            <i class="fas fa-chevron-left text-xs opacity-0 group-hover:opacity-100 transform group-hover:translate-x-1 transition-all duration-300 rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/design-reconstruction.php" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <span class="w-1.5 h-1.5 bg-purple-500 rounded-full mr-3 rtl:ml-3 rtl:mr-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                            <span class="flex-1"><?php echo t('design_reconstruction'); ?></span>
                            <i class="fas fa-chevron-left text-xs opacity-0 group-hover:opacity-100 transform group-hover:translate-x-1 transition-all duration-300 rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/exterior-design.php" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <span class="w-1.5 h-1.5 bg-orange-500 rounded-full mr-3 rtl:ml-3 rtl:mr-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                            <span class="flex-1"><?php echo t('exterior_design_implementation'); ?></span>
                            <i class="fas fa-chevron-left text-xs opacity-0 group-hover:opacity-100 transform group-hover:translate-x-1 transition-all duration-300 rtl:rotate-180"></i>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/interior-design.php" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <span class="w-1.5 h-1.5 bg-pink-500 rounded-full mr-3 rtl:ml-3 rtl:mr-0 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                            <span class="flex-1"><?php echo t('interior_design_implementation'); ?></span>
                            <i class="fas fa-chevron-left text-xs opacity-0 group-hover:opacity-100 transform group-hover:translate-x-1 transition-all duration-300 rtl:rotate-180"></i>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-bold mb-6 text-white">
                    <?php echo t('quick_links'); ?>
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="index.php" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <i class="fas fa-home w-5 text-blue-400 mr-3 rtl:ml-3 rtl:mr-0 group-hover:scale-110 transition-transform duration-300"></i>
                            <span><?php echo t('home'); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php#services" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <i class="fas fa-briefcase w-5 text-green-400 mr-3 rtl:ml-3 rtl:mr-0 group-hover:scale-110 transition-transform duration-300"></i>
                            <span><?php echo t('our_services'); ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="index.php#contact" 
                           class="group flex items-center text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                            <i class="fas fa-envelope w-5 text-purple-400 mr-3 rtl:ml-3 rtl:mr-0 group-hover:scale-110 transition-transform duration-300"></i>
                            <span><?php echo t('contact_us'); ?></span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-bold mb-6 text-white">
                    <?php echo t('contact_info'); ?>
                </h4>
                <div class="space-y-4">
                    <a href="tel:<?php echo str_replace([' ', '-', '(', ')'], '', $contact_phone); ?>" 
                       class="group flex items-start text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3 rtl:ml-3 rtl:mr-0 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-green-500/50 transition-all duration-300">
                            <i class="fas fa-phone text-sm text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium"><?php echo htmlspecialchars($contact_phone); ?></p>
                            <p class="text-xs text-gray-400 mt-1"><?php echo t('call_us') ?? 'Call Us'; ?></p>
                        </div>
                    </a>
                    
                    <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" 
                       class="group flex items-start text-gray-300 hover:text-white transition-all duration-300 hover:translate-x-1 rtl:hover:-translate-x-1">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mr-3 rtl:ml-3 rtl:mr-0 group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-blue-500/50 transition-all duration-300">
                            <i class="fas fa-envelope text-sm text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium break-all"><?php echo htmlspecialchars($contact_email); ?></p>
                            <p class="text-xs text-gray-400 mt-1"><?php echo t('email_us') ?? 'Email Us'; ?></p>
                        </div>
                    </a>
                    
                    <div class="flex items-start text-gray-300">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg flex items-center justify-center mr-3 rtl:ml-3 rtl:mr-0">
                            <i class="fas fa-map-marker-alt text-sm text-white"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium"><?php echo htmlspecialchars($display_address); ?></p>
                            <p class="text-xs text-gray-400 mt-1"><?php echo t('visit_us') ?? 'Visit Us'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="border-t border-gray-700/50 dark:border-gray-600/30 pt-8 mt-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-gray-400 text-sm text-center md:text-left">
                    <p>&copy; <?php echo $current_year; ?> <span class="text-white font-semibold"><?php echo htmlspecialchars($site_name); ?></span>. <?php echo t('all_rights_reserved'); ?></p>
                </div>
                <div class="flex items-center space-x-6 rtl:space-x-reverse text-sm text-gray-400">
                    <span class="flex items-center">
                        <i class="fas fa-heart text-red-500 mr-2 rtl:ml-2 rtl:mr-0 animate-pulse"></i>
                        <?php echo t('made_with_love') ?? 'Made with'; ?> <?php echo t('in_kurdistan') ?? 'in Kurdistan'; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Footer Professional Enhancements */
footer {
    position: relative;
}

footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.5), rgba(16, 185, 129, 0.5), transparent);
}

/* Smooth scroll behavior for anchor links */
footer a[href^="#"] {
    scroll-behavior: smooth;
}

/* Enhanced hover effects */
footer a {
    position: relative;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    footer .grid {
        gap: 2rem;
    }
}
</style>
