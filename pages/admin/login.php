<?php
// Include login process
require_once '../../process/admin/login.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('admin_login'); ?> - <?php echo t('construction_company'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/login.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="<?php echo t('admin_login_description'); ?>">
    <meta name="robots" content="noindex, nofollow">
</head>
<body class="login-container">
    <!-- Floating Particles -->
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>
    
    <!-- Top Navigation Bar -->
    <div class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between px-4 py-3 bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <!-- Back to Home Link -->
        <a href="../../index.php" class="flex items-center space-x-3 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 border border-gray-300 dark:border-gray-600">
            <i class="fas fa-arrow-left"></i>
            <span><?php echo t('back_to_home'); ?></span>
        </a>
        
        <!-- Dark Mode Toggle -->
        <button 
            onclick="toggleDarkMode()" 
            class="flex items-center justify-center w-10 h-10 rounded-full bg-white dark:bg-gray-800 shadow-sm border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200"
            title="<?php echo t('dark_mode'); ?>"
        >
            <i id="lightModeIcon" class="fas fa-sun text-base"></i>
            <i id="darkModeIcon" class="fas fa-moon text-base hidden"></i>
        </button>
    </div>
    
    <div class="min-h-screen flex items-center justify-center px-2 sm:px-4 lg:px-8 py-4 pt-20">
        <div class="w-full max-w-sm sm:max-w-md space-y-6 sm:space-y-8">
            <!-- Logo and Title -->
            <div class="text-center">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-6">
                    <?php echo t('admin_login'); ?>
                </h1>
            </div>
            
            <!-- Login Form -->
            <div class="login-card rounded-2xl p-8">
                <?php if ($error_message): ?>
                    <div class="error-message rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-3"></i>
                            <span><?php echo $error_message; ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="success-message rounded-lg p-4 mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-3"></i>
                            <span><?php echo $success_message; ?></span>
                        </div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-user mr-2"></i>
                            <?php echo t('username'); ?>
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required 
                            class="form-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="<?php echo t('enter_username'); ?>"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                        >
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            <i class="fas fa-lock mr-2"></i>
                            <?php echo t('password'); ?>
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required 
                                class="form-input w-full px-4 py-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent pr-12"
                                placeholder="<?php echo t('enter_password'); ?>"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                            >
                                <i id="passwordToggleIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    
                    <div>
                        <button 
                            type="submit" 
                            class="btn-login w-full py-3 px-4 rounded-lg text-white font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            <?php echo t('login'); ?>
                        </button>
                    </div>
                </form>
                
                <!-- Additional Info -->
                <div class="mt-8 text-center">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <?php echo t('secure_admin_access'); ?>
                    </div>
                </div>
            </div>
            
            <!-- Footer Copyright -->
            <div class="text-center text-sm text-gray-500 dark:text-gray-400 mt-4">
                <p>&copy; <?php echo date('Y'); ?> <?php echo t('construction_company'); ?>. <?php echo t('all_rights_reserved'); ?>.</p>
            </div>
        </div>
    </div>
    
    <!-- JavaScript -->
    <script src="../../assets/js/admin/login.js"></script>
</body>
</html>