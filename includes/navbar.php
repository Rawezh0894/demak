<?php
// Include shared translations
require_once __DIR__ . '/translations.php';

// Calculate base path to index.php based on where navbar is included from
$current_file = $_SERVER['PHP_SELF'];
$base_path = '';
if (strpos($current_file, '/pages/public/') !== false) {
    // If we're in pages/public/, go up two levels
    $base_path = '../../';
} elseif (strpos($current_file, '/pages/') !== false) {
    // If we're in pages/, go up one level
    $base_path = '../';
}
// If we're in root, base_path stays empty
?>

<nav class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                <div class="flex-shrink-0">
                    <a href="<?php echo $base_path; ?>index.php" class="flex items-center space-x-2 rtl:space-x-reverse">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-green-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white"><?php echo t('construction_company'); ?></span>
                    </a>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-2 rtl:space-x-reverse">
                    <?php
                    $current_page = basename($_SERVER['PHP_SELF']);
                    $current_section = $_GET['section'] ?? '';
                    ?>
                    <a href="<?php echo $base_path; ?>index.php" class="nav-link <?php echo ($current_page == 'index.php' && $current_section == '') ? 'active' : ''; ?>">
                        <?php echo t('home'); ?>
                    </a>
                    <a href="<?php echo $base_path; ?>index.php#services" class="nav-link <?php echo ($current_section == 'services' || (isset($_GET['section']) && $_GET['section'] == 'services')) ? 'active' : ''; ?>">
                        <?php echo t('our_services'); ?>
                    </a>
                    <a href="<?php echo $base_path; ?>index.php#contact" class="nav-link <?php echo ($current_section == 'contact' || (isset($_GET['section']) && $_GET['section'] == 'contact')) ? 'active' : ''; ?>">
                        <?php echo t('contact'); ?>
                    </a>
                </div>
            </div>

            <!-- Right side controls -->
            <div class="flex items-center space-x-4 rtl:space-x-reverse">
                <!-- Sidebar Toggle Button (Mobile Only) -->
                <button id="sidebarToggleBtn" onclick="toggleSidebar()" class="md:hidden relative text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 focus:outline-none focus:text-blue-600 dark:focus:text-blue-400 transition-all duration-300">
                    <!-- Hamburger Icon (Default) -->
                    <svg id="hamburgerIcon" class="h-6 w-6 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <!-- Close Icon (Hidden by default) -->
                    <svg id="closeIcon" class="h-6 w-6 absolute inset-0 transition-all duration-300 opacity-0 rotate-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <!-- Language Switcher -->
                <div class="language-switcher">
                    <button onclick="toggleLanguageDropdown()" class="flex items-center space-x-1 rtl:space-x-reverse px-3 py-2 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                        <?php if (isset($languages[$current_lang]['flag']) && $languages[$current_lang]['flag'] === 'img'): ?>
                            <img src="<?php echo $languages[$current_lang]['flag_path']; ?>" alt="Kurdistan Flag" class="w-5 h-5 object-contain">
                        <?php else: ?>
                            <span class="text-lg"><?php echo $languages[$current_lang]['flag']; ?></span>
                        <?php endif; ?>
                        <span><?php echo $languages[$current_lang]['name']; ?></span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="languageDropdown" class="language-dropdown hidden">
                        <?php foreach ($languages as $code => $lang): ?>
                            <a href="?lang=<?php echo $code; ?>" class="language-option flex items-center space-x-2 rtl:space-x-reverse">
                                <?php if (isset($lang['flag']) && $lang['flag'] === 'img'): ?>
                                    <img src="<?php echo $lang['flag_path']; ?>" alt="Kurdistan Flag" class="w-5 h-5 object-contain">
                                <?php else: ?>
                                    <span class="text-lg"><?php echo $lang['flag']; ?></span>
                                <?php endif; ?>
                                <span><?php echo $lang['name']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Dark Mode Toggle -->
                <button onclick="toggleDarkMode()" class="relative flex items-center justify-center w-10 h-10 rounded-lg text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-all duration-200" id="darkModeToggle" title="<?php echo t('dark_mode'); ?>">
                    <!-- Light Mode Icon (Sun) -->
                    <svg id="lightModeIcon" class="w-5 h-5 absolute inset-0 m-auto transition-all duration-300 ease-in-out opacity-100 rotate-0 scale-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    <!-- Dark Mode Icon (Moon) -->
                    <svg id="darkModeIcon" class="w-5 h-5 absolute inset-0 m-auto transition-all duration-300 ease-in-out opacity-0 rotate-90 scale-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button>

                <!-- User Menu -->
                <div class="relative">
                    <!-- <button onclick="toggleUserMenu()" class="flex items-center space-x-2 rtl:space-x-reverse text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white text-sm font-medium">U</span>
                        </div>
                        <svg class="w-4 h-4 text-gray-700 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button> -->
                    <div id="userMenu" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 hidden z-50">
                        <a href="dashboard.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <?php echo t('dashboard'); ?>
                        </a>
                        <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <?php echo t('profile'); ?>
                        </a>
                        <a href="settings.php" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <?php echo t('settings'); ?>
                        </a>
                        <hr class="border-gray-200 dark:border-gray-700">
                        <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <?php echo t('logout'); ?>
                        </a>
                    </div>
                </div>

            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="mobileMenu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 border-t border-gray-200 dark:border-gray-700">
                <a href="<?php echo $base_path; ?>index.php" class="nav-link block <?php echo ($current_page == 'index.php' && $current_section == '') ? 'active' : ''; ?>">
                    <?php echo t('home'); ?>
                </a>
                <a href="<?php echo $base_path; ?>index.php#services" class="nav-link block <?php echo ($current_section == 'services' || (isset($_GET['section']) && $_GET['section'] == 'services')) ? 'active' : ''; ?>">
                    <?php echo t('our_services'); ?>
                </a>
                <a href="<?php echo $base_path; ?>index.php#contact" class="nav-link block <?php echo ($current_section == 'contact' || (isset($_GET['section']) && $_GET['section'] == 'contact')) ? 'active' : ''; ?>">
                    <?php echo t('contact'); ?>
                </a>
            </div>
        </div>
    </div>
