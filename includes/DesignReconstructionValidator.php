<?php
/**
 * Design Reconstruction Validation Helper
 * 
 * Professional validation and error handling for design reconstruction projects
 */

class DesignReconstructionValidator {
    
    /**
     * Validate project data
     */
    public static function validateProjectData($data) {
        $errors = [];
        
        // Required fields validation
        $requiredFields = [
            'project_name' => 'Project Name',
            'project_category' => 'Project Category',
            'project_price' => 'Project Price',
            'project_duration' => 'Project Duration',
            'project_description' => 'Project Description'
        ];
        
        foreach ($requiredFields as $field => $label) {
            if (empty($data[$field])) {
                $errors[] = "$label is required";
            }
        }
        
        // Price validation - allow text and numbers (e.g., "100,000 دینار")
        // Only check if field is not empty (required validation is handled elsewhere)
        // No strict numeric validation to allow text like "100,000 دینار"
        
        // Category validation
        $validCategories = ['commercial', 'villa', 'house', 'school'];
        if (!empty($data['project_category']) && !in_array($data['project_category'], $validCategories)) {
            $errors[] = "Invalid project category";
        }
        
        // Description length validation
        if (!empty($data['project_description']) && strlen($data['project_description']) < 10) {
            $errors[] = "Description must be at least 10 characters long";
        }
        
        return $errors;
    }
    
    /**
     * Validate uploaded images
     */
    public static function validateImages($files) {
        $errors = [];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        
        if (isset($files['main_image']) && $files['main_image']['error'] === UPLOAD_ERR_OK) {
            $file = $files['main_image'];
            
            if ($file['size'] > $maxFileSize) {
                $errors[] = "Main image is too large. Maximum size is 5MB";
            }
            
            if (!in_array($file['type'], $allowedTypes)) {
                $errors[] = "Main image must be JPEG or PNG format";
            }
        }
        
        if (isset($files['additional_images']) && !empty($files['additional_images']['name'][0])) {
            $fileCount = count($files['additional_images']['name']);
            
            if ($fileCount > 10) {
                $errors[] = "Maximum 10 additional images allowed";
            }
            
            for ($i = 0; $i < $fileCount; $i++) {
                if ($files['additional_images']['error'][$i] === UPLOAD_ERR_OK) {
                    $file = [
                        'size' => $files['additional_images']['size'][$i],
                        'type' => $files['additional_images']['type'][$i]
                    ];
                    
                    if ($file['size'] > $maxFileSize) {
                        $errors[] = "Additional image " . ($i + 1) . " is too large. Maximum size is 5MB";
                    }
                    
                    if (!in_array($file['type'], $allowedTypes)) {
                        $errors[] = "Additional image " . ($i + 1) . " must be JPEG or PNG format";
                    }
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * Sanitize input data
     */
    public static function sanitizeData($data) {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    /**
     * Generate unique filename
     */
    public static function generateUniqueFilename($originalName, $prefix = '') {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        
        return $prefix . '_' . $timestamp . '_' . $random . '.' . $extension;
    }
    
    /**
     * Check if directory exists and create if needed
     */
    public static function ensureDirectoryExists($path) {
        if (!file_exists($path)) {
            return mkdir($path, 0755, true);
        }
        return true;
    }
    
    /**
     * Format error response
     */
    public static function formatErrorResponse($errors) {
        return [
            'success' => false,
            'message' => implode('. ', $errors),
            'errors' => $errors
        ];
    }
    
    /**
     * Format success response
     */
    public static function formatSuccessResponse($message, $data = []) {
        $response = [
            'success' => true,
            'message' => $message
        ];
        
        if (!empty($data)) {
            $response = array_merge($response, $data);
        }
        
        return $response;
    }
}
?>
