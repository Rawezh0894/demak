<?php
// Select and Load Settings Data Functionality

// Load all settings from database grouped by group_name
$settings = [];
$settings_by_group = [];
$settings_error = null;

try {
    // First, check if settings table exists
    $table_check = $pdo->query("SHOW TABLES LIKE 'settings'");
    $table_exists = $table_check->rowCount() > 0;
    
    if (!$table_exists) {
        $settings_error = "تەیبڵی settings نەدۆزرایەوە. تکایە سەرەتا تەیبڵی settings دروست بکە.";
        error_log("Settings table does not exist");
    } else {
        // Try to load settings
        $stmt = $pdo->query("
            SELECT * 
            FROM settings 
            ORDER BY group_name ASC, sort_order ASC, id ASC
        ");
        $all_settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($all_settings)) {
            $settings_error = "تەیبڵی settings هەیە بەڵام هیچ داتایەک تێدا نییە. تکایە داتاکان زیاد بکە.";
            error_log("Settings table exists but is empty");
        } else {
            // Organize settings by group
            foreach ($all_settings as $setting) {
                $group = $setting['group_name'] ?? 'other';
                if (!isset($settings_by_group[$group])) {
                    $settings_by_group[$group] = [];
                }
                $settings_by_group[$group][] = $setting;
                $settings[$setting['key']] = $setting;
            }
        }
    }
} catch (PDOException $e) {
    $error_code = $e->getCode();
    $error_message = $e->getMessage();
    
    // Check if it's a table doesn't exist error
    if ($error_code == '42S02' || strpos($error_message, "doesn't exist") !== false || strpos($error_message, "Unknown table") !== false) {
        $settings_error = "تەیبڵی settings نەدۆزرایەوە. تکایە سەرەتا تەیبڵی settings دروست بکە.";
    } else {
        $settings_error = "هەڵە لە خوێندنەوەی ڕێکخستنەکان: " . htmlspecialchars($error_message);
    }
    
    error_log("Error loading settings: " . $error_message);
    $settings = [];
    $settings_by_group = [];
} catch (Exception $e) {
    $settings_error = "هەڵەیەک ڕوویدا: " . htmlspecialchars($e->getMessage());
    error_log("Error loading settings: " . $e->getMessage());
    $settings = [];
    $settings_by_group = [];
}

// Group names in Kurdish
$group_names = [
    'general' => 'گشتی',
    'contact' => 'پەیوەندی',
    'social' => 'تۆڕە کۆمەڵایەتییەکان',
    'system' => 'سیستەم'
];

// Function to get setting by key
function getSettingByKey($pdo, $key) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

// Function to get settings by group
function getSettingsByGroup($pdo, $group_name) {
    try {
        $stmt = $pdo->prepare("
            SELECT * 
            FROM settings 
            WHERE group_name = ? 
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([$group_name]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to get public settings only
function getPublicSettings($pdo) {
    try {
        $stmt = $pdo->query("
            SELECT `key`, `value`, `type` 
            FROM settings 
            WHERE is_public = 1 
            ORDER BY group_name ASC, sort_order ASC
        ");
        $public_settings = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $public_settings[$row['key']] = $row['value'];
        }
        return $public_settings;
    } catch (Exception $e) {
        return [];
    }
}

// Function to get setting value by key
function getSettingValue($pdo, $key, $default = null) {
    try {
        $stmt = $pdo->prepare("SELECT `value` FROM settings WHERE `key` = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}
?>




