<?php
// Add Setting Functionality

// Clear any output buffer content
if (ob_get_level()) {
    ob_clean();
}

// Set JSON content type
header('Content-Type: application/json');

try {
    $key = trim($_POST['key'] ?? '');
    $value = trim($_POST['value'] ?? '');
    $type = trim($_POST['type'] ?? 'text');
    $description = trim($_POST['description'] ?? '');
    $description_ku = trim($_POST['description_ku'] ?? '');
    $description_ar = trim($_POST['description_ar'] ?? '');
    $is_public = isset($_POST['is_public']) ? intval($_POST['is_public']) : 0;
    $group_name = trim($_POST['group_name'] ?? 'other');
    $sort_order = intval($_POST['sort_order'] ?? 0);
    
    // Validate required fields
    if (empty($key)) {
        throw new Exception('کلیل پێویستە (key)');
    }
    
    // Validate key format (alphanumeric and underscore only)
    if (!preg_match('/^[a-z0-9_]+$/', $key)) {
        throw new Exception('کلیل پێویستە تەنها پیت و ژمارە و _ تێدابێت');
    }
    
    // Check if key already exists
    $check_stmt = $pdo->prepare("SELECT id FROM settings WHERE `key` = ?");
    $check_stmt->execute([$key]);
    if ($check_stmt->fetch()) {
        throw new Exception('ئەم کلیلە پێشتر هەیە');
    }
    
    // Validate type
    $allowed_types = ['text', 'number', 'boolean', 'json', 'file'];
    if (!in_array($type, $allowed_types)) {
        throw new Exception('جۆری هەڵە');
    }
    
    // Handle file uploads if type is 'file'
    if ($type === 'file' && isset($_FILES['value']) && $_FILES['value']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/images/settings/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = pathinfo($_FILES['value']['name'], PATHINFO_EXTENSION);
        $file_name = $key . '_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
        $file_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['value']['tmp_name'], $file_path)) {
            $value = 'assets/images/settings/' . $file_name;
        } else {
            throw new Exception('هەڵەیەک ڕوویدا لە بارکردنی فایل');
        }
    }
    
    // Insert setting
    $stmt = $pdo->prepare("
        INSERT INTO settings (`key`, value, type, description, description_ku, description_ar, is_public, group_name, sort_order, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
    ");
    
    $stmt->execute([
        $key,
        $value,
        $type,
        $description,
        $description_ku,
        $description_ar,
        $is_public,
        $group_name,
        $sort_order
    ]);
    
    $setting_id = $pdo->lastInsertId();
    
    // Log the action
    if (function_exists('createDetailedNotification')) {
        createDetailedNotification(
            $pdo, 
            $_SESSION['admin_id'], 
            'create', 
            'settings', 
            $setting_id, 
            'New setting added: ' . $key, 
            null, 
            ['key' => $key, 'value' => $value, 'type' => $type], 
            null, 
            getUserIP()
        );
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'ڕێکخستن بە سەرکەوتوویی زیاد کرا',
        'setting_id' => $setting_id
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




