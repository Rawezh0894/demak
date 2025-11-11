<?php
// Create Settings Table Functionality

session_start();
require_once '../../config/db_conected.php';

// Set JSON content type
header('Content-Type: application/json');

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// CSRF Protection
if (!isset($input['csrf_token']) || $input['csrf_token'] !== $_SESSION['csrf_token']) {
    echo json_encode(['success' => false, 'message' => 'Invalid request. Please try again.']);
    exit;
}

try {
    // Check if table exists
    $table_check = $pdo->query("SHOW TABLES LIKE 'settings'");
    $table_exists = $table_check->rowCount() > 0;
    
    $pdo->beginTransaction();
    
    try {
        if (!$table_exists) {
            // Create new table with all columns
            $create_table_sql = "
            CREATE TABLE `settings` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `key` varchar(100) NOT NULL,
              `value` text DEFAULT NULL,
              `type` enum('text','number','boolean','json','file') DEFAULT 'text',
              `description` text DEFAULT NULL,
              `description_ku` text DEFAULT NULL,
              `description_ar` text DEFAULT NULL,
              `is_public` tinyint(1) DEFAULT 0,
              `group_name` varchar(50) DEFAULT NULL,
              `sort_order` int(11) DEFAULT 0,
              `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
              `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
              PRIMARY KEY (`id`),
              UNIQUE KEY `key` (`key`),
              KEY `idx_public` (`is_public`),
              KEY `idx_group` (`group_name`),
              KEY `idx_sort` (`sort_order`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";
            
            $pdo->exec($create_table_sql);
        } else {
            // Table exists, check and add missing columns
            $columns_check = $pdo->query("SHOW COLUMNS FROM settings");
            $existing_columns = [];
            while ($col = $columns_check->fetch(PDO::FETCH_ASSOC)) {
                $existing_columns[] = $col['Field'];
            }
            
            // Add missing columns
            if (!in_array('description_ku', $existing_columns)) {
                $pdo->exec("ALTER TABLE settings ADD COLUMN `description_ku` text DEFAULT NULL AFTER `description`");
            }
            
            if (!in_array('description_ar', $existing_columns)) {
                $pdo->exec("ALTER TABLE settings ADD COLUMN `description_ar` text DEFAULT NULL AFTER `description_ku`");
            }
            
            if (!in_array('group_name', $existing_columns)) {
                $pdo->exec("ALTER TABLE settings ADD COLUMN `group_name` varchar(50) DEFAULT NULL AFTER `is_public`");
                $pdo->exec("ALTER TABLE settings ADD KEY `idx_group` (`group_name`)");
            }
            
            if (!in_array('sort_order', $existing_columns)) {
                $pdo->exec("ALTER TABLE settings ADD COLUMN `sort_order` int(11) DEFAULT 0 AFTER `group_name`");
                $pdo->exec("ALTER TABLE settings ADD KEY `idx_sort` (`sort_order`)");
            }
        }
        
        // Check if contact_phone_2 exists, if not, insert it
        $check_phone2 = $pdo->query("SELECT COUNT(*) as count FROM settings WHERE `key` = 'contact_phone_2'");
        $phone2_exists = $check_phone2->fetch(PDO::FETCH_ASSOC)['count'] > 0;
        
        if (!$phone2_exists) {
            // Insert contact_phone_2 if it doesn't exist
            $insert_phone2 = $pdo->prepare("
                INSERT INTO settings (`key`, `value`, `type`, `description`, `description_ku`, `description_ar`, `is_public`, `group_name`, `sort_order`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $insert_phone2->execute(['contact_phone_2', '', 'text', 'Secondary contact phone', 'تەلەفۆنی پەیوەندی دووەم', 'رقم الهاتف الثانوي', 1, 'contact', 3]);
        }
        
        // Check if table is empty, if so, insert default data
        $check_stmt = $pdo->query("SELECT COUNT(*) as count FROM settings");
        $count = $check_stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        if ($count == 0) {
            // Insert default settings
            $default_settings = [
                ['site_name', 'Demak Construction Company', 'text', 'Website name', 'ناوی وێبسایت', 'اسم الموقع', 1, 'general', 1],
                ['site_name_ku', 'کۆمپانیای بیناسازی دەماک', 'text', 'Website name in Kurdish', 'ناوی وێبسایت بە کوردی', 'اسم الموقع بالكردية', 1, 'general', 2],
                ['site_name_ar', 'شركة دماك للإنشاءات', 'text', 'Website name in Arabic', 'ناوی وێبسایت بە عەرەبی', 'اسم الموقع بالعربية', 1, 'general', 3],
                ['site_description', 'Professional construction and building services in Kurdistan', 'text', 'Website description', 'وەسفی وێبسایت', 'وصف الموقع', 1, 'general', 4],
                ['contact_email', 'info@demak.com', 'text', 'Contact email', 'ئیمەیلی پەیوەندی', 'البريد الإلكتروني للاتصال', 1, 'contact', 1],
                ['contact_phone', '+964 750 123 4567', 'text', 'Contact phone', 'تەلەفۆنی پەیوەندی', 'رقم الهاتف للاتصال', 1, 'contact', 2],
                ['contact_phone_2', '', 'text', 'Secondary contact phone', 'تەلەفۆنی پەیوەندی دووەم', 'رقم الهاتف الثانوي', 1, 'contact', 3],
                ['contact_address', 'Erbil, Kurdistan Region, Iraq', 'text', 'Contact address', 'ناونیشانی پەیوەندی', 'عنوان الاتصال', 1, 'contact', 4],
                ['contact_address_ku', 'هەولێر، هەرێمی کوردستان، عێراق', 'text', 'Contact address in Kurdish', 'ناونیشانی پەیوەندی بە کوردی', 'عنوان الاتصال بالكردية', 1, 'contact', 5],
                ['contact_address_ar', 'أربيل، إقليم كردستان، العراق', 'text', 'Contact address in Arabic', 'ناونیشانی پەیوەندی بە عەرەبی', 'عنوان الاتصال بالعربية', 1, 'contact', 6],
                ['maintenance_mode', '0', 'boolean', 'Enable maintenance mode', 'چالاککردنی مۆدی چاککردن', 'تفعيل وضع الصيانة', 0, 'system', 1],
            ];
            
            $insert_stmt = $pdo->prepare("
                INSERT INTO settings (`key`, `value`, `type`, `description`, `description_ku`, `description_ar`, `is_public`, `group_name`, `sort_order`) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($default_settings as $setting) {
                $insert_stmt->execute($setting);
            }
        }
        
        $pdo->commit();
        
        echo json_encode([
            'success' => true, 
            'message' => 'تەیبڵی settings بە سەرکەوتوویی دروست کرا و داتاکان زیاد کراون!'
        ]);
        
    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error creating settings table: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'هەڵە لە دروستکردنی تەیبڵ: ' . htmlspecialchars($e->getMessage())
    ]);
}
?>

