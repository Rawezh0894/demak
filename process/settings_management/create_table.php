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
    $pdo->beginTransaction();
    
    try {
        // Execute CREATE TABLE statement
        $create_table_sql = "
        CREATE TABLE IF NOT EXISTS `settings` (
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

