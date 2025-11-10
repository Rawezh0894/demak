-- phpMyAdmin SQL Dump
-- Settings Table for Demak DB
-- This migration adds settings functionality for website configuration

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

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

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`key`, `value`, `type`, `description`, `description_ku`, `description_ar`, `is_public`, `group_name`, `sort_order`, `created_at`, `updated_at`) VALUES
('site_name', 'Demak Construction Company', 'text', 'Website name', 'ناوی وێبسایت', 'اسم الموقع', 1, 'general', 1, NOW(), NOW()),
('site_name_ku', 'کۆمپانیای بیناسازی دەماک', 'text', 'Website name in Kurdish', 'ناوی وێبسایت بە کوردی', 'اسم الموقع بالكردية', 1, 'general', 2, NOW(), NOW()),
('site_name_ar', 'شركة دماك للإنشاءات', 'text', 'Website name in Arabic', 'ناوی وێبسایت بە عەرەبی', 'اسم الموقع بالعربية', 1, 'general', 3, NOW(), NOW()),
('site_description', 'Professional construction and building services in Kurdistan', 'text', 'Website description', 'وەسفی وێبسایت', 'وصف الموقع', 1, 'general', 4, NOW(), NOW()),
('site_description_ku', 'خزمەتگوزاری پیشەیی بیناسازی و بینا لە کوردستان', 'text', 'Website description in Kurdish', 'وەسفی وێبسایت بە کوردی', 'وصف الموقع بالكردية', 1, 'general', 5, NOW(), NOW()),
('site_description_ar', 'خدمات البناء والإنشاء المهنية في كردستان', 'text', 'Website description in Arabic', 'وەسفی وێبسایت بە عەرەبی', 'وصف الموقع بالعربية', 1, 'general', 6, NOW(), NOW()),
('contact_email', 'info@demak.com', 'text', 'Contact email', 'ئیمەیلی پەیوەندی', 'البريد الإلكتروني للاتصال', 1, 'contact', 1, NOW(), NOW()),
('contact_phone', '+964 750 123 4567', 'text', 'Contact phone', 'تەلەفۆنی پەیوەندی', 'رقم الهاتف للاتصال', 1, 'contact', 2, NOW(), NOW()),
('contact_phone_2', '', 'text', 'Secondary contact phone', 'تەلەفۆنی پەیوەندی دووەم', 'رقم الهاتف الثانوي', 1, 'contact', 3, NOW(), NOW()),
('contact_address', 'Erbil, Kurdistan Region, Iraq', 'text', 'Contact address', 'ناونیشانی پەیوەندی', 'عنوان الاتصال', 1, 'contact', 4, NOW(), NOW()),
('contact_address_ku', 'هەولێر، هەرێمی کوردستان، عێراق', 'text', 'Contact address in Kurdish', 'ناونیشانی پەیوەندی بە کوردی', 'عنوان الاتصال بالكردية', 1, 'contact', 5, NOW(), NOW()),
('contact_address_ar', 'أربيل، إقليم كردستان، العراق', 'text', 'Contact address in Arabic', 'ناونیشانی پەیوەندی بە عەرەبی', 'عنوان الاتصال بالعربية', 1, 'contact', 6, NOW(), NOW()),
('facebook_url', '', 'text', 'Facebook page URL', 'لینکی فەیسبووک', 'رابط صفحة Facebook', 1, 'social', 1, NOW(), NOW()),
('twitter_url', '', 'text', 'Twitter profile URL', 'لینکی تویتەر', 'رابط Twitter', 1, 'social', 2, NOW(), NOW()),
('instagram_url', '', 'text', 'Instagram profile URL', 'لینکی ئینستاگرام', 'رابط Instagram', 1, 'social', 3, NOW(), NOW()),
('linkedin_url', '', 'text', 'LinkedIn profile URL', 'لینکی لینکدئین', 'رابط LinkedIn', 1, 'social', 4, NOW(), NOW()),
('youtube_url', '', 'text', 'YouTube channel URL', 'لینکی یوتیوب', 'رابط قناة YouTube', 1, 'social', 5, NOW(), NOW()),
('whatsapp_number', '', 'text', 'WhatsApp business number', 'ژمارەی واتسئاپ', 'رقم WhatsApp', 1, 'social', 6, NOW(), NOW()),
('maintenance_mode', '0', 'boolean', 'Enable maintenance mode', 'چالاککردنی مۆدی چاککردن', 'تفعيل وضع الصيانة', 0, 'system', 1, NOW(), NOW()),
('maintenance_message', 'Website is under maintenance. Please check back later.', 'text', 'Maintenance mode message', 'پەیامی مۆدی چاککردن', 'رسالة وضع الصيانة', 0, 'system', 2, NOW(), NOW()),
('maintenance_message_ku', 'وێبسایت لەژێر چاککردندا. تکایە دواتر سەردان بکە.', 'text', 'Maintenance mode message in Kurdish', 'پەیامی مۆدی چاککردن بە کوردی', 'رسالة وضع الصيانة بالكردية', 0, 'system', 3, NOW(), NOW()),
('maintenance_message_ar', 'الموقع قيد الصيانة. يرجى التحقق مرة أخرى لاحقًا.', 'text', 'Maintenance mode message in Arabic', 'پەیامی مۆدی چاککردن بە عەرەبی', 'رسالة وضع الصيانة بالعربية', 0, 'system', 4, NOW(), NOW()),
('max_file_size', '10485760', 'number', 'Maximum file upload size in bytes (10MB)', 'زۆرترین قەبارەی فایلی بارکردن (بایت)', 'الحد الأقصى لحجم ملف التحميل (بايت)', 0, 'system', 5, NOW(), NOW()),
('allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx', 'text', 'Allowed file types for upload (comma separated)', 'جۆرەکانی فایلی ڕێگەپێدراو بۆ بارکردن', 'أنواع الملفات المسموح بها للتحميل', 0, 'system', 6, NOW(), NOW()),
('timezone', 'Asia/Baghdad', 'text', 'Website timezone', 'کاتژمێری وێبسایت', 'المنطقة الزمنية للموقع', 0, 'system', 7, NOW(), NOW()),
('default_language', 'ku', 'text', 'Default website language', 'زمانی بنەڕەتی وێبسایت', 'اللغة الافتراضية للموقع', 0, 'system', 8, NOW(), NOW()),
('items_per_page', '12', 'number', 'Number of items per page', 'ژمارەی بەندەکان لە هەر پەیجێکدا', 'عدد العناصر في كل صفحة', 0, 'system', 9, NOW(), NOW()),
('site_logo', '', 'file', 'Website logo', 'لۆگۆی وێبسایت', 'شعار الموقع', 1, 'general', 7, NOW(), NOW()),
('site_favicon', '', 'file', 'Website favicon', 'فەڤیکۆنی وێبسایت', 'أيقونة الموقع', 1, 'general', 8, NOW(), NOW());