</nav>

<script>
// Language dropdown toggle
function toggleLanguageDropdown() {
    const dropdown = document.getElementById('languageDropdown');
    dropdown.classList.toggle('hidden');
}

// User menu toggle
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('hidden');
}

// Sidebar toggle function (moved from sidebar.php) - Mobile only
function toggleSidebar() {
    // Only toggle on mobile devices (screen width < 768px)
    if (window.innerWidth >= 768) {
        return;
    }
    
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    
    if (!sidebar) return;
    
    // Get direction from sidebar data attribute
    const direction = sidebar.getAttribute('data-direction') || 'ltr';
    const isRTL = direction === 'rtl';
    
    // Use appropriate transform class based on direction
    const hideClass = isRTL ? 'translate-x-full' : '-translate-x-full';
    const isOpen = !sidebar.classList.contains(hideClass);
    
    // Toggle sidebar
    sidebar.classList.toggle(hideClass);
    overlay.classList.toggle('hidden');
    
    // Toggle icons with smooth animation
    if (isOpen) {
        // Closing: Show hamburger, hide close
        hamburgerIcon.classList.remove('opacity-0', 'rotate-90');
        hamburgerIcon.classList.add('opacity-100', 'rotate-0');
        closeIcon.classList.remove('opacity-100', 'rotate-0');
        closeIcon.classList.add('opacity-0', 'rotate-90');
    } else {
        // Opening: Hide hamburger, show close
        hamburgerIcon.classList.remove('opacity-100', 'rotate-0');
        hamburgerIcon.classList.add('opacity-0', 'rotate-90');
        closeIcon.classList.remove('opacity-0', 'rotate-90');
        closeIcon.classList.add('opacity-100', 'rotate-0');
    }
    
    // Adjust main content margin on mobile
    if (sidebar.classList.contains(hideClass)) {
        mainContent.classList.remove('main-content-with-sidebar');
        mainContent.classList.add('main-content-without-sidebar');
    } else {
        mainContent.classList.remove('main-content-without-sidebar');
        mainContent.classList.add('main-content-with-sidebar');
    }
}

