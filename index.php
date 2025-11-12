<?php
session_start();
require_once 'config/db_conected.php';
require_once 'includes/translations.php';

// Track website visitors (silently - don't break if tables don't exist)
try {
    require_once 'includes/visitor_tracker.php';
} catch (Exception $e) {
    // Silently fail - visitor tracking shouldn't break the website
    error_log("Visitor tracking error: " . $e->getMessage());
}

// Sample project data
$projects = [
    [
        'id' => 1,
        'title' => 'Modern Residential Complex',
        'title_ku' => 'کۆمپلێکسی نیشتەجێبوونی مۆدێرن',
        'title_ar' => 'مجمع سكني حديث',
        'type' => 'residential',
        'status' => 'completed',
        'budget' => '$2.5M',
        'completion_date' => '2024-01-15',
        'location' => 'Erbil, Kurdistan',
        'location_ku' => 'هەولێر، کوردستان',
        'location_ar' => 'أربيل، كردستان',
        'client' => 'Kurdistan Housing Authority',
        'client_ku' => 'دەزگای خانووی کوردستان',
        'client_ar' => 'هيئة الإسكان الكردية',
        'description' => 'A modern residential complex with 200 units, featuring sustainable design and modern amenities.',
        'description_ku' => 'کۆمپلێکسی نیشتەجێبوونی مۆدێرن بە ٢٠٠ یەکە، بە دیزاینی بەردەوام و ئاسایشی مۆدێرن.',
        'description_ar' => 'مجمع سكني حديث يحتوي على 200 وحدة، يتميز بالتصميم المستدام والمرافق الحديثة.',
        'image' => 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 2,
        'title' => 'Commercial Office Building',
        'title_ku' => 'بینای ئۆفیسی بازرگانی',
        'title_ar' => 'مبنى مكتبي تجاري',
        'type' => 'commercial',
        'status' => 'active',
        'budget' => '$5.2M',
        'completion_date' => '2024-06-30',
        'location' => 'Sulaymaniyah, Kurdistan',
        'location_ku' => 'سلێمانی، کوردستان',
        'location_ar' => 'السليمانية، كردستان',
        'client' => 'Kurdistan Business Center',
        'client_ku' => 'ناوەندەی بازرگانی کوردستان',
        'client_ar' => 'مركز الأعمال الكردي',
        'description' => 'A 15-story commercial office building with modern facilities and green building certification.',
        'description_ku' => 'بینای ئۆفیسی بازرگانی ١٥ نهۆمی بە ئاسایشی مۆدێرن و بڕوانامەی بینای سەوز.',
        'description_ar' => 'مبنى مكتبي تجاري من 15 طابق مع مرافق حديثة وشهادة المبنى الأخضر.',
        'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ],
    [
        'id' => 3,
        'title' => 'Industrial Manufacturing Plant',
        'title_ku' => 'کارگەی پیشەسازی',
        'title_ar' => 'مصنع صناعي',
        'type' => 'industrial',
        'status' => 'upcoming',
        'budget' => '$8.7M',
        'completion_date' => '2024-12-31',
        'location' => 'Duhok, Kurdistan',
        'location_ku' => 'دهۆک، کوردستان',
        'location_ar' => 'دهوك، كردستان',
        'client' => 'Kurdistan Industrial Development',
        'client_ku' => 'پەرەپێدانی پیشەسازی کوردستان',
        'client_ar' => 'تطوير الصناعة الكردية',
        'description' => 'A state-of-the-art manufacturing facility with advanced automation and environmental compliance.',
        'description_ku' => 'کارگەیەکی پیشەسازی پێشکەوتوو بە ئۆتۆمەیشنی پێشکەوتوو و ڕێکخستنی ژینگەیی.',
        'description_ar' => 'منشأة تصنيع متطورة مع أتمتة متقدمة وامتثال بيئي.',
        'image' => 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
    ]
];

// Set page direction based on language
$page_dir = $languages[$current_lang]['dir'];
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('construction_company'); ?> - <?php echo t('excellence_in_construction'); ?></title>
    
    <!-- Tailwind CSS -->
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'rabar': ['Rabar', 'sans-serif'],
                        'display': ['Lalezar', 'Rabar', 'sans-serif']
                    },
                    colors: {
                        primary: {
                            DEFAULT: '#2563eb',
                            dark: '#1d4ed8',
                            light: '#3b82f6'
                        },
                        accent: {
                            DEFAULT: '#38bdf8'
                        }
                    }
                }
            }
        };
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/main.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Meta tags for SEO -->
    <meta name="description" content="<?php echo t('we_build_dreams'); ?> - <?php echo t('construction_company'); ?>">
    <meta name="keywords" content="construction, building, projects, kurdistan, iraq">
    
    <!-- Open Graph tags -->
    <meta property="og:title" content="<?php echo t('construction_company'); ?>">
    <meta property="og:description" content="<?php echo t('we_build_dreams'); ?>">
    <meta property="og:type" content="website">
</head>
<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <!-- Include Navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Include Sidebar -->
    <?php include 'includes/sidebar.php'; ?>
    
    <!-- Include Floating Contact -->
    <?php include 'includes/floating-contact.php'; ?>
    
    <!-- Main Content -->
    <div id="mainContent" class="main-content-without-sidebar">
        <!-- Include Hero Section -->
        <?php include 'pages/public/sections/hero.php'; ?>
        
        <!-- Include Services Section -->
        <?php include 'pages/public/sections/services.php'; ?>
        
        <!-- Include Contact Section -->
        <?php include 'pages/public/sections/contact.php'; ?>
        
        <!-- Include Footer -->
        <?php include 'pages/public/sections/footer.php'; ?>
        
        <!-- Include Modals -->
        <?php include 'pages/public/sections/modals.php'; ?>
    </div>

    <!-- JavaScript -->
    <script src="assets/js/index/index.js"></script>
    
    <!-- Dark Mode Toggle Script - Using function from navbar.php -->
    <!-- Note: toggleDarkMode() is defined in navbar.php with animation support -->
</body>
</html>