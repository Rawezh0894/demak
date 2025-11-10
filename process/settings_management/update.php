<?php
// Update Settings Functionality

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

// Set JSON content type
header('Content-Type: application/json');

try {
    // Check if it's a bulk update (multiple settings at once)
    if (isset($_POST['bulk_update']) && $_POST['bulk_update'] == '1') {
        // Bulk update multiple settings
        $settings_data = $_POST['settings'] ?? [];
        
        if (empty($settings_data)) {
            throw new Exception('هیچ ڕێکخستنێک نەدراوە');
        }
        
        $updated_count = 0;
        $pdo->beginTransaction();
        
        try {
            foreach ($settings_data as $setting_id => $setting_data) {
                $setting_id = intval($setting_id);
                if ($setting_id <= 0) {
                    continue;
                }
                
                $value = trim($setting_data['value'] ?? '');
                $description = trim($setting_data['description'] ?? '');
                $description_ku = trim($setting_data['description_ku'] ?? '');
                $description_ar = trim($setting_data['description_ar'] ?? '');
                $is_public = isset($setting_data['is_public']) ? intval($setting_data['is_public']) : 0;
                
                // Handle file uploads if type is 'file'
                $current_setting = $pdo->prepare("SELECT * FROM settings WHERE id = ?");
                $current_setting->execute([$setting_id]);
                $setting = $current_setting->fetch(PDO::FETCH_ASSOC);
                
                if (!$setting) {
                    continue;
                }
                
                // If it's a file type and a new file is uploaded
                if ($setting['type'] === 'file' && isset($_FILES['settings']['name'][$setting_id]['value'])) {
                    $file = $_FILES['settings'];
                    if ($file['error'][$setting_id]['value'] === UPLOAD_ERR_OK) {
                        $upload_dir = '../../assets/images/settings/';
                        if (!file_exists($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }
                        
                        $file_extension = pathinfo($file['name'][$setting_id]['value'], PATHINFO_EXTENSION);
                        $file_name = $setting['key'] . '_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                        $file_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($file['tmp_name'][$setting_id]['value'], $file_path)) {
                            // Delete old file if exists
                            if (!empty($setting['value']) && file_exists('../../' . $setting['value'])) {
                                @unlink('../../' . $setting['value']);
                            }
                            $value = 'assets/images/settings/' . $file_name;
                        }
                    } else {
                        // Keep existing value if upload failed
                        $value = $setting['value'];
                    }
                }
                
                // Update setting
                $stmt = $pdo->prepare("
                    UPDATE settings 
                    SET value = ?, 
                        description = ?,
                        description_ku = ?,
                        description_ar = ?,
                        is_public = ?,
                        updated_at = NOW()
                    WHERE id = ?
                ");
                
                $stmt->execute([
                    $value,
                    $description ?: $setting['description'],
                    $description_ku ?: $setting['description_ku'],
                    $description_ar ?: $setting['description_ar'],
                    $is_public,
                    $setting_id
                ]);
                
                $updated_count++;
            }
            
            $pdo->commit();
            
            // Log the action
            if (function_exists('createDetailedNotification')) {
                createDetailedNotification(
                    $pdo, 
                    $_SESSION['admin_id'], 
                    'update', 
                    'settings', 
                    null, 
                    'Settings updated (bulk)', 
                    null, 
                    null, 
                    ['updated_count' => $updated_count], 
                    getUserIP()
                );
            }
            
            echo json_encode([
                'success' => true,
                'message' => "{$updated_count} ڕێکخستن بە سەرکەوتوویی نوێ کراونەوە",
                'updated_count' => $updated_count
            ]);
            exit;
            
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    } else {
        // Single setting update
        $setting_id = intval($_POST['setting_id'] ?? 0);
        if ($setting_id <= 0) {
            throw new Exception('ڕێکخستنی هەڵە');
        }
        
        $value = trim($_POST['value'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $description_ku = trim($_POST['description_ku'] ?? '');
        $description_ar = trim($_POST['description_ar'] ?? '');
        $is_public = isset($_POST['is_public']) ? intval($_POST['is_public']) : 0;
        
        // Get current setting
        $stmt = $pdo->prepare("SELECT * FROM settings WHERE id = ?");
        $stmt->execute([$setting_id]);
        $setting = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$setting) {
            throw new Exception('ڕێکخستن نەدۆزرایەوە');
        }
        
        // Handle file uploads if type is 'file'
        if ($setting['type'] === 'file' && isset($_FILES['value']) && $_FILES['value']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../assets/images/settings/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['value']['name'], PATHINFO_EXTENSION);
            $file_name = $setting['key'] . '_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['value']['tmp_name'], $file_path)) {
                // Delete old file if exists
                if (!empty($setting['value']) && file_exists('../../' . $setting['value'])) {
                    @unlink('../../' . $setting['value']);
                }
                $value = 'assets/images/settings/' . $file_name;
            } else {
                throw new Exception('هەڵەیەک ڕوویدا لە بارکردنی فایل');
            }
        }
        
        // Update setting
        $stmt = $pdo->prepare("
            UPDATE settings 
            SET value = ?, 
                description = ?,
                description_ku = ?,
                description_ar = ?,
                is_public = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            $value,
            $description ?: $setting['description'],
            $description_ku ?: $setting['description_ku'],
            $description_ar ?: $setting['description_ar'],
            $is_public,
            $setting_id
        ]);
        
        if ($stmt->rowCount() === 0) {
            throw new Exception('هیچ گۆڕانکارییەک نەکرا');
        }
        
        // Log the action
        if (function_exists('createDetailedNotification')) {
            createDetailedNotification(
                $pdo, 
                $_SESSION['admin_id'], 
                'update', 
                'settings', 
                $setting_id, 
                'Setting updated: ' . $setting['key'], 
                ['old_value' => $setting['value']], 
                ['new_value' => $value], 
                null, 
                getUserIP()
            );
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'ڕێکخستن بە سەرکەوتوویی نوێ کراەوە',
            'setting_id' => $setting_id
        ]);
        exit;
    }
    
} catch (Exception $e) {
    // Return JSON error response for AJAX
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
?>




