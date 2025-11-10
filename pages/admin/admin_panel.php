<?php
session_start();
require_once '../../config/db_conected.php';
require_once '../../includes/translations.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get admin info
$admin_id = $_SESSION['admin_id'];
$admin_name = $_SESSION['admin_name'];
$admin_role = $_SESSION['admin_role'];

// Get statistics
try {
    // Get visitor statistics (today)
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT * FROM daily_visitor_stats WHERE visit_date = ?");
    $stmt->execute([$today]);
    $today_stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $today_visitors = $today_stats['total_visits'] ?? 0;
    $today_unique_visitors = $today_stats['unique_visitors'] ?? 0;
    
    // Get total visitors (all time)
    $stmt = $pdo->query("SELECT COUNT(DISTINCT ip_address) as total_unique FROM website_visitors");
    $total_unique_visitors = $stmt->fetchColumn();
    
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM website_visitors");
    $total_visits = $stmt->fetchColumn();
    
    // Get visitors for last 7 days
    $stmt = $pdo->query("
        SELECT visit_date, total_visits, unique_visitors 
        FROM daily_visitor_stats 
        WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ORDER BY visit_date DESC
    ");
    $weekly_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    error_log("Error fetching admin panel data: " . $e->getMessage());
    $today_visitors = $today_unique_visitors = $total_unique_visitors = $total_visits = 0;
    $weekly_stats = [];
}

// Force Kurdish language for admin panel
$current_lang = 'ku';
$page_dir = 'rtl';
?>

<!DOCTYPE html>
<html lang="ku" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پانێڵی ئەدمین - دیمەک</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Meta tags -->
    <meta name="description" content="پانێڵی بەڕێوەبردنی کۆمپانیای دیمەک">
    <meta name="robots" content="noindex, nofollow">
    
    <style>
        .admin-container {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .management-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 8px 16px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border-radius: 20px;
        }
        
        .management-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3b82f6, #10b981, #f59e0b, #8b5cf6);
            background-size: 300% 100%;
            animation: gradientShift 3s ease-in-out infinite;
        }
        
        .management-section:hover {
            transform: translateY(-4px);
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.15),
                0 16px 32px rgba(0, 0, 0, 0.1);
        }
        
        .dark-mode .management-section,
        .dark .management-section {
            background: rgba(30, 41, 59, 0.95);
            border-color: rgba(51, 65, 85, 0.3);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                0 8px 16px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.05);
        }
        
        .dark-mode .management-section:hover,
        .dark .management-section:hover {
            box-shadow: 
                0 32px 64px rgba(0, 0, 0, 0.4),
                0 16px 32px rgba(0, 0, 0, 0.3);
        }
        
        .dark-mode .admin-container,
        .dark .admin-container {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        /* Ensure dark mode works with both .dark and .dark-mode */
        html.dark body,
        body.dark-mode {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        
        .section-icon {
            width: 80px;
            height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        
        .section-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.05));
            border-radius: 20px;
        }
        
        .section-icon i {
            position: relative;
            z-index: 1;
        }
        
        .decorative-dots {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            gap: 8px;
        }
        
        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .dot:nth-child(1) { background: #3b82f6; animation-delay: 0s; }
        .dot:nth-child(2) { background: #f59e0b; animation-delay: 0.5s; }
        .dot:nth-child(3) { background: #10b981; animation-delay: 1s; }
        .dot:nth-child(4) { background: #ef4444; animation-delay: 1.5s; }
        
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.6; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.2); }
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
            line-height: 1.3;
            transition: color 0.3s ease;
        }
        
        .dark-mode .section-title,
        .dark .section-title,
        html.dark .section-title {
            color: #f1f5f9 !important;
        }
        
        .section-subtitle {
            font-size: 0.9rem;
            color: #64748b;
            line-height: 1.4;
            transition: color 0.3s ease;
        }
        
        .dark-mode .section-subtitle,
        .dark .section-subtitle,
        html.dark .section-subtitle {
            color: #94a3b8 !important;
        }
        
        .logout-btn {
            position: fixed;
            top: 6rem;
            right: 2rem;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #ef4444;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            z-index: 40;
        }
        
        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        }
        
        .dark-mode .logout-btn {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.3);
            color: #f87171;
        }
        
        .dark-mode .logout-btn:hover {
            background: rgba(239, 68, 68, 0.3);
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
        .dark-mode .top-toolbar,
        .dark .top-toolbar {
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
        .dark-mode .toolbar-btn,
        .dark .toolbar-btn { 
            background: rgba(30,41,59,0.9); 
            border-color: rgba(255,255,255,0.12);
            color: #e2e8f0;
        }
        .dark-mode .toolbar-btn:hover,
        .dark .toolbar-btn:hover {
            background: rgba(51, 65, 85, 0.9);
        }
        .dark-mode .toolbar-btn span,
        .dark .toolbar-btn span {
            color: #e2e8f0;
        }
        .toolbar-spacer { height: 64px; }
        
        /* Dark mode text colors */
        .dark-mode h1,
        .dark-mode h2,
        .dark-mode h3,
        .dark h1,
        .dark h2,
        .dark h3 {
            color: #f1f5f9;
        }
        
        .dark-mode p,
        .dark p {
            color: #cbd5e1;
        }
        
        /* Ensure all text is visible in dark mode */
        .dark-mode .text-gray-900,
        .dark .text-gray-900 {
            color: #f1f5f9 !important;
        }
        
        .dark-mode .text-gray-600,
        .dark .text-gray-600 {
            color: #cbd5e1 !important;
        }
        
        .dark-mode .text-gray-700,
        .dark .text-gray-700 {
            color: #e2e8f0 !important;
        }
        
        .dark-mode .text-gray-400,
        .dark .text-gray-400 {
            color: #94a3b8 !important;
        }
        
        /* Additional dark mode support */
        html.dark {
            color-scheme: dark;
        }
        
        html.dark body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
        }
    </style>
</head>
<body class="admin-container">
    <!-- Top Toolbar -->
    <div class="top-toolbar">
        <button onclick="toggleDarkMode()" class="toolbar-btn">
            <i id="lightModeIcon" class="fas fa-sun text-yellow-500"></i>
            <i id="darkModeIcon" class="fas fa-moon text-blue-500 hidden"></i>
            <span class="text-sm text-gray-700 dark:text-gray-200">مۆد</span>
        </button>
        <a href="../../core/logout.php" class="toolbar-btn text-red-600 dark:text-red-400 border-red-200">
            <i class="fas fa-sign-out-alt"></i>
            <span class="text-sm">دەرچوون</span>
        </a>
    </div>
    <div class="toolbar-spacer"></div>
    
    <!-- Main Title -->
    <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">
            پانێڵی ئەدمین
        </h1>
        <p class="text-xl text-gray-600 dark:text-gray-400">
            بەخێربێیتەوە, <?php echo htmlspecialchars($admin_name); ?>!
        </p>
                </div>
    
    <!-- Management Sections -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
        
        <!-- Section 1: Infrastructure Management -->
        <a href="infrastructure_management.php" class="management-section p-8 text-center cursor-pointer">
            <div class="decorative-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                </div>
            
            <div class="section-icon bg-gradient-to-br from-blue-500 to-blue-600 mx-auto">
                <i class="fas fa-building"></i>
            </div>
            
            <h3 class="section-title">بەڕێوەبردنی تەلارسازی</h3>
            <p class="section-subtitle">بەڕێوەبردنی تەلارسازی و بیناسازی</p>
        </a>
        
        <!-- Section 2: Design Reconstruction Management -->
        <a href="design_reconstruction_management.php" class="management-section p-8 text-center cursor-pointer">
            <div class="decorative-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            
            <div class="section-icon bg-gradient-to-br from-purple-500 to-purple-600 mx-auto">
                    <i class="fas fa-paint-brush"></i>
            </div>
            
            <h3 class="section-title">بەڕێوەبردنی دیزاین و دووبارە دروستکردنەوە</h3>
            <p class="section-subtitle">بەڕێوەبردنی دیزاین و دووبارە دروستکردنەوەی بیناکان</p>
        </a>
        
        <!-- Section 3: Commercial & Residential Design Management -->
        <a href="commercial_residential_design_management.php" class="management-section p-8 text-center cursor-pointer">
            <div class="decorative-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            
            <div class="section-icon bg-gradient-to-br from-green-500 to-emerald-600 mx-auto">
                <i class="fas fa-building"></i>
            </div>
            
            <h3 class="section-title">بەڕێوەبردنی دیزاینی بینای بازرگانی و ڤێلا و خانوو</h3>
            <p class="section-subtitle">دیزاین و سەرپەرشتی کردنی بینای بازرگانی و ڤێلا و خانوو</p>
        </a>
        
        <!-- Section 4: Exterior Design Management -->
        <a href="exterior_design_management.php" class="management-section p-8 text-center cursor-pointer">
            <div class="decorative-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            
            <div class="section-icon bg-gradient-to-br from-orange-500 to-red-500 mx-auto">
                <i class="fas fa-building"></i>
            </div>
            
            <h3 class="section-title">دیزاین کردن و جێبەجێکردنی دەرەوەی بینا</h3>
            <p class="section-subtitle">دیزاین کردن و جێبەجێکردنی دەرەوەی بینا</p>
        </a>
        
        <!-- Section 5: Interior Design Management -->
        <a href="interior_design_management.php" class="management-section p-8 text-center cursor-pointer">
            <div class="decorative-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            
            <div class="section-icon bg-gradient-to-br from-pink-500 to-rose-500 mx-auto">
                <i class="fas fa-home"></i>
            </div>
            
            <h3 class="section-title">دیزاین کردنی جێبەجێکردنی ناوەوەی بینا</h3>
            <p class="section-subtitle">دیزاین کردنی جێبەجێکردنی ناوەوەی بینا</p>
        </a>
        
        <!-- Section 6: Settings Management -->
        <a href="settings_management.php" class="management-section p-8 text-center cursor-pointer">
            <div class="decorative-dots">
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
                <div class="dot"></div>
            </div>
            
            <div class="section-icon bg-gradient-to-br from-indigo-500 to-indigo-600 mx-auto">
                <i class="fas fa-cog"></i>
            </div>
            
            <h3 class="section-title">بەڕێوەبردنی ڕێکخستنەکان</h3>
            <p class="section-subtitle">بەڕێوەبردنی ڕێکخستنەکانی وێبسایت</p>
        </a>
        
                    </div>
                    
    <!-- Visitor Statistics -->
    <div class="mt-16 max-w-7xl mx-auto">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white text-center mb-8">
                <i class="fas fa-chart-line mr-3 text-blue-600"></i>
                ئامارەکانی سەردانیکەران
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Today's Visitors -->
                <div class="management-section p-6 text-center">
                    <div class="w-16 h-16 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-indigo-600 dark:text-indigo-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo number_format($today_visitors); ?></h3>
                    <p class="text-gray-600 dark:text-gray-400">سەردانیکەرانی ئەمڕۆ</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1"><?php echo number_format($today_unique_visitors); ?> سەردانیکەری جیاواز</p>
                </div>
                
                <!-- Total Unique Visitors -->
                <div class="management-section p-6 text-center">
                    <div class="w-16 h-16 bg-teal-100 dark:bg-teal-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-friends text-teal-600 dark:text-teal-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo number_format($total_unique_visitors); ?></h3>
                    <p class="text-gray-600 dark:text-gray-400">کۆی سەردانیکەرانی جیاواز</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">هەموو کاتەکان</p>
                </div>
                
                <!-- Total Visits -->
                <div class="management-section p-6 text-center">
                    <div class="w-16 h-16 bg-pink-100 dark:bg-pink-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-eye text-pink-600 dark:text-pink-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo number_format($total_visits); ?></h3>
                    <p class="text-gray-600 dark:text-gray-400">کۆی سەردانەکان</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">هەموو کاتەکان</p>
                </div>
                
                <!-- Weekly Average -->
                <div class="management-section p-6 text-center">
                    <div class="w-16 h-16 bg-cyan-100 dark:bg-cyan-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-calendar-week text-cyan-600 dark:text-cyan-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        <?php 
                        $weekly_avg = count($weekly_stats) > 0 ? round(array_sum(array_column($weekly_stats, 'total_visits')) / count($weekly_stats)) : 0;
                        echo number_format($weekly_avg);
                        ?>
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">تێکڕای ٧ ڕۆژی ڕابردوو</p>
                    <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">سەردانیکەر لە ڕۆژێکدا</p>
                </div>
            </div>
            
            <!-- Weekly Chart -->
            <?php if (!empty($weekly_stats)): ?>
            <div class="management-section p-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 text-center">
                    <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                    سەردانیکەران بۆ ٧ ڕۆژی ڕابردوو
                </h3>
                <div class="grid grid-cols-7 gap-2">
                    <?php 
                    // Create array with all 7 days
                    $last_7_days = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $date = date('Y-m-d', strtotime("-$i days"));
                        $last_7_days[$date] = ['visit_date' => $date, 'total_visits' => 0, 'unique_visitors' => 0];
                    }
                    
                    // Merge with actual stats
                    foreach ($weekly_stats as $stat) {
                        $last_7_days[$stat['visit_date']] = $stat;
                    }
                    ?>
                    <?php foreach ($last_7_days as $stat): ?>
                    <div class="text-center">
                        <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-3 mb-2">
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">
                                <?php echo $stat['total_visits']; ?>
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">
                                <?php echo date('d/m', strtotime($stat['visit_date'])); ?>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-500">
                            <?php echo $stat['unique_visitors']; ?> جیاواز
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
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
            
            // Add smooth animations to management sections
            const sections = document.querySelectorAll('.management-section');
            sections.forEach((section, index) => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    section.style.transition = 'all 0.6s ease';
                    section.style.opacity = '1';
                    section.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Apply dark mode transition
            document.documentElement.style.transition = 'background-color 0.3s ease, color 0.3s ease';
            document.body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
            
            // Apply dark mode styles immediately
            if (darkMode === 'true') {
                // Force style recalculation
                const allElements = document.querySelectorAll('*');
                allElements.forEach(el => {
                    el.style.transition = 'color 0.3s ease, background-color 0.3s ease';
                });
            }
        });
        
        // Add click animation to management sections
        document.querySelectorAll('.management-section').forEach(section => {
            section.addEventListener('click', function(e) {
                // Create ripple effect
                const ripple = document.createElement('div');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(255, 255, 255, 0.6)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.left = e.clientX - this.offsetLeft + 'px';
                ripple.style.top = e.clientY - this.offsetTop + 'px';
                ripple.style.width = ripple.style.height = '20px';
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
