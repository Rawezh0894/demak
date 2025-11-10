-- phpMyAdmin SQL Dump
-- Commercial & Residential Design & Management Tables for Demak DB
-- This migration adds commercial_residential_design functionality
-- Categories: Commercial Buildings, Villas, Houses
-- Each project has: area (ڕووبەر), floors (نهۆم), price, duration

-- --------------------------------------------------------

--
-- Drop existing tables if needed (comment out if you want to keep existing data)
--

DROP TABLE IF EXISTS `commercial_residential_design_features`;
DROP TABLE IF EXISTS `commercial_residential_design_images`;
DROP TABLE IF EXISTS `commercial_residential_design_projects`;
DROP TABLE IF EXISTS `commercial_residential_design_categories`;

-- --------------------------------------------------------

--
-- Table structure for table `commercial_residential_design_categories`
--

CREATE TABLE `commercial_residential_design_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_key` varchar(50) NOT NULL,
  `title` varchar(200) NOT NULL,
  `title_ku` varchar(200) DEFAULT NULL,
  `title_ar` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ku` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `color` varchar(20) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_key` (`category_key`),
  KEY `idx_active` (`is_active`),
  KEY `idx_sort` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `commercial_residential_design_categories`
--

INSERT INTO `commercial_residential_design_categories` (`id`, `category_key`, `title`, `title_ku`, `title_ar`, `description`, `description_ku`, `description_ar`, `icon`, `color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'commercial', 'Commercial Buildings', 'بینای بازرگانی', 'المباني التجارية', 'Commercial building design and management services', 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی بینای بازرگانی', 'خدمات التصميم والإدارة للمباني التجارية', 'fas fa-building', '#3b82f6', 1, 1, NOW(), NOW()),
(2, 'villa', 'Villas', 'باڵەخانە', 'الفيلات', 'Luxury villa design and management services', 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی باڵەخانە لۆکس', 'خدمات التصميم والإدارة للفيلات الفاخرة', 'fas fa-home', '#10b981', 2, 1, NOW(), NOW()),
(3, 'house', 'Houses', 'خانوو', 'المنازل', 'Residential house design and management services', 'خزمەتگوزاری دیزاین و سەرپەرشتی کردنی خانوو', 'خدمات التصميم والإدارة للمنازل السكنية', 'fas fa-house-user', '#f59e0b', 3, 1, NOW(), NOW());

-- --------------------------------------------------------

--
-- Table structure for table `commercial_residential_design_projects`
--

CREATE TABLE `commercial_residential_design_projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_key` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `name_ku` varchar(200) DEFAULT NULL,
  `name_ar` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ku` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `area` varchar(100) DEFAULT NULL COMMENT 'ڕووبەر (Area in square meters)',
  `area_ku` varchar(100) DEFAULT NULL,
  `area_ar` varchar(100) DEFAULT NULL,
  `floors` int(11) DEFAULT NULL COMMENT 'نهۆم (Number of floors)',
  `price` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','featured') DEFAULT 'active',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category_key`),
  KEY `idx_status` (`status`),
  KEY `idx_active` (`is_active`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_sort` (`sort_order`),
  CONSTRAINT `commercial_residential_design_projects_ibfk_1` FOREIGN KEY (`category_key`) REFERENCES `commercial_residential_design_categories` (`category_key`) ON DELETE CASCADE,
  CONSTRAINT `commercial_residential_design_projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commercial_residential_design_images`
--

CREATE TABLE `commercial_residential_design_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project` (`project_id`),
  KEY `idx_main` (`is_main`),
  KEY `idx_sort` (`sort_order`),
  CONSTRAINT `commercial_residential_design_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `commercial_residential_design_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commercial_residential_design_features`
--

CREATE TABLE `commercial_residential_design_features` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `feature_text` text NOT NULL,
  `feature_text_ku` text DEFAULT NULL,
  `feature_text_ar` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_project` (`project_id`),
  KEY `idx_sort` (`sort_order`),
  CONSTRAINT `commercial_residential_design_features_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `commercial_residential_design_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;