// Dark mode toggle - Universal function for navbar toggle button
function toggleDarkMode() {
    const body = document.body;
    
    // Get navbar icon elements
    const lightIcon = document.getElementById('lightModeIcon');
    const darkIcon = document.getElementById('darkModeIcon');
    const toggle = document.getElementById('darkModeToggle');
    
    const isDarkMode = body.classList.contains('dark-mode');
    
    // Toggle body class
    if (isDarkMode) {
        body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'false');
    } else {
        body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'true');
    }
    
    // Update navbar icons
    if (lightIcon && darkIcon && toggle) {
        if (isDarkMode) {
            // Switch to light mode: Show sun, hide moon
            lightIcon.classList.remove('opacity-0', 'rotate-90', 'scale-0');
            lightIcon.classList.add('opacity-100', 'rotate-0', 'scale-100');
            darkIcon.classList.remove('opacity-100', 'rotate-0', 'scale-100');
            darkIcon.classList.add('opacity-0', 'rotate-90', 'scale-0');
            toggle.title = '<?php echo t('light_mode'); ?>';
        } else {
            // Switch to dark mode: Hide sun, show moon
            lightIcon.classList.remove('opacity-100', 'rotate-0', 'scale-100');
            lightIcon.classList.add('opacity-0', 'rotate-90', 'scale-0');
            darkIcon.classList.remove('opacity-0', 'rotate-90', 'scale-0');
            darkIcon.classList.add('opacity-100', 'rotate-0', 'scale-100');
            toggle.title = '<?php echo t('dark_mode'); ?>';
        }
    }
}

// Initialize dark mode from localStorage
document.addEventListener('DOMContentLoaded', function() {
    const darkMode = localStorage.getItem('darkMode');
    const body = document.body;
    
    // Get navbar icon elements
    const lightIcon = document.getElementById('lightModeIcon');
    const darkIcon = document.getElementById('darkModeIcon');
    const toggle = document.getElementById('darkModeToggle');
    
    if (darkMode === 'true') {
        body.classList.add('dark-mode');
        
        // Update navbar icons
        if (lightIcon && darkIcon && toggle) {
            lightIcon.classList.remove('opacity-100', 'rotate-0', 'scale-100');
            lightIcon.classList.add('opacity-0', 'rotate-90', 'scale-0');
            darkIcon.classList.remove('opacity-0', 'rotate-90', 'scale-0');
            darkIcon.classList.add('opacity-100', 'rotate-0', 'scale-100');
        toggle.title = '<?php echo t('dark_mode'); ?>';
        }
    } else {
        body.classList.remove('dark-mode');
        
        // Update navbar icons
        if (lightIcon && darkIcon && toggle) {
            lightIcon.classList.remove('opacity-0', 'rotate-90', 'scale-0');
            lightIcon.classList.add('opacity-100', 'rotate-0', 'scale-100');
            darkIcon.classList.remove('opacity-100', 'rotate-0', 'scale-100');
            darkIcon.classList.add('opacity-0', 'rotate-90', 'scale-0');
        toggle.title = '<?php echo t('light_mode'); ?>';
        }
    }
    
    // Initialize navigation active states
    initializeNavigation();
    initializeSidebarNavigation();
    
    // Initialize sidebar for desktop/mobile
    initializeSidebar();
});

