<?php
/**
 * Chart Test Page - بۆ تاقیکردنەوەی چارتەکان
 * This page tests if the charts are working properly
 */

session_start();
require_once '../config/db_conected.php';
require_once '../includes/translations.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Get statistics
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM projects");
    $total_projects = $stmt->fetch()['total'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as active FROM projects WHERE status = 'active'");
    $active_projects = $stmt->fetch()['active'];
    
    $stmt = $pdo->query("SELECT COUNT(*) as completed FROM projects WHERE status = 'completed'");
    $completed_projects = $stmt->fetch()['completed'];
    
    $stmt = $pdo->query("SELECT SUM(budget) as total_budget FROM projects WHERE budget IS NOT NULL");
    $total_budget = $stmt->fetch()['total_budget'] ?? 0;
    
} catch (Exception $e) {
    error_log("Error fetching data: " . $e->getMessage());
    $total_projects = $active_projects = $completed_projects = $total_budget = 0;
}

$page_dir = $languages[$current_lang]['dir'];
?>

<!DOCTYPE html>
<html lang="<?php echo $current_lang; ?>" dir="<?php echo $page_dir; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart Test - <?php echo t('admin_panel'); ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../assets/css/main.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        .test-container {
            background: linear-gradient(135deg, var(--bg-primary) 0%, var(--bg-secondary) 100%);
            min-height: 100vh;
            padding: 2rem;
        }
        
        .chart-test-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.1),
                0 8px 16px rgba(0, 0, 0, 0.05);
            border-radius: 1rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        .dark-mode .chart-test-card {
            background: rgba(30, 41, 59, 0.95);
            border-color: rgba(51, 65, 85, 0.3);
        }
        
        .chart-container {
            position: relative;
            height: 400px;
            margin: 1rem 0;
        }
        
        .stats-info {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .dark-mode .stats-info {
            background: rgba(96, 165, 250, 0.1);
            border-color: rgba(96, 165, 250, 0.3);
        }
    </style>
</head>
<body class="test-container">
    <div class="max-w-6xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6 text-center">
            <i class="fas fa-chart-pie mr-3"></i>
            تاقیکردنەوەی چارتەکان - Chart Test
        </h1>
        
        <!-- Statistics Info -->
        <div class="stats-info">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">ئامارەکان - Statistics:</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>کۆی پڕۆژەکان: <strong><?php echo $total_projects; ?></strong></div>
                <div>پڕۆژە چالاکەکان: <strong><?php echo $active_projects; ?></strong></div>
                <div>پڕۆژە تەواوکراوەکان: <strong><?php echo $completed_projects; ?></strong></div>
                <div>کۆی بودجە: <strong>$<?php echo number_format($total_budget); ?></strong></div>
            </div>
        </div>
        
        <!-- Chart 1: Projects Overview -->
        <div class="chart-test-card">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-project-diagram mr-2"></i>
                <?php echo t('projects_overview'); ?>
            </h2>
            <div class="chart-container">
                <canvas id="projectsChart"></canvas>
            </div>
        </div>
        
        <!-- Chart 2: Budget Distribution -->
        <div class="chart-test-card">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-dollar-sign mr-2"></i>
                <?php echo t('budget_distribution'); ?>
            </h2>
            <div class="chart-container">
                <canvas id="budgetChart"></canvas>
            </div>
        </div>
        
        <!-- Chart 3: Project Types -->
        <div class="chart-test-card">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-chart-bar mr-2"></i>
                جۆرەکانی پڕۆژە - Project Types
            </h2>
            <div class="chart-container">
                <canvas id="typesChart"></canvas>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="text-center">
            <a href="admin_panel.php" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                گەڕانەوە بۆ پانێڵی ئەدمین - Back to Admin Panel
            </a>
        </div>
    </div>
    
    <script>
        // Global variables to store chart instances
        let projectsChart = null;
        let budgetChart = null;
        let typesChart = null;
        
        // Initialize all charts
        function initializeAllCharts() {
            console.log('Initializing charts...');
            
            // Projects Chart
            if (projectsChart === null) {
                const projectsCtx = document.getElementById('projectsChart').getContext('2d');
                projectsChart = new Chart(projectsCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['<?php echo t('active_projects'); ?>', '<?php echo t('completed_projects'); ?>', '<?php echo t('upcoming_projects'); ?>'],
                        datasets: [{
                            data: [<?php echo $active_projects; ?>, <?php echo $completed_projects; ?>, <?php echo max(0, $total_projects - $active_projects - $completed_projects); ?>],
                            backgroundColor: [
                                '#10b981',
                                '#3b82f6',
                                '#f59e0b'
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        family: 'Rabar, Arial, sans-serif'
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 8
                            }
                        }
                    }
                });
                console.log('Projects chart created');
            }
            
            // Budget Chart
            if (budgetChart === null) {
                const budgetCtx = document.getElementById('budgetChart').getContext('2d');
                budgetChart = new Chart(budgetCtx, {
                    type: 'bar',
                    data: {
                        labels: ['<?php echo t('residential'); ?>', '<?php echo t('commercial'); ?>', '<?php echo t('industrial'); ?>', '<?php echo t('infrastructure'); ?>'],
                        datasets: [{
                            label: '<?php echo t('budget'); ?> ($)',
                            data: [1500000, 2500000, 3500000, 2000000],
                            backgroundColor: [
                                'rgba(16, 185, 129, 0.8)',
                                'rgba(59, 130, 246, 0.8)',
                                'rgba(245, 158, 11, 0.8)',
                                'rgba(139, 92, 246, 0.8)'
                            ],
                            borderColor: [
                                '#10b981',
                                '#3b82f6',
                                '#f59e0b',
                                '#8b5cf6'
                            ],
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return '$' + (value / 1000000) + 'M';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
                console.log('Budget chart created');
            }
            
            // Types Chart
            if (typesChart === null) {
                const typesCtx = document.getElementById('typesChart').getContext('2d');
                typesChart = new Chart(typesCtx, {
                    type: 'pie',
                    data: {
                        labels: ['<?php echo t('residential'); ?>', '<?php echo t('commercial'); ?>', '<?php echo t('industrial'); ?>', '<?php echo t('infrastructure'); ?>'],
                        datasets: [{
                            data: [2, 3, 1, 2],
                            backgroundColor: [
                                '#10b981',
                                '#3b82f6',
                                '#f59e0b',
                                '#8b5cf6'
                            ],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: {
                                        size: 12,
                                        family: 'Rabar, Arial, sans-serif'
                                    }
                                }
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: 'white',
                                bodyColor: 'white',
                                borderColor: 'rgba(255, 255, 255, 0.1)',
                                borderWidth: 1,
                                cornerRadius: 8
                            }
                        }
                    }
                });
                console.log('Types chart created');
            }
        }
        
        // Destroy all charts
        function destroyAllCharts() {
            if (projectsChart !== null) {
                projectsChart.destroy();
                projectsChart = null;
                console.log('Projects chart destroyed');
            }
            if (budgetChart !== null) {
                budgetChart.destroy();
                budgetChart = null;
                console.log('Budget chart destroyed');
            }
            if (typesChart !== null) {
                typesChart.destroy();
                typesChart = null;
                console.log('Types chart destroyed');
            }
        }
        
        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Page loaded, initializing charts...');
            initializeAllCharts();
        });
        
        // Test button functionality
        function testCharts() {
            console.log('Testing charts...');
            destroyAllCharts();
            setTimeout(() => {
                initializeAllCharts();
            }, 500);
        }
        
        // Add test button
        document.addEventListener('DOMContentLoaded', function() {
            const testButton = document.createElement('button');
            testButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>تاقیکردنەوەی چارتەکان - Test Charts';
            testButton.className = 'fixed top-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors z-50';
            testButton.onclick = testCharts;
            document.body.appendChild(testButton);
        });
    </script>
</body>
</html>
