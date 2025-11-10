<?php
/**
 * Exterior Design Data Loader
 * Loads exterior design projects from database
 */

function loadExteriorDesignData($pdo) {
    $projects = [];
    
    try {
        // Load projects
        $stmt = $pdo->query("
            SELECT * FROM exterior_design_projects
            WHERE is_active = 1 AND status = 'active'
            ORDER BY sort_order ASC, created_at DESC
        ");
        $projects_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Load project images
        $images_stmt = $pdo->prepare("
            SELECT image_path, is_main, sort_order 
            FROM exterior_design_images 
            WHERE project_id = ? 
            ORDER BY sort_order ASC
        ");
        
        foreach ($projects_data as $project) {
            // Get project images
            $images_stmt->execute([$project['id']]);
            $images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Prepare main image
            $main_image_url = null;
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
                
                if (!$main_image_url) {
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
                'images' => []
            ];
            
            // Add additional images
            foreach ($images as $image) {
                if (!$image['is_main']) {
                    $full_image_path = '../../' . $image['image_path'];
                    if (file_exists($full_image_path)) {
                        $project_data['images'][] = '../../' . $image['image_path'];
                    }
                }
            }
            
            $projects[] = $project_data;
        }
        
        return $projects;
        
    } catch (Exception $e) {
        error_log("Error loading exterior design projects: " . $e->getMessage());
        return [];
    }
}
?>
