<?php
session_start();
require_once 'config/db_conected.php';
require_once 'includes/translations.php';

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('login'); ?> - <?php echo t('construction_company'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center">
    <!-- Language Switcher -->
    <div class="fixed top-4 right-4 z-50">
        <div class="relative">
            <button onclick="toggleLanguageDropdown()" class="flex items-center space-x-2 rtl:space-x-reverse px-4 py-2 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700">
                <span class="text-lg"><?php echo $languages[$current_lang]['flag']; ?></span>
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300"><?php echo $languages[$current_lang]['name']; ?></span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div id="languageDropdown" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg border border-gray-200 dark:border-gray-700 hidden z-50">
                <?php foreach ($languages as $code => $lang): ?>
                    <a href="?lang=<?php echo $code; ?>" class="flex items-center space-x-2 rtl:space-x-reverse px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <span class="text-lg"><?php echo $lang['flag']; ?></span>
                        <span><?php echo $lang['name']; ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Dark Mode Toggle -->
    <div class="fixed top-4 left-4 z-50">
        <button onclick="toggleDarkMode()" class="flex items-center justify-center w-12 h-12 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200" id="darkModeToggle" title="<?php echo t('dark_mode'); ?>">
            <!-- Light Mode Icon (Sun) -->
            <svg id="lightModeIcon" class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <!-- Dark Mode Icon (Moon) -->
            <svg id="darkModeIcon" class="w-6 h-6 text-blue-500 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
            </svg>
        </button>
    </div>

    <!-- Login Form -->
    <div class="max-w-md w-full mx-4">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8">
            <!-- Logo and Title -->
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                    <?php echo t('construction_company'); ?>
                </h1>
                <p class="text-gray-600 dark:text-gray-300">
                    <?php echo t('excellence_in_construction'); ?>
                </p>
            </div>

            <!-- Welcome Message -->
            <div class="text-center mb-6">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    <?php echo t('welcome_back'); ?>
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                    <?php echo t('login'); ?>
                </p>
            </div>

            <!-- Login Form -->
            <form class="space-y-6" method="POST" action="process/login_process.php">
                <div>
                    <label for="email" class="form-label">
                        <i class="fas fa-envelope mr-2"></i>
                        <?php echo t('email'); ?>
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="<?php echo t('email'); ?>"
                        required
                    >
                </div>

                <div>
                    <label for="password" class="form-label">
                        <i class="fas fa-lock mr-2"></i>
                        <?php echo t('password'); ?>
                    </label>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input pr-10" 
                            placeholder="<?php echo t('password'); ?>"
                            required
                        >
                        <button 
                            type="button" 
                            onclick="togglePassword()" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center"
                        >
                            <i id="passwordIcon" class="fas fa-eye text-gray-400"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-end">
                    <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500">
                        <?php echo t('forgot_password'); ?>
                    </a>
                </div>

                <button 
                    type="submit" 
                    class="w-full btn btn-primary text-lg py-3"
                >
                    <i class="fas fa-eye mr-2"></i>
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    <?php echo t('sign_in'); ?>
                </button>
            </form>

            <!-- Register Link -->
            <div class="mt-6 text-center">
                <p class="text-gray-600 dark:text-gray-300">
                    <?php echo t('dont_have_account'); ?>
                    <a href="#" class="text-blue-600 dark:text-blue-400 hover:text-blue-500 font-medium">
                        <?php echo t('create_account'); ?>
                    </a>
                </p>
            </div>

            <!-- Demo Credentials -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                <h3 class="text-sm font-semibold text-blue-800 dark:text-blue-300 mb-2">
                    Demo Credentials:
                </h3>
                <div class="text-xs text-blue-700 dark:text-blue-400 space-y-1">
                    <p><strong>Email:</strong> admin@construction.com</p>
                    <p><strong>Password:</strong> admin123</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Background Elements -->
    <div class="fixed inset-0 -z-10 overflow-hidden">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-600/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-green-600/10 rounded-full blur-3xl"></div>
    </div>

    <script>
        // Language dropdown toggle
        function toggleLanguageDropdown() {
            const dropdown = document.getElementById('languageDropdown');
            dropdown.classList.toggle('hidden');
        }

        // Dark mode toggle
        function toggleDarkMode() {
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            const toggle = document.getElementById('darkModeToggle');
            
            if (body.classList.contains('dark-mode')) {
                // Switch to light mode
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
                toggle.title = '<?php echo t('light_mode'); ?>';
                localStorage.setItem('darkMode', 'false');
            } else {
                // Switch to dark mode
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
                toggle.title = '<?php echo t('dark_mode'); ?>';
                localStorage.setItem('darkMode', 'true');
            }
        }

        // Password visibility toggle
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('passwordIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Initialize dark mode from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode');
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            const toggle = document.getElementById('darkModeToggle');
            
            if (darkMode === 'true') {
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
                toggle.title = '<?php echo t('dark_mode'); ?>';
            } else {
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
                toggle.title = '<?php echo t('light_mode'); ?>';
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const languageDropdown = document.getElementById('languageDropdown');
            if (!event.target.closest('.relative')) {
                languageDropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