// Initialize sidebar based on screen size
function initializeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.getElementById('mainContent');
    const hamburgerIcon = document.getElementById('hamburgerIcon');
    const closeIcon = document.getElementById('closeIcon');
    
    if (!sidebar || !mainContent) return;
    
    // Get direction from sidebar data attribute
    const direction = sidebar.getAttribute('data-direction') || 'ltr';
    const isRTL = direction === 'rtl';
    const hideClass = isRTL ? 'translate-x-full' : '-translate-x-full';
    
    if (window.innerWidth >= 768) {
        // Desktop: Sidebar hidden (md:hidden class handles this)
        // Main content has no margin on desktop
        if (overlay) overlay.classList.add('hidden');
        mainContent.classList.remove('main-content-with-sidebar');
        mainContent.classList.add('main-content-without-sidebar');
    } else {
        // Mobile: Sidebar hidden by default - use appropriate transform based on direction
        // Ensure sidebar is closed on mobile
        // Remove both possible classes first
        sidebar.classList.remove('-translate-x-full', 'translate-x-full');
        // Add the correct one based on direction
        sidebar.classList.add(hideClass);
        if (overlay) overlay.classList.add('hidden');
        mainContent.classList.remove('main-content-with-sidebar');
        mainContent.classList.add('main-content-without-sidebar');
        
        // Reset icons to default state (hamburger visible, close hidden)
        if (hamburgerIcon && closeIcon) {
            hamburgerIcon.classList.remove('opacity-0', 'rotate-90');
            hamburgerIcon.classList.add('opacity-100', 'rotate-0');
            closeIcon.classList.remove('opacity-100', 'rotate-0');
            closeIcon.classList.add('opacity-0', 'rotate-90');
        }
    }
}

// Handle window resize
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        initializeSidebar();
    }, 100);
});

// Navigation active state management
function initializeNavigation() {
    const navLinks = document.querySelectorAll('.nav-link');
    
    // Remove active class from all links
    navLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Add active class based on current page/section
    const currentPage = window.location.pathname.split('/').pop();
    const currentHash = window.location.hash;
    
    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) {
            return;
        }
        
        // Check if it's the home page
        if (href === 'index.php' && (currentPage === 'index.php' || currentPage === '')) {
            if (!currentHash || currentHash === '#') {
                link.classList.add('active');
            }
        }
        // Check for section matches
        else if (href.includes('#') && currentHash) {
            const section = href.split('#')[1];
            if (currentHash === '#' + section) {
                link.classList.add('active');
            }
        }
    });
}

// Update active states when hash changes
window.addEventListener('hashchange', function() {
    initializeNavigation();
});

// Update active states when page loads
window.addEventListener('load', function() {
    initializeNavigation();
    initializeSidebarNavigation();
});

// Sidebar navigation active state management
function initializeSidebarNavigation() {
    const sidebarLinks = document.querySelectorAll('.sidebar-nav-link');
    
    // Remove active class from all sidebar links
    sidebarLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Add active class based on current page/section
    const currentPage = window.location.pathname.split('/').pop();
    const currentHash = window.location.hash;
    
    sidebarLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (!href) {
            return;
        }
        
        // Check if it's the home page
        if (href === 'index.php' && (currentPage === 'index.php' || currentPage === '')) {
            if (!currentHash || currentHash === '#') {
                link.classList.add('active');
            }
        }
        // Check for section matches
        else if (href.includes('#') && currentHash) {
            const section = href.split('#')[1];
            if (currentHash === '#' + section) {
                link.classList.add('active');
            }
        }
    });
}

// Update sidebar active states when hash changes
window.addEventListener('hashchange', function() {
    initializeSidebarNavigation();
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const languageDropdown = document.getElementById('languageDropdown');
    const userMenu = document.getElementById('userMenu');
    
    if (!event.target.closest('.language-switcher')) {
        languageDropdown.classList.add('hidden');
    }
    
    if (!event.target.closest('.relative')) {
        userMenu.classList.add('hidden');
    }
});

