<?php
// Include shared translations
require_once __DIR__ . '/translations.php';

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);
$current_section = $_GET['section'] ?? '';

$service_pages = [
    'commercial-residential-design.php',
    'infrastructure.php',
    'design-reconstruction.php',
    'exterior-design.php',
    'interior-design.php'
];
$is_services_active = (
    $current_section === 'services' ||
    (isset($_GET['section']) && $_GET['section'] === 'services') ||
    in_array($current_page, $service_pages, true)
);

// Get current language direction
$page_dir = $languages[$current_lang]['dir'];
$is_rtl = ($page_dir === 'rtl');

// Set sidebar position and transform classes based on direction
$sidebar_position = $is_rtl ? 'right-0' : 'left-0';
$sidebar_transform = $is_rtl ? 'translate-x-full' : '-translate-x-full';
// Set border radius for top corners (both left and right)
$sidebar_top_radius = 'rounded-t-lg';
?>

<!-- Sidebar (Mobile Only) -->
<div id="sidebar" class="md:hidden fixed top-20 bottom-0 <?php echo $sidebar_position; ?> z-40 w-64 bg-white dark:bg-gray-800 shadow-lg <?php echo $sidebar_top_radius; ?> transform <?php echo $sidebar_transform; ?> transition-transform duration-300 ease-in-out" data-direction="<?php echo $page_dir; ?>">
    <div class="flex flex-col h-full">
        <!-- Sidebar Header -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-2 rtl:space-x-reverse">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-green-600 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <span class="text-lg font-semibold text-gray-900 dark:text-white"><?php echo t('construction_company'); ?></span>
            </div>
            <!-- Close Button (Mobile Only) -->
            <button onclick="toggleSidebar()" class="md:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            <!-- Home -->
            <a href="index.php" class="sidebar-nav-link <?php echo ($current_page == 'index.php' && $current_section == '') ? 'active' : ''; ?>">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                <span class="whitespace-nowrap truncate"><?php echo t('home'); ?></span>
            </a>

            <!-- Services Dropdown -->
            <div class="sidebar-dropdown">
                <button type="button"
                        class="sidebar-nav-link flex items-center justify-between <?php echo $is_services_active ? 'active' : ''; ?>"
                        data-dropdown-target="servicesSidebarDropdown"
                        onclick="toggleSidebarDropdown('servicesSidebarDropdown')"
                        aria-expanded="<?php echo $is_services_active ? 'true' : 'false'; ?>">
                    <div class="flex items-center space-x-3 rtl:space-x-reverse">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span class="whitespace-nowrap truncate"><?php echo t('our_services'); ?></span>
                    </div>
                    <svg class="dropdown-arrow w-4 h-4 text-gray-400 transition-transform duration-200 <?php echo $is_services_active ? 'transform rotate-90' : ''; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                <div id="servicesSidebarDropdown" class="sidebar-submenu mt-2 space-y-2 pl-4 border-l border-gray-200 dark:border-gray-700 <?php echo $is_services_active ? '' : 'hidden'; ?>">
                    <a href="index.php#services" class="flex items-center text-sm text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors <?php echo ($current_section == 'services' || (isset($_GET['section']) && $_GET['section'] == 'services')) ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'font-medium'; ?>">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-blue-500 dark:bg-blue-400 mr-3 rtl:ml-3 rtl:mr-0"></span>
                        <span class="whitespace-nowrap truncate"><?php echo t('our_services'); ?></span>
                    </a>
                    <a href="pages/public/commercial-residential-design.php" class="flex items-center text-sm text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors <?php echo ($current_page == 'commercial-residential-design.php') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'font-medium'; ?>">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-green-500 dark:bg-green-400 mr-3 rtl:ml-3 rtl:mr-0"></span>
                        <span class="whitespace-nowrap truncate"><?php echo t('commercial_design_management'); ?></span>
                    </a>
                    <a href="pages/public/infrastructure.php" class="flex items-center text-sm text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors <?php echo ($current_page == 'infrastructure.php') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'font-medium'; ?>">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400 mr-3 rtl:ml-3 rtl:mr-0"></span>
                        <span class="whitespace-nowrap truncate"><?php echo t('infrastructure_construction'); ?></span>
                    </a>
                    <a href="pages/public/design-reconstruction.php" class="flex items-center text-sm text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors <?php echo ($current_page == 'design-reconstruction.php') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'font-medium'; ?>">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-purple-500 dark:bg-purple-400 mr-3 rtl:ml-3 rtl:mr-0"></span>
                        <span class="whitespace-nowrap truncate"><?php echo t('design_reconstruction'); ?></span>
                    </a>
                    <a href="pages/public/exterior-design.php" class="flex items-center text-sm text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors <?php echo ($current_page == 'exterior-design.php') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'font-medium'; ?>">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-orange-500 dark:bg-orange-400 mr-3 rtl:ml-3 rtl:mr-0"></span>
                        <span class="whitespace-nowrap truncate"><?php echo t('exterior_design_implementation'); ?></span>
                    </a>
                    <a href="pages/public/interior-design.php" class="flex items-center text-sm text-gray-800 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400 transition-colors <?php echo ($current_page == 'interior-design.php') ? 'text-blue-600 dark:text-blue-400 font-semibold' : 'font-medium'; ?>">
                        <span class="inline-block w-1.5 h-1.5 rounded-full bg-pink-500 dark:bg-pink-400 mr-3 rtl:ml-3 rtl:mr-0"></span>
                        <span class="whitespace-nowrap truncate"><?php echo t('interior_design_implementation'); ?></span>
                    </a>
                </div>
            </div>

            <!-- Contact -->
            <a href="index.php#contact" class="sidebar-nav-link <?php echo ($current_section == 'contact' || (isset($_GET['section']) && $_GET['section'] == 'contact')) ? 'active' : ''; ?>">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                <span class="whitespace-nowrap truncate"><?php echo t('contact'); ?></span>
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="px-4 py-4 border-t border-gray-200 dark:border-gray-700">
            <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                © 2024 <?php echo t('construction_company'); ?>
            </div>
        </div>
    </div>
</div>

<!-- Sidebar Overlay (Mobile Only) -->
<div id="sidebarOverlay" class="md:hidden fixed top-20 bottom-0 left-0 right-0 bg-black bg-opacity-50 z-30 hidden" onclick="toggleSidebar()"></div>

<script>
function toggleSidebarDropdown(id) {
    const menu = document.getElementById(id);
    const button = document.querySelector(`[data-dropdown-target="${id}"]`);
    if (!menu || !button) {
        return;
    }

    const isHidden = menu.classList.contains('hidden');
    menu.classList.toggle('hidden');
    button.setAttribute('aria-expanded', isHidden ? 'true' : 'false');

    const arrow = button.querySelector('.dropdown-arrow');
    if (arrow) {
        arrow.classList.toggle('transform', true);
        arrow.classList.toggle('rotate-90', isHidden);
    }
}
</script>