<?php
/**
 * Infrastructure Data Loader
 * Loads infrastructure categories and projects from database
 */

function loadInfrastructureData($pdo) {
    $infrastructure_categories = [];
    
    try {
        // Load categories
        $stmt = $pdo->query("
            SELECT * FROM infrastructure_categories 
            WHERE is_active = 1 
            ORDER BY sort_order ASC
        ");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($categories as $category) {
            $infrastructure_categories[$category['key']] = [
                'title' => $category['title'],
                'title_ku' => $category['title_ku'],
                'title_ar' => $category['title_ar'],
                'description' => $category['description'],
                'description_ku' => $category['description_ku'],
                'description_ar' => $category['description_ar'],
                'icon' => $category['icon'],
                'color' => $category['color'],
                'projects' => []
            ];
        }
        
        // Load projects with their features and images
        $stmt = $pdo->query("
            SELECT ip.*, ic.title as category_title, ic.title_ku as category_title_ku, ic.title_ar as category_title_ar,
                   ic.icon as category_icon, ic.color as category_color
            FROM infrastructure_projects ip
            LEFT JOIN infrastructure_categories ic ON ip.category_key = ic.key
            WHERE ip.is_active = 1 AND ip.status = 'active'
            ORDER BY ip.sort_order ASC, ip.id ASC
        ");
        $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Load project features
        $features_stmt = $pdo->prepare("
            SELECT project_id, feature_text, feature_text_ku, feature_text_ar 
            FROM project_features 
            WHERE project_id = ? AND is_active = 1 
            ORDER BY sort_order ASC
        ");
        
        // Load project images
        $images_stmt = $pdo->prepare("
            SELECT image_path, image_type, sort_order 
            FROM project_images 
            WHERE project_id = ? AND is_active = 1 
            ORDER BY sort_order ASC
        ");
        
        foreach ($projects as $project) {
            // Get project features
            $features_stmt->execute([$project['id']]);
            $features = $features_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get project images
            $images_stmt->execute([$project['id']]);
            $images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Prepare project data
            $main_image_url = 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
            
            if ($project['main_image'] && !empty(trim($project['main_image']))) {
                $possible_paths = [
                    '../../' . $project['main_image'],
                    '../' . $project['main_image'],
                    $project['main_image']
                ];
                
                foreach ($possible_paths as $path) {
                    if (file_exists($path)) {
                        $main_image_url = $path;
                        break;
                    }
                }
                
                // If no file found, try with absolute path
                if ($main_image_url === 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') {
                    $main_image_url = '../../' . $project['main_image'];
                }
            }
            
            $project_data = [
                'id' => $project['id'],
                'name' => $project['name'],
                'name_ku' => $project['name_ku'],
                'name_ar' => $project['name_ar'],
                'description' => $project['description'],
                'description_ku' => $project['description_ku'],
                'description_ar' => $project['description_ar'],
                'price' => $project['price'],
                'duration' => $project['duration'],
                'image' => $main_image_url,
                'images' => [],
                'features' => []
            ];
            
            // Add additional images
            foreach ($images as $image) {
                if ($image['image_type'] === 'gallery') {
                    $full_image_path = '../../' . $image['image_path'];
                    if (file_exists($full_image_path)) {
                        $project_data['images'][] = '../../' . $image['image_path'];
                    }
                }
            }
            
            // Add features
            foreach ($features as $feature) {
                $project_data['features'][] = $feature['feature_text'];
            }
            
            // Add project to category
            if (isset($infrastructure_categories[$project['category_key']])) {
                $infrastructure_categories[$project['category_key']]['projects'][] = $project_data;
            }
        }
        
        return $infrastructure_categories;
        
    } catch (Exception $e) {
        // Fallback to static data if database tables don't exist yet
        return require_once __DIR__ . '/../../config/infrastructure_data.php';
    }
}
?>
