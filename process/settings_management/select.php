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
        // Check which columns exist in the table
        $columns_check = $pdo->query("SHOW COLUMNS FROM settings");
        $existing_columns = [];
        while ($col = $columns_check->fetch(PDO::FETCH_ASSOC)) {
            $existing_columns[] = $col['Field'];
        }
        
        $has_group_name = in_array('group_name', $existing_columns);
        $has_sort_order = in_array('sort_order', $existing_columns);
        $has_description_ku = in_array('description_ku', $existing_columns);
        $has_description_ar = in_array('description_ar', $existing_columns);
        
        // Build ORDER BY clause based on available columns
        $order_by = [];
        if ($has_group_name) {
            $order_by[] = 'group_name ASC';
        }
        if ($has_sort_order) {
            $order_by[] = 'sort_order ASC';
        }
        $order_by[] = 'id ASC';
        $order_by_clause = !empty($order_by) ? 'ORDER BY ' . implode(', ', $order_by) : 'ORDER BY id ASC';
        
        // Try to load settings
        $stmt = $pdo->query("
            SELECT * 
            FROM settings 
            $order_by_clause
        ");
        $all_settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($all_settings)) {
            $settings_error = "تەیبڵی settings هەیە بەڵام هیچ داتایەک تێدا نییە. تکایە داتاکان زیاد بکە.";
            error_log("Settings table exists but is empty");
        } else {
            // Check if table needs migration
            $needs_migration = false;
            $missing_columns = [];
            if (!$has_group_name) $missing_columns[] = 'group_name';
            if (!$has_sort_order) $missing_columns[] = 'sort_order';
            if (!$has_description_ku) $missing_columns[] = 'description_ku';
            if (!$has_description_ar) $missing_columns[] = 'description_ar';
            
            if (!empty($missing_columns)) {
                $needs_migration = true;
                $settings_error = "تەیبڵی settings پێویست بە نوێکردنەوەیە. ستونەکانی دیکە نەدۆزرانەوە: " . implode(', ', $missing_columns) . ". تکایە تەیبڵ نوێ بکەرەوە.";
            }
            
            // Organize settings by group
            foreach ($all_settings as $setting) {
                // Add default values for missing columns
                if (!$has_group_name) {
                    $setting['group_name'] = 'other';
                }
                if (!$has_sort_order) {
                    $setting['sort_order'] = 0;
                }
                if (!$has_description_ku) {
                    $setting['description_ku'] = $setting['description'] ?? '';
                }
                if (!$has_description_ar) {
                    $setting['description_ar'] = $setting['description'] ?? '';
                }
                
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
        // Check if group_name column exists
        $columns_check = $pdo->query("SHOW COLUMNS FROM settings LIKE 'group_name'");
        $has_group_name = $columns_check->rowCount() > 0;
        
        if ($has_group_name) {
            $stmt = $pdo->prepare("
                SELECT * 
                FROM settings 
                WHERE group_name = ? 
                ORDER BY sort_order ASC, id ASC
            ");
            $stmt->execute([$group_name]);
        } else {
            // If group_name doesn't exist, return all settings
            $stmt = $pdo->query("
                SELECT * 
                FROM settings 
                ORDER BY id ASC
            ");
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

// Function to get public settings only
function getPublicSettings($pdo) {
    try {
        // Check which columns exist
        $columns_check = $pdo->query("SHOW COLUMNS FROM settings");
        $existing_columns = [];
        while ($col = $columns_check->fetch(PDO::FETCH_ASSOC)) {
            $existing_columns[] = $col['Field'];
        }
        
        $has_group_name = in_array('group_name', $existing_columns);
        $has_sort_order = in_array('sort_order', $existing_columns);
        
        // Build ORDER BY clause
        $order_by = [];
        if ($has_group_name) {
            $order_by[] = 'group_name ASC';
        }
        if ($has_sort_order) {
            $order_by[] = 'sort_order ASC';
        }
        $order_by_clause = !empty($order_by) ? 'ORDER BY ' . implode(', ', $order_by) : 'ORDER BY id ASC';
        
        $stmt = $pdo->query("
            SELECT `key`, `value`, `type` 
            FROM settings 
            WHERE is_public = 1 
            $order_by_clause
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