// Smooth scroll for anchor links without page reload
(function() {
    'use strict';
    
    function handleNavLinkClick(e) {
        const link = e.currentTarget || e.target.closest('a');
        if (!link) return;
        
        const href = link.getAttribute('href');
        
        if (!href || !href.includes('#')) {
            return; // Not an anchor link
        }
        
        // Parse the href
        const parts = href.split('#');
        let pagePath = parts[0].trim();
        const sectionId = parts[1];
        
        if (!sectionId) {
            return; // No section ID
        }
        
        // Clean up the page path
        pagePath = pagePath.replace(/^\.\.\/\.\.\//, '').replace(/^\.\.\//, '').replace(/^\//, '').replace(/\/$/, '');
        
        // Get current page
        const currentPath = window.location.pathname;
        const currentPage = currentPath.split('/').pop() || 'index.php';
        const currentPageClean = currentPage || 'index.php';
        
        // Check if we're on index.php or root
        const isIndexPage = currentPageClean === 'index.php' || currentPageClean === '' || currentPath.endsWith('/') || currentPath === '/';
        
        // Check if the link points to index.php or current page
        const pointsToIndex = pagePath === '' || pagePath === 'index.php' || pagePath.endsWith('index.php') || pagePath.includes('index.php');
        const isSamePage = isIndexPage && pointsToIndex;
        
        if (isSamePage) {
            // Prevent default navigation - MUST be first
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            
            // Find the target section
            const targetSection = document.getElementById(sectionId);
            
            if (targetSection) {
                // Calculate offset for sticky navbar (64px height)
                const navbarHeight = 64;
                const targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                
                // Smooth scroll to section
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
                
                // Update URL hash without reload
                if (history.pushState) {
                    history.pushState(null, null, '#' + sectionId);
                } else {
                    window.location.hash = '#' + sectionId;
                }
                
                // Update active navigation states
                if (typeof initializeNavigation === 'function') {
                    initializeNavigation();
                }
                if (typeof initializeSidebarNavigation === 'function') {
                    initializeSidebarNavigation();
                }
                
                // Close mobile menu if open
                const mobileMenu = document.getElementById('mobileMenu');
                if (mobileMenu && !mobileMenu.classList.contains('hidden')) {
                    mobileMenu.classList.add('hidden');
                }
                
                return false;
            }
        }
    }
    
    // Initialize on DOM ready
    function initSmoothScroll() {
        // Handle all navigation links with hash (both desktop and mobile)
        const navLinks = document.querySelectorAll('.nav-link[href*="#"]');
        
        navLinks.forEach(link => {
            // Clone node to remove all existing listeners
            const newLink = link.cloneNode(true);
            link.parentNode.replaceChild(newLink, link);
            
            // Add new listener with capture phase to ensure it runs first
            newLink.addEventListener('click', handleNavLinkClick, true);
        });
    }
    
    // Run on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSmoothScroll);
    } else {
        initSmoothScroll();
    }
    
    // Also run after a short delay to catch any dynamically added links
    setTimeout(initSmoothScroll, 200);
    
    // Handle initial hash on page load
    window.addEventListener('load', function() {
        const currentPath = window.location.pathname;
        const currentPage = currentPath.split('/').pop() || 'index.php';
        const isIndexPage = currentPage === 'index.php' || currentPage === '' || currentPath.endsWith('/') || currentPath === '/';
        
        if (window.location.hash && isIndexPage) {
            const hash = window.location.hash.substring(1);
            const targetSection = document.getElementById(hash);
            
            if (targetSection) {
                // Small delay to ensure page is fully loaded
                setTimeout(() => {
                    const navbarHeight = 64;
                    const targetPosition = targetSection.getBoundingClientRect().top + window.pageYOffset - navbarHeight;
                    
                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });
                }, 100);
            }
        }
    });
})();
</script>
