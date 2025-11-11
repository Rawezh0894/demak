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
<footer class="bg-gray-800 dark:bg-gray-900 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div>
                <h3 class="text-xl font-bold mb-4"><?php echo htmlspecialchars($site_name); ?></h3>
                <p class="text-gray-300 mb-4">
                    <?php echo htmlspecialchars($site_description); ?>
                </p>
                <?php if (!empty($facebook_url) || !empty($instagram_url) || !empty($twitter_url) || !empty($linkedin_url) || !empty($youtube_url) || !empty($whatsapp_number)): ?>
                <div class="flex space-x-4 rtl:space-x-reverse">
                    <?php if (!empty($facebook_url) && $facebook_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($facebook_url); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($twitter_url) && $twitter_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($twitter_url); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($linkedin_url) && $linkedin_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($linkedin_url); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors" title="LinkedIn">
                        <i class="fab fa-linkedin-in"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($instagram_url) && $instagram_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($instagram_url); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($youtube_url) && $youtube_url !== '#'): ?>
                    <a href="<?php echo htmlspecialchars($youtube_url); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php if (!empty($whatsapp_number)): ?>
                    <a href="https://wa.me/<?php echo str_replace([' ', '-', '(', ')', '+'], '', $whatsapp_number); ?>" target="_blank" rel="noopener noreferrer" class="text-gray-300 hover:text-white transition-colors" title="WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Services -->
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo t('our_services'); ?></h4>
                <ul class="space-y-2 text-gray-300">
                    <li>
                        <a href="pages/public/commercial-residential-design.php" class="hover:text-white transition-colors">
                            <?php echo t('commercial_design_management'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/infrastructure.php" class="hover:text-white transition-colors">
                            <?php echo t('infrastructure_construction'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/design-reconstruction.php" class="hover:text-white transition-colors">
                            <?php echo t('design_reconstruction'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/exterior-design.php" class="hover:text-white transition-colors">
                            <?php echo t('exterior_design_implementation'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="pages/public/interior-design.php" class="hover:text-white transition-colors">
                            <?php echo t('interior_design_implementation'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo t('quick_links'); ?></h4>
                <ul class="space-y-2 text-gray-300">
                    <li>
                        <a href="index.php" class="hover:text-white transition-colors">
                            <?php echo t('home'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="index.php#services" class="hover:text-white transition-colors">
                            <?php echo t('our_services'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="index.php#contact" class="hover:text-white transition-colors">
                            <?php echo t('contact_us'); ?>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div>
                <h4 class="text-lg font-semibold mb-4"><?php echo t('contact_info'); ?></h4>
                <div class="space-y-2 text-gray-300">
                    <p>
                        <i class="fas fa-phone mr-2 rtl:ml-2 rtl:mr-0"></i>
                        <a href="tel:<?php echo str_replace([' ', '-', '(', ')'], '', $contact_phone); ?>" class="hover:text-white transition-colors">
                            <?php echo htmlspecialchars($contact_phone); ?>
                        </a>
                    </p>
                    <p>
                        <i class="fas fa-envelope mr-2 rtl:ml-2 rtl:mr-0"></i>
                        <a href="mailto:<?php echo htmlspecialchars($contact_email); ?>" class="hover:text-white transition-colors break-all">
                            <?php echo htmlspecialchars($contact_email); ?>
                        </a>
                    </p>
                    <p>
                        <i class="fas fa-map-marker-alt mr-2 rtl:ml-2 rtl:mr-0"></i>
                        <?php echo htmlspecialchars($display_address); ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-300">
            <p>&copy; <?php echo $current_year; ?> <?php echo htmlspecialchars($site_name); ?>. <?php echo t('all_rights_reserved'); ?></p>
        </div>
    </div>
</footer>
