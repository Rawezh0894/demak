<?php
// Include the main commercial residential design management logic
require_once '../../process/commercial_residential_design/commercial_residential_design.php';

// Force Kurdish language for admin panel
$current_lang = 'ku';
$page_dir = 'rtl';
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بەڕێوەبردنی دیزاینی بینای بازرگانی و ڤێلا و خانوو - پانێڵی ئەدمین</title>
    
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
        .dark-mode .toolbar-btn { background: rgba(30,41,59,0.9); border-color: rgba(255,255,255,0.12); }
        .toolbar-spacer { height: 64px; }

        /* Professional Action Buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.9);
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
        }
        .action-btn:focus,
        .action-btn:active {
            outline: none;
            transform: translateY(0);
        }
        .action-btn-edit {
            color: #3b82f6;
            border-color: rgba(59, 130, 246, 0.2);
            background: rgba(59, 130, 246, 0.05);
        }
        .action-btn-edit:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .action-btn-edit:focus,
        .action-btn-edit:active {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .action-btn-delete {
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.2);
            background: rgba(239, 68, 68, 0.05);
        }
        .action-btn-delete:hover {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
        }
        .action-btn-delete:focus,
        .action-btn-delete:active {
            background: rgba(239, 68, 68, 0.1);
            border-color: rgba(239, 68, 68, 0.3);
        }
        .dark-mode .action-btn {
            background: rgba(30,41,59,0.9);
            border-color: rgba(255,255,255,0.12);
        }
        .dark-mode .action-btn-edit {
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.3);
            background: rgba(96, 165, 250, 0.1);
        }
        .dark-mode .action-btn-edit:hover {
            background: rgba(96, 165, 250, 0.15);
            border-color: rgba(96, 165, 250, 0.4);
        }
        .dark-mode .action-btn-delete {
            color: #f87171;
            border-color: rgba(248, 113, 113, 0.3);
            background: rgba(248, 113, 113, 0.1);
        }
        .dark-mode .action-btn-delete:hover {
            background: rgba(248, 113, 113, 0.15);
            border-color: rgba(248, 113, 113, 0.4);
        }

        /* Professional Pagination Styles */
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1rem;
            border-radius: 12px;
            border: 1px solid rgba(59, 130, 246, 0.2);
            background: rgba(255, 255, 255, 0.9);
            color: #3b82f6;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
        }
        
        .pagination-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .pagination-btn:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.15);
        }
        
        .pagination-btn-number {
            min-width: 2.5rem;
            height: 2.5rem;
        }
        
        .pagination-btn-active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-color: #3b82f6;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
            font-weight: 600;
        }
        
        .pagination-btn-active:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.4);
        }
        
        .pagination-btn-disabled {
            opacity: 0.5;
            cursor: not-allowed;
            background: rgba(156, 163, 175, 0.1);
            border-color: rgba(156, 163, 175, 0.2);
            color: rgba(107, 114, 128, 0.5);
        }
        
        .pagination-btn-prev,
        .pagination-btn-next {
            padding: 0.625rem 1.25rem;
        }
        
        .dark-mode .pagination-btn {
            background: rgba(30, 41, 59, 0.9);
            border-color: rgba(96, 165, 250, 0.3);
            color: #60a5fa;
        }
        
        .dark-mode .pagination-btn:hover:not(:disabled) {
            background: rgba(96, 165, 250, 0.15);
            border-color: rgba(96, 165, 250, 0.4);
        }
        
        .dark-mode .pagination-btn-active {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border-color: #3b82f6;
        }
        
        .dark-mode .pagination-btn-active:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }
        
        .dark-mode .pagination-btn-disabled {
            background: rgba(55, 65, 81, 0.3);
            border-color: rgba(75, 85, 99, 0.3);
            color: rgba(156, 163, 175, 0.5);
        }

        /* Modal Action Buttons */
        .modal-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            border: 1px solid rgba(0,0,0,0.1);
            background: rgba(255,255,255,0.9);
            box-shadow: 0 6px 18px rgba(0,0,0,0.06);
            transition: all 0.2s ease;
            font-weight: 500;
            font-size: 0.875rem;
            cursor: pointer;
        }
        .modal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
        }
        .modal-btn:focus,
        .modal-btn:active {
            outline: none;
            transform: translateY(0);
        }
        .modal-btn-cancel {
            color: #6b7280;
            border-color: rgba(107, 114, 128, 0.2);
            background: rgba(107, 114, 128, 0.05);
        }
        .modal-btn-cancel:hover {
            background: rgba(107, 114, 128, 0.1);
            border-color: rgba(107, 114, 128, 0.3);
        }
        .modal-btn-save {
            color: #3b82f6;
            border-color: rgba(59, 130, 246, 0.2);
            background: rgba(59, 130, 246, 0.05);
        }
        .modal-btn-save:hover {
            background: rgba(59, 130, 246, 0.1);
            border-color: rgba(59, 130, 246, 0.3);
        }
        .dark-mode .modal-btn {
            background: rgba(30,41,59,0.9);
            border-color: rgba(255,255,255,0.12);
        }
        .dark-mode .modal-btn-cancel {
            color: #9ca3af;
            border-color: rgba(156, 163, 175, 0.3);
            background: rgba(156, 163, 175, 0.1);
        }
        .dark-mode .modal-btn-cancel:hover {
            background: rgba(156, 163, 175, 0.15);
            border-color: rgba(156, 163, 175, 0.4);
        }
        .dark-mode .modal-btn-save {
            color: #60a5fa;
            border-color: rgba(96, 165, 250, 0.3);
            background: rgba(96, 165, 250, 0.1);
        }
        .dark-mode .modal-btn-save:hover {
            background: rgba(96, 165, 250, 0.15);
            border-color: rgba(96, 165, 250, 0.4);
        }

        /* Upload dropzone */
        .upload-dropzone {
            border: 2px dashed rgba(59, 130, 246, 0.35);
            background: rgba(59, 130, 246, 0.03);
            border-radius: 16px;
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .upload-dropzone:hover {
            background: rgba(59, 130, 246, 0.06);
            border-color: rgba(59, 130, 246, 0.55);
            box-shadow: 0 10px 24px rgba(59,130,246,0.08);
        }
        .dark-mode .upload-dropzone {
            border-color: rgba(96, 165, 250, 0.35);
            background: rgba(96, 165, 250, 0.05);
        }
        .dark-mode .upload-dropzone:hover {
            background: rgba(96, 165, 250, 0.09);
            border-color: rgba(96, 165, 250, 0.6);
        }
        .upload-input-overlay {
            position: absolute; inset: 0; opacity: 0; cursor: pointer;
        }
        .thumb-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 0.5rem; }
        .thumb-item { position: relative; border-radius: 12px; overflow: hidden; }
        .thumb-item img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Top Toolbar -->
    <div class="top-toolbar">
        <button onclick="toggleDarkMode()" class="toolbar-btn">
            <i id="lightModeIcon" class="fas fa-sun text-yellow-500"></i>
            <i id="darkModeIcon" class="fas fa-moon text-blue-500 hidden"></i>
            <span class="text-sm text-gray-700 dark:text-gray-200">مۆد</span>
        </button>
        <a href="admin_panel.php" class="toolbar-btn text-blue-600 dark:text-blue-400 border-blue-200">
            <i class="fas fa-arrow-left"></i>
            <span class="text-sm">گەڕانەوە بۆ پانێڵی ئەدمین</span>
        </a>
    </div>
    <div class="toolbar-spacer"></div>

    
    <!-- Main Content -->
    <div id="mainContent" class="main-content-without-navbar">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8">
                    <div class="flex flex-col items-center">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-green-500 rounded-2xl flex items-center justify-center mb-6">
                            <i class="fas fa-building text-white text-3xl"></i>
                        </div>
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            بەڕێوەبردنی دیزاینی بینای بازرگانی و ڤێلا و خانوو
                        </h1>
                        <p class="text-xl text-gray-600 dark:text-gray-400 mb-6">
                            دیزاین و سەرپەرشتی کردنی بینای بازرگانی و ڤێلا و خانوو
                        </p>
                        <button onclick="openAddProjectModal()" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-medium transition-all duration-200 flex items-center shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-plus mr-3"></i>
                            زیادکردنی پڕۆژەی نوێ
                        </button>
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

            <!-- Filters and Search -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                    <i class="fas fa-filter mr-3 text-blue-600"></i>
                    گەڕان و پاڵاوتن
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Search -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            گەڕانی پڕۆژەکان
                        </label>
                        <div class="relative">
                            <input type="text" 
                                   id="searchInput"
                                   class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-lg"
                                   placeholder="گەڕانی پڕۆژەکان">
                            <i class="fas fa-search absolute left-4 top-4 text-gray-400 text-lg"></i>
                        </div>
                    </div>
                    
                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            پاڵاوتن بەپێی پۆل
                        </label>
                        <select id="categoryFilter"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-lg">
                            <option value="">هەموو پۆلەکان</option>
                            <?php 
                            if (!empty($commercial_residential_categories)): 
                                foreach ($commercial_residential_categories as $key => $category): 
                                    $categoryTitle = isset($category['title_ku']) && !empty($category['title_ku']) 
                                        ? $category['title_ku'] 
                                        : (isset($category['title']) ? $category['title'] : 'Unknown');
                            ?>
                            <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                            <?php 
                                endforeach; 
                            else: 
                            ?>
                            <!-- Fallback options if categories are not loaded -->
                            <option value="commercial">بینای بازرگانی</option>
                            <option value="villa">باڵەخانە</option>
                            <option value="house">خانوو</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Sort -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            ڕیزکردن بەپێی
                        </label>
                        <select id="sortSelect"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-lg">
                            <option value="name_asc">ناو (A-Z)</option>
                            <option value="name_desc">ناو (Z-A)</option>
                            <option value="price_asc">نرخ (کەمەوە)</option>
                            <option value="price_desc">نرخ (زیادەوە)</option>
                            <option value="date_created">ڕۆژی دروستکردن</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Projects Grid -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 text-center">
                    <i class="fas fa-th-large mr-3 text-blue-600"></i>
                    لیستی پڕۆژەکان
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="projectsGrid">
                <?php if (!empty($commercial_residential_projects)): ?>
                    <?php foreach ($commercial_residential_projects as $project): ?>
                    <?php
                    // Get main image
                    $main_image = '';
                    if (!empty($project['images'])) {
                        foreach ($project['images'] as $img) {
                            if ($img['is_main'] == 1) {
                                $main_image = $img['image_path'];
                                break;
                            }
                        }
                        if (empty($main_image) && !empty($project['images'])) {
                            $main_image = $project['images'][0]['image_path'];
                        }
                    }
                    ?>
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden project-card transition-all duration-300 hover:shadow-2xl hover:-translate-y-2" 
                         data-category="<?php echo $project['category_key']; ?>" 
                         data-name="<?php echo strtolower($project['name']); ?>"
                         data-price="<?php echo $project['price']; ?>">
                        <!-- Project Image -->
                        <div class="relative h-56 bg-gray-200 dark:bg-gray-700">
                            <?php if ($main_image): ?>
                                <img src="../../<?php echo $main_image; ?>" 
                                     alt="<?php echo $project['name']; ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-5xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-4 right-4">
                                <span class="bg-blue-600 text-white px-3 py-2 rounded-xl text-sm font-medium shadow-lg">
                                    <?php echo $project['category_title_ku'] ?? $project['category_title']; ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Project Content -->
                        <div class="p-8">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">
                                <?php echo $project['name']; ?>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400 text-base mb-6 line-clamp-2">
                                <?php echo $project['description']; ?>
                            </p>
                            
                            <!-- Project Info -->
                            <div class="space-y-2 mb-6">
                                <div class="flex items-center justify-between text-base">
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-ruler-combined mr-2 text-green-600"></i>
                                        <span class="font-semibold">ڕووبەر: <?php echo preg_replace('/[^\d.,]/', '', $project['area']); ?> م²</span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-base">
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-layer-group mr-2 text-blue-600"></i>
                                        <span class="font-semibold">نهۆم: <?php echo $project['floors']; ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center justify-between text-base">
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-dollar-sign mr-2 text-green-600"></i>
                                        <span class="font-semibold"><?php echo $project['price']; ?></span>
                                    </div>
                                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                                        <i class="fas fa-clock mr-2 text-blue-600"></i>
                                        <span class="font-semibold"><?php echo $project['duration']; ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex gap-3">
                                <button onclick="editProject(<?php echo $project['id']; ?>)" 
                                        class="flex-1 action-btn action-btn-edit">
                                    <i class="fas fa-edit"></i>
                                    <span>دەستکاری</span>
                                </button>
                                <button onclick="deleteProject(<?php echo $project['id']; ?>)" 
                                        class="action-btn action-btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-16">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-12">
                            <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-8">
                                <i class="fas fa-folder-open text-gray-400 text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                            هیچ پڕۆژەیەک نەدۆزرایەوە
                        </h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-8 text-lg">
                            هێشتا هیچ پڕۆژەیەک زیاد نەکراوە
                        </p>
                        <button onclick="openAddProjectModal()" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-plus mr-3"></i>
                            زیادکردنی پڕۆژەی نوێ
                        </button>
                        </div>
                    </div>
                <?php endif; ?>
                </div>
                
                <!-- Pagination Container -->
                <div id="paginationContainer" class="mt-8">
                    <?php if (isset($total_pages) && $total_pages > 1): ?>
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium"><?php echo isset($total_projects) ? $total_projects : 0; ?></span> پڕۆژە لە کۆی 
                            <span class="font-medium"><?php echo isset($total_pages) ? $total_pages : 0; ?></span> پەڕە
                        </div>
                        <div class="flex items-center gap-2">
                            <?php $current_page = isset($current_page) ? $current_page : 1; ?>
                            <?php if ($current_page > 1): ?>
                            <button onclick="updateProjectsList(<?php echo $current_page - 1; ?>)" 
                                    class="pagination-btn pagination-btn-prev">
                                <i class="fas fa-chevron-right"></i>
                                <span>پێشوو</span>
                            </button>
                            <?php else: ?>
                            <button disabled class="pagination-btn pagination-btn-disabled">
                                <i class="fas fa-chevron-right"></i>
                                <span>پێشوو</span>
                            </button>
                            <?php endif; ?>
                            
                            <?php
                            $maxVisiblePages = 5;
                            $startPage = max(1, $current_page - floor($maxVisiblePages / 2));
                            $endPage = min($total_pages, $startPage + $maxVisiblePages - 1);
                            if ($endPage - $startPage < $maxVisiblePages - 1) {
                                $startPage = max(1, $endPage - $maxVisiblePages + 1);
                            }
                            
                            if ($startPage > 1): ?>
                                <button onclick="updateProjectsList(1)" 
                                        class="pagination-btn pagination-btn-number <?php echo $current_page == 1 ? 'pagination-btn-active' : ''; ?>">
                                    1
                                </button>
                                <?php if ($startPage > 2): ?>
                                    <span class="px-2 text-gray-400">...</span>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <button onclick="updateProjectsList(<?php echo $i; ?>)" 
                                        class="pagination-btn pagination-btn-number <?php echo $current_page == $i ? 'pagination-btn-active' : ''; ?>">
                                    <?php echo $i; ?>
                                </button>
                            <?php endfor; ?>
                            
                            <?php if ($endPage < $total_pages): ?>
                                <?php if ($endPage < $total_pages - 1): ?>
                                    <span class="px-2 text-gray-400">...</span>
                                <?php endif; ?>
                                <button onclick="updateProjectsList(<?php echo $total_pages; ?>)" 
                                        class="pagination-btn pagination-btn-number <?php echo $current_page == $total_pages ? 'pagination-btn-active' : ''; ?>">
                                    <?php echo $total_pages; ?>
                                </button>
                            <?php endif; ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                            <button onclick="updateProjectsList(<?php echo $current_page + 1; ?>)" 
                                    class="pagination-btn pagination-btn-next">
                                <span>دواتر</span>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <?php else: ?>
                            <button disabled class="pagination-btn pagination-btn-disabled">
                                <span>دواتر</span>
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- No Projects Found -->
            <div id="noProjectsFound" class="hidden text-center py-16">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-12">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-8">
                        <i class="fas fa-search text-gray-400 text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                    <?php echo t('no_projects_found'); ?>
                </h3>
                    <p class="text-gray-600 dark:text-gray-400 text-lg">
                    <?php echo t('no_projects_found'); ?>
                </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Project Modal -->
    <div id="projectModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white" id="modalTitle">
                            زیادکردنی پڕۆژەی نوێ
                        </h2>
                        <button onclick="closeProjectModal()" 
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Modal Form -->
                    <form id="projectForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                        <input type="hidden" name="action" id="formAction" value="add_project">
                        <input type="hidden" name="project_id" id="projectId" value="">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Project Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    ناوی پڕۆژە *
                                </label>
                                <input type="text" 
                                       name="project_name" 
                                       id="projectName"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            
                            <!-- Project Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    پۆلی پڕۆژە *
                                </label>
                                <select name="project_category" 
                                        id="projectCategory"
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">پۆلێک هەڵبژێرە</option>
                                    <?php 
                                    // Debug: Log categories
                                    if (empty($commercial_residential_categories)) {
                                        error_log("⚠️ commercial_residential_categories is empty in commercial_residential_design_management.php");
                                    } else {
                                        error_log("✅ Found " . count($commercial_residential_categories) . " categories in commercial_residential_design_management.php");
                                    }
                                    
                                    if (!empty($commercial_residential_categories)): 
                                        foreach ($commercial_residential_categories as $key => $category): 
                                            $categoryTitle = isset($category['title_ku']) && !empty($category['title_ku']) 
                                                ? $category['title_ku'] 
                                                : (isset($category['title']) ? $category['title'] : 'Unknown');
                                    ?>
                                    <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo htmlspecialchars($categoryTitle, ENT_QUOTES, 'UTF-8'); ?>
                                    </option>
                                    <?php 
                                        endforeach; 
                                    else: 
                                    ?>
                                    <!-- Fallback options if categories are not loaded -->
                                    <option value="commercial">بینای بازرگانی</option>
                                    <option value="villa">باڵەخانە</option>
                                    <option value="house">خانوو</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <!-- Project Area (ڕووبەر) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    ڕووبەر (م²) *
                                </label>
                                <input type="number" 
                                       name="project_area" 
                                       id="projectArea"
                                       min="1"
                                       step="1"
                                       required
                                       placeholder="200"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            
                            <!-- Project Floors (نهۆم) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نهۆم (ژمارە) *
                                </label>
                                <input type="number" 
                                       name="project_floors" 
                                       id="projectFloors"
                                       min="0"
                                       max="100"
                                       step="1"
                                       required
                                       placeholder="2"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            
                            <!-- Project Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    نرخی پڕۆژە *
                                </label>
                                <input type="text" 
                                       name="project_price" 
                                       id="projectPrice"
                                       required
                                       placeholder="مثال: 100,000 دینار"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            
                            <!-- Project Duration -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    ماوەی پڕۆژە *
                                </label>
                                <input type="text" 
                                       name="project_duration" 
                                       id="projectDuration"
                                       required
                                       placeholder="مثال: 30 ڕۆژ یان 1 مانگ"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            
                            <!-- Project Description -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    دەربارەی پڕۆژە *
                                </label>
                                <textarea name="project_description" 
                                          id="projectDescription"
                                          rows="4"
                                          required
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"></textarea>
                            </div>
                            
                            <!-- Main Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    وێنەی سەرەکی *
                                </label>
                                <div class="upload-dropzone relative p-6 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <i class="fas fa-cloud-upload-alt text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            دراگ و دابەزاندن یان کلیک بکە بۆ بارکردن
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">PNG, JPG, JPEG • Max 5MB</p>
                                    </div>
                                    <input type="file" name="main_image" id="mainImage" accept="image/*" class="upload-input-overlay" />
                                </div>
                                <div id="mainImagePreview" class="mt-3 hidden">
                                    <div class="relative inline-block">
                                        <img id="mainImagePreviewImg" src="" alt="Preview" class="w-40 h-40 object-cover rounded-xl border border-gray-200 dark:border-gray-700">
                                        <button type="button" onclick="removeMainImage()" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition-colors duration-200">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Images -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    وێنەکانی زیادە
                                </label>
                                <div class="upload-dropzone relative p-6 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <i class="fas fa-images text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">
                                            دراگ و دابەزاندن یان کلیک بکە بۆ بارکردنی چەندین وێنە
                                        </p>
                                        <p class="text-xs text-gray-400 dark:text-gray-500">Up to 10 images • PNG, JPG, JPEG • Max 5MB each</p>
                                    </div>
                                    <input type="file" name="additional_images[]" id="additionalImages" accept="image/*" multiple class="upload-input-overlay" />
                                </div>
                                <div id="additionalImagesPreview" class="mt-3 thumb-grid hidden"></div>
                            </div>
                        </div>
                        
                        <!-- Modal Actions -->
                        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" 
                                    onclick="closeProjectModal()"
                                    class="modal-btn modal-btn-cancel">
                                <i class="fas fa-times"></i>
                                <span>هەڵوەشاندنەوە</span>
                            </button>
                            <button type="submit" 
                                    class="modal-btn modal-btn-save">
                                <i class="fas fa-save"></i>
                                <span>پاشەکەوتکردنی پڕۆژە</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            دڵنیابوونەوە بۆ سڕینەوە
                        </h3>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        ئایا دڵنیایت کە دەتەوێت ئەم پڕۆژەیە بسڕیتەوە؟ ئەم کردارە ناگەڕێتەوە.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button onclick="closeDeleteModal()" 
                                class="modal-btn modal-btn-cancel">
                            <i class="fas fa-times"></i>
                            <span>هەڵوەشاندنەوە</span>
                        </button>
                        <button onclick="confirmDelete()" 
                                class="modal-btn action-btn-delete">
                            <i class="fas fa-trash"></i>
                            <span>سڕینەوەی پڕۆژە</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commercial Residential Design Management JavaScript -->
    <script src="../../assets/js/admin/commercial_residential_design/commercial_residential_design.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/modal.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/features.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/image-preview.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/add.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/edit.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/delete.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/select.js"></script>
    <script src="../../assets/js/admin/commercial_residential_design/update.js"></script>
    
    <!-- Initialize with PHP data -->
    <script>
        // Dark mode toggle function
        function toggleDarkMode() {
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            
            if (body.classList.contains('dark-mode')) {
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
                localStorage.setItem('darkMode', 'false');
                } else {
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
                localStorage.setItem('darkMode', 'true');
            }
        }
        
        // Initialize dark mode from localStorage
        document.addEventListener('DOMContentLoaded', function() {
            const darkMode = localStorage.getItem('darkMode');
            const body = document.body;
            const lightIcon = document.getElementById('lightModeIcon');
            const darkIcon = document.getElementById('darkModeIcon');
            
            if (darkMode === 'true') {
                body.classList.add('dark-mode');
                lightIcon.classList.add('hidden');
                darkIcon.classList.remove('hidden');
                } else {
                body.classList.remove('dark-mode');
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            }
        });
        
        // Pass PHP data to JavaScript
        const csrfToken = '<?php echo $_SESSION['csrf_token']; ?>';
        const translations = {
            addNewProject: 'زیادکردنی پڕۆژەی نوێ',
            editProject: 'دەستکاریکردنی پڕۆژە',
            addFeature: 'زیادکردنی تایبەتمەندی'
        };
        
        // Make CSRF token available globally
        window.csrfToken = csrfToken;
        window.translations = translations;
        
        // Image removal functions
        function removeMainImage() {
            const preview = document.getElementById('mainImagePreview');
            const input = document.getElementById('mainImage');
            preview.classList.add('hidden');
            input.value = '';
        }
        
        function removeAdditionalImage(index) {
            const previewContainer = document.getElementById('additionalImagesPreview');
            const thumbItem = previewContainer.children[index];
            if (thumbItem) {
                thumbItem.remove();
            }
            
            // Clear the file input
            const fileInput = document.getElementById('additionalImages');
            if (fileInput) {
                fileInput.value = '';
            }
            
            // If no more images, hide the preview container
            if (previewContainer.children.length === 0) {
                previewContainer.classList.add('hidden');
            }
        }
        
        // Make functions globally available
        window.removeMainImage = removeMainImage;
        window.removeAdditionalImage = removeAdditionalImage;
    </script>
</body>
</html>



