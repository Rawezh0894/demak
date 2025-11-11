<?php
// Include the main settings management logic
require_once '../../process/settings_management/settings_management.php';
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی ڕێکخستنەکان - پانێڵی ئەدمین</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'rabar': ['Rabar', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Meta tags -->
    <meta name="robots" content="noindex, nofollow">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <style>
        .back-button {
            position: fixed;
            top: 6rem;
            right: 2rem;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 40;
        }
        
        .back-button:hover {
            background: rgba(59, 130, 246, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        }
        
        .dark-mode .back-button {
            background: rgba(96, 165, 250, 0.2);
            border-color: rgba(96, 165, 250, 0.3);
            color: #60a5fa;
        }
        
        .dark-mode .back-button:hover {
            background: rgba(96, 165, 250, 0.3);
        }
        
        .main-content-without-navbar {
            padding-top: 2rem;
        }
        
        /* Top toolbar */
        .top-toolbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 0.75rem 1rem;
            background: rgba(255, 255, 255, 0.75);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0,0,0,0.06);
            z-index: 50;
        }
        .dark-mode .top-toolbar {
            background: rgba(17, 24, 39, 0.75);
            border-bottom-color: rgba(255,255,255,0.08);
        }
        .toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.875rem;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.9);
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .toolbar-btn:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,0.08); }
        .dark-mode .toolbar-btn { 
            background: rgba(30,41,59,0.9); 
            border-color: rgba(255,255,255,0.12);
            color: #e2e8f0;
        }
        .dark-mode .toolbar-btn:hover {
            background: rgba(51, 65, 85, 0.9);
        }
        .toolbar-spacer { height: 64px; }
        
        .setting-group {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .dark-mode .setting-group {
            background: rgba(30, 41, 59, 0.95);
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .setting-item {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease;
        }
        
        .setting-item:last-child {
            border-bottom: none;
        }
        
        .setting-item:hover {
            background-color: rgba(0, 0, 0, 0.02);
        }
        
        .dark-mode .setting-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .setting-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            background: white;
            transition: all 0.3s ease;
        }
        
        .dark-mode .setting-input {
            background: rgba(17, 24, 39, 0.8);
            border-color: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
        }
        
        .setting-input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
    </style>
</head>
<body class="admin-container">
    <!-- Top Toolbar -->
    <div class="top-toolbar">
        <a href="admin_panel.php" class="toolbar-btn">
            <i class="fas fa-arrow-right"></i>
            <span class="text-sm">گەڕانەوە</span>
        </a>
        <button onclick="toggleDarkMode()" class="toolbar-btn">
            <i id="lightModeIcon" class="fas fa-sun text-yellow-500"></i>
            <i id="darkModeIcon" class="fas fa-moon text-blue-500 hidden"></i>
            <span class="text-sm">مۆد</span>
        </button>
    </div>
    <div class="toolbar-spacer"></div>
    
    <!-- Back Button -->
    <a href="admin_panel.php" class="back-button">
        <i class="fas fa-arrow-right mr-2"></i>
        گەڕانەوە بۆ پانێڵی سەرەکی
    </a>
    
    <!-- Main Content -->
    <div id="mainContent" class="main-content-without-navbar">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-cog text-white text-3xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            بەڕێوەبردنی ڕێکخستنەکان
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 mb-6">
                            بەڕێوەبردنی ڕێکخستنەکانی وێبسایت
                        </p>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span><?php echo $success_message; ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span><?php echo $error_message; ?></span>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if (isset($settings_error) && $settings_error): ?>
                <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <div class="flex-1">
                            <span class="font-semibold"><?php echo $settings_error; ?></span>
                            <div class="mt-2">
                                <a href="../../database/settings_table.sql" download class="text-yellow-800 underline hover:text-yellow-900">
                                    <i class="fas fa-download mr-2"></i>
                                    داونلۆدکردنی SQL فایل
                                </a>
                                <span class="mx-2">|</span>
                                <button onclick="createSettingsTable()" class="text-yellow-800 underline hover:text-yellow-900">
                                    <i class="fas fa-database mr-2"></i>
                                    <?php echo (isset($settings_by_group) && !empty($settings_by_group)) ? 'نوێکردنەوەی تەیبڵی settings' : 'دروستکردنی تەیبڵی settings'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Settings Form -->
            <form id="settingsForm" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <input type="hidden" name="action" value="update_settings">
                <input type="hidden" name="bulk_update" value="1">
                
                <?php if (!empty($settings_by_group)): ?>
                    <?php foreach ($settings_by_group as $group => $group_settings): ?>
                        <div class="setting-group">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <i class="fas fa-folder mr-3 text-indigo-600"></i>
                                <?php echo $group_names[$group] ?? ucfirst($group); ?>
                            </h2>
                            
                            <?php foreach ($group_settings as $setting): ?>
                                <div class="setting-item">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- Setting Key and Description -->
                                        <div class="md:col-span-1">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                <?php echo htmlspecialchars($setting['key']); ?>
                                            </label>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                <?php echo htmlspecialchars($setting['description_ku'] ?? $setting['description'] ?? ''); ?>
                                            </p>
                                            <span class="inline-block px-2 py-1 text-xs rounded <?php echo $setting['is_public'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200'; ?>">
                                                <?php echo $setting['is_public'] ? 'گشتی' : 'تایبەت'; ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Setting Value -->
                                        <div class="md:col-span-2">
                                            <?php if ($setting['type'] === 'boolean'): ?>
                                                <div class="flex items-center gap-4">
                                                    <label class="relative inline-flex items-center cursor-pointer">
                                                        <input type="hidden" name="settings[<?php echo $setting['id']; ?>][value]" value="0">
                                                        <input type="checkbox" 
                                                               name="settings[<?php echo $setting['id']; ?>][value]" 
                                                               value="1"
                                                               <?php echo ($setting['value'] == '1' || $setting['value'] === 'true') ? 'checked' : ''; ?>
                                                               class="sr-only peer">
                                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:right-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                                        <span class="mr-3 text-sm font-medium text-gray-700 dark:text-gray-300">
                                                            <?php echo ($setting['value'] == '1' || $setting['value'] === 'true') ? 'چالاک' : 'ناچالاک'; ?>
                                                        </span>
                                                    </label>
                                                </div>
                                            <?php elseif ($setting['type'] === 'file'): ?>
                                                <div class="space-y-2">
                                                    <?php if (!empty($setting['value'])): ?>
                                                        <div class="mb-2">
                                                            <img src="../../<?php echo htmlspecialchars($setting['value']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($setting['key']); ?>"
                                                                 class="max-w-xs h-20 object-contain border border-gray-300 dark:border-gray-600 rounded">
                                                        </div>
                                                    <?php endif; ?>
                                                    <input type="file" 
                                                           name="settings[<?php echo $setting['id']; ?>][value]"
                                                           accept="image/*"
                                                           class="setting-input">
                                                </div>
                                            <?php elseif ($setting['type'] === 'number'): ?>
                                                <input type="number" 
                                                       name="settings[<?php echo $setting['id']; ?>][value]"
                                                       value="<?php echo htmlspecialchars($setting['value']); ?>"
                                                       class="setting-input">
                                            <?php else: ?>
                                                <textarea name="settings[<?php echo $setting['id']; ?>][value]"
                                                          rows="2"
                                                          class="setting-input"><?php echo htmlspecialchars($setting['value']); ?></textarea>
                                            <?php endif; ?>
                                            
                                            <!-- Hidden fields -->
                                            <input type="hidden" name="settings[<?php echo $setting['id']; ?>][description]" value="<?php echo htmlspecialchars($setting['description'] ?? ''); ?>">
                                            <input type="hidden" name="settings[<?php echo $setting['id']; ?>][description_ku]" value="<?php echo htmlspecialchars($setting['description_ku'] ?? ''); ?>">
                                            <input type="hidden" name="settings[<?php echo $setting['id']; ?>][description_ar]" value="<?php echo htmlspecialchars($setting['description_ar'] ?? ''); ?>">
                                            <input type="hidden" name="settings[<?php echo $setting['id']; ?>][is_public]" value="<?php echo $setting['is_public']; ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <!-- Submit Button -->
                    <div class="text-center mt-8">
                        <button type="submit" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-12 py-4 rounded-xl font-medium transition-all duration-200 flex items-center justify-center mx-auto shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-save mr-3"></i>
                            پاشەکەوتکردنی هەموو ڕێکخستنەکان
                        </button>
                    </div>
                <?php else: ?>
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-8">
                            <i class="fas fa-cog text-gray-400 text-4xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            هیچ ڕێکخستنێک نەدۆزرایەوە
                        </h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">
                            تکایە سەرەتا تەیبڵی settings دروست بکە
                        </p>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <script>
        // Dark mode toggle function
        function toggleDarkMode() {
            const html = document.documentElement;
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            
            if (html.classList.contains('dark') || body.classList.contains('dark-mode')) {
                html.classList.remove('dark');
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
                localStorage.setItem('darkMode', 'false');
            } else {
                html.classList.add('dark');
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
                localStorage.setItem('darkMode', 'true');
            }
        }
        
        // Initialize dark mode from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode');
            const html = document.documentElement;
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            
            if (darkMode === 'true') {
                html.classList.add('dark');
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
            } else {
                html.classList.remove('dark');
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            }
        });
        
        // Handle form submission
        document.getElementById('settingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3"></i> پاشەکەوتکردن...';
            
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg';
                    successDiv.innerHTML = '<div class="flex items-center"><i class="fas fa-check-circle mr-3"></i><span>' + data.message + '</span></div>';
                    document.querySelector('.container').insertBefore(successDiv, document.querySelector('.container').firstChild);
                    
                    // Scroll to top
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    
                    // Reload page after 2 seconds
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    alert('هەڵە: ' + data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('هەڵەیەک ڕوویدا. تکایە دووبارە هەوڵ بەرەوە.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
        
        // Function to create settings table
        function createSettingsTable() {
            if (!confirm('ئایا دڵنیایت لە دروستکردنی تەیبڵی settings؟')) {
                return;
            }
            
            // Show loading
            const btn = event.target;
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> دروستکردن...';
            
            fetch('../../process/settings_management/create_table.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    csrf_token: '<?php echo $_SESSION['csrf_token']; ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('تەیبڵی settings بە سەرکەوتوویی دروست کرا!');
                    window.location.reload();
                } else {
                    alert('هەڵە: ' + data.message);
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('هەڵەیەک ڕوویدا. تکایە دووبارە هەوڵ بەرەوە.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        }
    </script>
</body>
</html>




