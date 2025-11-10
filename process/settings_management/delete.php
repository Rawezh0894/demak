<?php
// Delete Setting Functionality

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

// Set JSON content type
header('Content-Type: application/json');

try {
    $setting_id = intval($_POST['setting_id'] ?? 0);
    if ($setting_id <= 0) {
        throw new Exception('ڕێکخستنی هەڵە');
    }
    
    // Get setting before deletion
    $stmt = $pdo->prepare("SELECT * FROM settings WHERE id = ?");
    $stmt->execute([$setting_id]);
    $setting = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$setting) {
        throw new Exception('ڕێکخستن نەدۆزرایەوە');
    }
    
    // Prevent deletion of critical system settings
    $protected_keys = ['site_name', 'site_name_ku', 'site_name_ar', 'contact_email', 'contact_phone'];
    if (in_array($setting['key'], $protected_keys)) {
        throw new Exception('ناتوانیت ئەم ڕێکخستنە بسڕیتەوە (ڕێکخستنی گرنگ)');
    }
    
    // Delete associated file if exists
    if ($setting['type'] === 'file' && !empty($setting['value']) && file_exists('../../' . $setting['value'])) {
        @unlink('../../' . $setting['value']);
    }
    
    // Delete setting
    $stmt = $pdo->prepare("DELETE FROM settings WHERE id = ?");
    $stmt->execute([$setting_id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('هیچ شتێک نەسڕایەوە');
    }
    
    // Log the action
    if (function_exists('createDetailedNotification')) {
        createDetailedNotification(
            $pdo, 
            $_SESSION['admin_id'], 
            'delete', 
            'settings', 
            $setting_id, 
            'Setting deleted: ' . $setting['key'], 
            ['key' => $setting['key'], 'value' => $setting['value']], 
            null, 
            null, 
            getUserIP()
        );
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'ڕێکخستن بە سەرکەوتوویی سڕایەوە'
    ]);
    exit;
    
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




