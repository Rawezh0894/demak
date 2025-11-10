-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 02, 2025 at 08:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demak_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('super_admin','admin','moderator') DEFAULT 'admin',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `full_name`, `email`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$ndP7Gf7pGF4EXRhAVDfYC.BdFcVV5X4Hfz81VmcLFDfu6.x9St7kS', 'زریان', 'admin@demak.com', 'super_admin', 1, '2025-11-02 20:53:52', '2025-10-16 07:45:26', '2025-11-02 17:53:52');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `name_ku` varchar(200) DEFAULT NULL,
  `name_ar` varchar(200) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `company` varchar(200) DEFAULT NULL,
  `company_ku` varchar(200) DEFAULT NULL,
  `company_ar` varchar(200) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `address_ku` text DEFAULT NULL,
  `address_ar` text DEFAULT NULL,
  `logo` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(200) DEFAULT NULL,
  `message` text NOT NULL,
  `status` enum('new','read','replied','archived') DEFAULT 'new',
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `design_reconstruction_categories`
--

CREATE TABLE `design_reconstruction_categories` (
  `id` int(11) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `design_reconstruction_categories`
--

INSERT INTO `design_reconstruction_categories` (`id`, `category_key`, `title`, `title_ku`, `title_ar`, `description`, `description_ku`, `description_ar`, `icon`, `color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'commercial', 'Commercial Buildings', 'بینای بازرگانی', 'المباني التجارية', 'Commercial building design and reconstruction services', 'خزمەتگوزاری دیزاین و دووبارە دروستکردنەوەی بینای بازرگانی', 'خدمات التصميم وإعادة الإنشاء التجاري', 'fas fa-building', '#3b82f6', 1, 1, '2025-10-27 15:20:44', '2025-10-27 15:20:44'),
(2, 'villa', 'Villas', 'باڵەخانە', 'الفيلات', 'Luxury villa design and reconstruction services', 'خزمەتگوزاری دیزاین و دووبارە دروستکردنەوەی باڵەخانە لۆکس', 'خدمات التصميم وإعادة الإنشاء للفيلات الفاخرة', 'fas fa-home', '#10b981', 2, 1, '2025-10-27 15:20:44', '2025-10-27 15:20:44'),
(3, 'house', 'Houses', 'خانوو', 'المنازل', 'Residential house design and reconstruction services', 'خزمەتگوزاری دیزاین و دووبارە دروستکردنەوەی خانوو', 'خدمات التصميم وإعادة الإنشاء للمنازل السكنية', 'fas fa-house-user', '#f59e0b', 3, 1, '2025-10-27 15:20:44', '2025-10-27 15:20:44'),
(4, 'school', 'Schools', 'قوتابخانە', 'المدارس', 'Educational facility design and reconstruction services', 'خزمەتگوزاری دیزاین و دووبارە دروستکردنەوەی قوتابخانە', 'خدمات التصميم وإعادة الإنشاء للمرافق التعليمية', 'fas fa-school', '#8b5cf6', 4, 1, '2025-10-27 15:20:44', '2025-10-27 15:20:44');

-- --------------------------------------------------------

--
-- Table structure for table `design_reconstruction_features`
--

CREATE TABLE `design_reconstruction_features` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `feature_text` text NOT NULL,
  `feature_text_ku` text DEFAULT NULL,
  `feature_text_ar` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `design_reconstruction_images`
--

CREATE TABLE `design_reconstruction_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `design_reconstruction_images`
--

INSERT INTO `design_reconstruction_images` (`id`, `project_id`, `image_path`, `is_main`, `sort_order`, `created_at`) VALUES
(4, 2, 'assets/images/projects/design_reconstruction/main_2_1761578544_6121.jpeg', 1, 0, '2025-10-27 15:22:24'),
(5, 2, 'assets/images/projects/design_reconstruction/gallery/gallery_2_0_1761578544_9305.jpg', 0, 0, '2025-10-27 15:22:24'),
(6, 2, 'assets/images/projects/design_reconstruction/gallery/gallery_2_1_1761578544_6375.png', 0, 0, '2025-10-27 15:22:24'),
(7, 3, 'assets/images/projects/design_reconstruction/main_3_1762102287_1404.jpg', 1, 0, '2025-11-02 16:51:27'),
(8, 3, 'assets/images/projects/design_reconstruction/gallery/gallery_3_0_1762102287_1874.JPG', 0, 0, '2025-11-02 16:51:27'),
(9, 3, 'assets/images/projects/design_reconstruction/gallery/gallery_3_1_1762102287_6766.jpg', 0, 0, '2025-11-02 16:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `design_reconstruction_projects`
--

CREATE TABLE `design_reconstruction_projects` (
  `id` int(11) NOT NULL,
  `category_key` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `name_ku` varchar(200) DEFAULT NULL,
  `name_ar` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ku` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive','featured') DEFAULT 'active',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `design_reconstruction_projects`
--

INSERT INTO `design_reconstruction_projects` (`id`, `category_key`, `name`, `name_ku`, `name_ar`, `description`, `description_ku`, `description_ar`, `price`, `duration`, `status`, `sort_order`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'commercial', 'بەرزاییەکانی سلێمانی', NULL, NULL, 'سڵآو لسملمدل', NULL, NULL, '2', '1', 'active', 0, 1, NULL, '2025-10-27 15:22:24', '2025-10-27 15:22:24'),
(3, 'house', 'گوندی ئەڵمانی', NULL, NULL, 'سڵاو کاک زریان', NULL, NULL, '2500', '8', 'active', 0, 1, NULL, '2025-11-02 16:51:27', '2025-11-02 16:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `file_uploads`
--

CREATE TABLE `file_uploads` (
  `id` int(11) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `file_type` enum('image','document','other') DEFAULT 'other',
  `uploaded_by` int(11) DEFAULT NULL,
  `related_table` varchar(50) DEFAULT NULL,
  `related_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `infrastructure_categories`
--

CREATE TABLE `infrastructure_categories` (
  `id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `infrastructure_categories`
--

INSERT INTO `infrastructure_categories` (`id`, `key`, `title`, `title_ku`, `title_ar`, `description`, `description_ku`, `description_ar`, `icon`, `color`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'architectural', 'Architectural Drawings', 'نەخشەی تەلارسازی', 'الرسومات المعمارية', 'Professional architectural design and planning services', 'خزمەتگوزاری دیزاین و پلاندانانی تەلارسازی پیشەیی', 'خدمات التصميم والتخطيط المعماري المهني', 'fas fa-drafting-compass', '#3b82f6', 1, 1, '2025-10-16 09:40:31', '2025-10-16 09:40:31'),
(2, 'structural', 'Structural Drawings', 'نەخشەی ستڕەکچەری', 'الرسومات الإنشائية', 'Structural engineering and design services', 'خزمەتگوزاری ئەندازیاری و دیزاینی ستڕەکچەری', 'خدمات الهندسة والتصميم الإنشائي', 'fas fa-cube', '#10b981', 2, 1, '2025-10-16 09:40:31', '2025-10-16 09:40:31'),
(3, 'mechanical', 'Mechanical Drawings', 'نەخشەی میکانیکی', 'الرسومات الميكانيكية', 'Mechanical systems design and engineering', 'دیزاین و ئەندازیاری سیستەمەکانی میکانیکی', 'تصميم وهندسة الأنظمة الميكانيكية', 'fas fa-cogs', '#f59e0b', 3, 1, '2025-10-16 09:40:31', '2025-10-16 09:40:31'),
(4, 'electrical', 'Electrical Drawings', 'نەخشەی کارەبا', 'الرسومات الكهربائية', 'Electrical systems design and installation', 'دیزاین و دامەزراندنی سیستەمەکانی کارەبا', 'تصميم وتركيب الأنظمة الكهربائية', 'fas fa-bolt', '#8b5cf6', 4, 1, '2025-10-16 09:40:31', '2025-10-16 09:40:31');

-- --------------------------------------------------------

--
-- Table structure for table `infrastructure_projects`
--

CREATE TABLE `infrastructure_projects` (
  `id` int(11) NOT NULL,
  `category_key` varchar(50) NOT NULL,
  `name` varchar(200) NOT NULL,
  `name_ku` varchar(200) DEFAULT NULL,
  `name_ar` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ku` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `price` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `main_image` varchar(500) DEFAULT NULL,
  `status` enum('active','inactive','featured') DEFAULT 'active',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `infrastructure_projects`
--

INSERT INTO `infrastructure_projects` (`id`, `category_key`, `name`, `name_ku`, `name_ar`, `description`, `description_ku`, `description_ar`, `price`, `duration`, `main_image`, `status`, `sort_order`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(9, 'structural', 'گوندی ئەڵمانی', 'گوندی ئەڵمانی', 'گوندی ئەڵمانی', 'سڵاو کوردستان', 'سڵاو کوردستان', 'سڵاو کوردستان', '500', '14', 'assets/images/projects/main_1762106131_9317.jpeg', 'active', 0, 1, 1, '2025-11-02 17:55:31', '2025-11-02 17:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `additional_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`additional_info`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `action`, `table_name`, `record_id`, `description`, `old_values`, `new_values`, `additional_info`, `ip_address`, `is_read`, `created_at`) VALUES
(1, 1, 'failed_login', 'admins', 1, 'Failed login attempt', NULL, NULL, '{\"ip_address\":\"::1\",\"reason\":\"invalid_password\"}', '::1', 0, '2025-10-16 07:48:03'),
(2, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 07:48:05'),
(3, 1, 'failed_login', 'admins', 1, 'Failed login attempt', NULL, NULL, '{\"ip_address\":\"::1\",\"reason\":\"invalid_password\"}', '::1', 0, '2025-10-16 07:55:15'),
(4, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 07:55:23'),
(5, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:05:10'),
(6, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:06:34'),
(7, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:06:37'),
(8, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:07:04'),
(9, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:07:08'),
(10, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:09:17'),
(11, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:09:22'),
(12, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:09:55'),
(13, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:09:59'),
(14, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:20:33'),
(15, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:27:36'),
(16, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 08:27:49'),
(17, 1, 'failed_login', 'admins', 1, 'Failed login attempt', NULL, NULL, '{\"ip_address\":\"::1\",\"reason\":\"invalid_password\"}', '::1', 0, '2025-10-16 09:21:57'),
(18, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-16 09:22:01'),
(19, 1, 'create', 'infrastructure_projects', 5, 'New infrastructure project created', NULL, NULL, '{\"project_name\":\"شاری جوان\"}', '::1', 0, '2025-10-16 09:47:17'),
(20, 1, 'create', 'infrastructure_projects', 6, 'New infrastructure project created', NULL, NULL, '{\"project_name\":\"شاری جوان\"}', '::1', 0, '2025-10-16 09:58:00'),
(21, 1, 'create', 'infrastructure_projects', 7, 'New infrastructure project created', NULL, NULL, '{\"project_name\":\"شاری جوان\"}', '::1', 0, '2025-10-16 10:21:41'),
(22, 1, 'create', 'infrastructure_projects', 8, 'New infrastructure project created', NULL, NULL, '{\"project_name\":\"مایۆرکا\"}', '::1', 0, '2025-10-16 10:22:52'),
(23, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-24 06:27:55'),
(24, 1, 'delete', 'infrastructure_projects', 7, 'Infrastructure project deleted', NULL, NULL, '{\"project_name\":\"شاری جوان\"}', '::1', 0, '2025-10-24 06:29:45'),
(25, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-24 13:43:15'),
(26, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-24 13:57:59'),
(27, 1, 'update', 'infrastructure_projects', 8, 'Infrastructure project updated', NULL, NULL, '{\"project_name\":\"مایۆرکا\"}', '::1', 0, '2025-10-24 14:05:44'),
(28, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-27 15:21:41'),
(29, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-10-27 16:14:54'),
(30, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-11-02 16:42:26'),
(31, 1, 'logout', 'admins', 1, 'Admin logged out successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-11-02 16:42:31'),
(32, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-11-02 16:43:03'),
(33, 1, 'delete', 'infrastructure_projects', 8, 'Infrastructure project deleted', NULL, NULL, '{\"project_name\":\"مایۆرکا\"}', '::1', 0, '2025-11-02 16:56:35'),
(34, 1, 'login', 'admins', 1, 'Admin logged in successfully', NULL, NULL, '{\"ip_address\":\"::1\"}', '::1', 0, '2025-11-02 17:53:52'),
(35, 1, 'create', 'infrastructure_projects', 9, 'New infrastructure project created', NULL, NULL, '{\"project_name\":\"گوندی ئەڵمانی\"}', '::1', 0, '2025-11-02 17:55:31'),
(36, 1, 'update', 'infrastructure_projects', 9, 'Infrastructure project updated', NULL, NULL, '{\"project_name\":\"گوندی ئەڵمانی\"}', '::1', 0, '2025-11-02 17:55:44');

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `title_ku` varchar(200) DEFAULT NULL,
  `title_ar` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ku` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `type` enum('residential','commercial','industrial','infrastructure','renovation') NOT NULL,
  `infrastructure_category` varchar(50) DEFAULT NULL,
  `status` enum('upcoming','active','completed','on_hold','cancelled') DEFAULT 'upcoming',
  `budget` decimal(15,2) DEFAULT NULL,
  `project_price` varchar(100) DEFAULT NULL,
  `project_duration` varchar(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `completion_date` date DEFAULT NULL,
  `location` varchar(200) NOT NULL,
  `location_ku` varchar(200) DEFAULT NULL,
  `location_ar` varchar(200) DEFAULT NULL,
  `client` varchar(200) DEFAULT NULL,
  `client_ku` varchar(200) DEFAULT NULL,
  `client_ar` varchar(200) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `gallery` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gallery`)),
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_featured` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `title_ku`, `title_ar`, `description`, `description_ku`, `description_ar`, `type`, `infrastructure_category`, `status`, `budget`, `project_price`, `project_duration`, `start_date`, `completion_date`, `location`, `location_ku`, `location_ar`, `client`, `client_ku`, `client_ar`, `image`, `gallery`, `features`, `is_featured`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Modern Residential Complex', 'کۆمپلێکسی نیشتەجێبوونی مۆدێرن', 'مجمع سكني حديث', 'A modern residential complex with 200 units, featuring sustainable design and modern amenities.', 'کۆمپلێکسی نیشتەجێبوونی مۆدێرن بە ٢٠٠ یەکە، بە دیزاینی بەردەوام و ئاسایشی مۆدێرن.', 'مجمع سكني حديث يحتوي على 200 وحدة، يتميز بالتصميم المستدام والمرافق الحديثة.', 'residential', NULL, 'completed', 2500000.00, NULL, NULL, NULL, '2024-01-15', 'Erbil, Kurdistan', 'هەولێر، کوردستان', 'أربيل، كردستان', 'Kurdistan Housing Authority', 'دەزگای خانووی کوردستان', 'هيئة الإسكان الكردية', 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', NULL, NULL, 1, 1, 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(2, 'Commercial Office Building', 'بینای ئۆفیسی بازرگانی', 'مبنى مكتبي تجاري', 'A 15-story commercial office building with modern facilities and green building certification.', 'بینای ئۆفیسی بازرگانی ١٥ نهۆمی بە ئاسایشی مۆدێرن و بڕوانامەی بینای سەوز.', 'مبنى مكتبي تجاري من 15 طابق مع مرافق حديثة وشهادة المبنى الأخضر.', 'commercial', NULL, 'active', 5200000.00, NULL, NULL, NULL, '2024-06-30', 'Sulaymaniyah, Kurdistan', 'سلێمانی، کوردستان', 'السليمانية، كردستان', 'Kurdistan Business Center', 'ناوەندەی بازرگانی کوردستان', 'مركز الأعمال الكردي', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', NULL, NULL, 1, 1, 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(3, 'Industrial Manufacturing Plant', 'کارگەی پیشەسازی', 'مصنع صناعي', 'A state-of-the-art manufacturing facility with advanced automation and environmental compliance.', 'کارگەیەکی پیشەسازی پێشکەوتوو بە ئۆتۆمەیشنی پێشکەوتوو و ڕێکخستنی ژینگەیی.', 'منشأة تصنيع متطورة مع أتمتة متقدمة وامتثال بيئي.', 'industrial', NULL, 'upcoming', 8700000.00, NULL, NULL, NULL, '2024-12-31', 'Duhok, Kurdistan', 'دهۆک، کوردستان', 'دهوك، كردستان', 'Kurdistan Industrial Development', 'پەرەپێدانی پیشەسازی کوردستان', 'تطوير الصناعة الكردية', 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', NULL, NULL, 0, 1, 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(4, 'Modern Residential Complex', 'کۆمپلێکسی نیشتەجێبوونی مۆدێرن', 'مجمع سكني حديث', 'A modern residential complex with 200 units, featuring sustainable design and modern amenities.', 'کۆمپلێکسی نیشتەجێبوونی مۆدێرن بە ٢٠٠ یەکە، بە دیزاینی بەردەوام و ئاسایشی مۆدێرن.', 'مجمع سكني حديث يحتوي على 200 وحدة، يتميز بالتصميم المستدام والمرافق الحديثة.', 'residential', NULL, 'completed', 2500000.00, NULL, NULL, NULL, '2024-01-15', 'Erbil, Kurdistan', 'هەولێر، کوردستان', 'أربيل، كردستان', 'Kurdistan Housing Authority', 'دەزگای خانووی کوردستان', 'هيئة الإسكان الكردية', 'https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', NULL, NULL, 1, 1, 1, '2025-10-16 07:46:51', '2025-10-16 07:46:51'),
(5, 'Commercial Office Building', 'بینای ئۆفیسی بازرگانی', 'مبنى مكتبي تجاري', 'A 15-story commercial office building with modern facilities and green building certification.', 'بینای ئۆفیسی بازرگانی ١٥ نهۆمی بە ئاسایشی مۆدێرن و بڕوانامەی بینای سەوز.', 'مبنى مكتبي تجاري من 15 طابق مع مرافق حديثة وشهادة المبنى الأخضر.', 'commercial', NULL, 'active', 5200000.00, NULL, NULL, NULL, '2024-06-30', 'Sulaymaniyah, Kurdistan', 'سلێمانی، کوردستان', 'السليمانية، كردستان', 'Kurdistan Business Center', 'ناوەندەی بازرگانی کوردستان', 'مركز الأعمال الكردي', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', NULL, NULL, 1, 1, 1, '2025-10-16 07:46:51', '2025-10-16 07:46:51'),
(6, 'Industrial Manufacturing Plant', 'کارگەی پیشەسازی', 'مصنع صناعي', 'A state-of-the-art manufacturing facility with advanced automation and environmental compliance.', 'کارگەیەکی پیشەسازی پێشکەوتوو بە ئۆتۆمەیشنی پێشکەوتوو و ڕێکخستنی ژینگەیی.', 'منشأة تصنيع متطورة مع أتمتة متقدمة وامتثال بيئي.', 'industrial', NULL, 'upcoming', 8700000.00, NULL, NULL, NULL, '2024-12-31', 'Duhok, Kurdistan', 'دهۆک، کوردستان', 'دهوك، كردستان', 'Kurdistan Industrial Development', 'پەرەپێدانی پیشەسازی کوردستان', 'تطوير الصناعة الكردية', 'https://images.unsplash.com/photo-1581094794329-c8112a89af12?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', NULL, NULL, 0, 1, 1, '2025-10-16 07:46:51', '2025-10-16 07:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `project_downloads`
--

CREATE TABLE `project_downloads` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `downloaded_by` varchar(200) DEFAULT NULL,
  `download_ip` varchar(45) DEFAULT NULL,
  `download_count` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_engineers`
--

CREATE TABLE `project_engineers` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `engineer_name` varchar(200) NOT NULL,
  `engineer_name_ku` varchar(200) DEFAULT NULL,
  `engineer_name_ar` varchar(200) DEFAULT NULL,
  `specialization` varchar(200) DEFAULT NULL,
  `specialization_ku` varchar(200) DEFAULT NULL,
  `specialization_ar` varchar(200) DEFAULT NULL,
  `contact_info` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_features`
--

CREATE TABLE `project_features` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `feature_text` text NOT NULL,
  `feature_text_ku` text DEFAULT NULL,
  `feature_text_ar` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_images`
--

CREATE TABLE `project_images` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `image_path` varchar(500) NOT NULL,
  `image_type` enum('main','gallery') DEFAULT 'gallery',
  `alt_text` varchar(200) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_images`
--

INSERT INTO `project_images` (`id`, `project_id`, `image_path`, `image_type`, `alt_text`, `sort_order`, `is_active`, `created_at`) VALUES
(13, 9, 'assets/images/projects/gallery/gallery_1762106131_0_5114.jpeg', 'gallery', NULL, 1, 1, '2025-11-02 17:55:31'),
(14, 9, 'assets/images/projects/gallery/gallery_1762106131_1_6660.jpg', 'gallery', NULL, 2, 1, '2025-11-02 17:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `project_materials`
--

CREATE TABLE `project_materials` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `material_name` varchar(200) NOT NULL,
  `material_name_ku` varchar(200) DEFAULT NULL,
  `material_name_ar` varchar(200) DEFAULT NULL,
  `quantity` varchar(100) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_quotes`
--

CREATE TABLE `project_quotes` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `client_name` varchar(200) NOT NULL,
  `client_email` varchar(100) NOT NULL,
  `client_phone` varchar(20) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','expired') DEFAULT 'pending',
  `quoted_price` decimal(15,2) DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `title_ku` varchar(200) DEFAULT NULL,
  `title_ar` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ku` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `icon` varchar(100) DEFAULT NULL,
  `image` varchar(500) DEFAULT NULL,
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `title`, `title_ku`, `title_ar`, `description`, `description_ku`, `description_ar`, `icon`, `image`, `features`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Commercial Design', 'دیزاینی بازرگانی', 'التصميم التجاري', 'Professional commercial building design and construction services.', 'خزمەتگوزاری دیزاین و بیناسازی بینای بازرگانی پیشەیی.', 'خدمات التصميم والبناء التجاري المهني.', 'fas fa-building', NULL, NULL, 1, 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(2, 'Residential Design', 'دیزاینی نیشتەجێبوون', 'التصميم السكني', 'Modern residential construction and design solutions.', 'چارەسەرەکانی دیزاین و بیناسازی نیشتەجێبوونی مۆدێرن.', 'حلول التصميم والبناء السكني الحديث.', 'fas fa-home', NULL, NULL, 1, 2, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(3, 'Infrastructure', 'ژێرخانی', 'البنية التحتية', 'Complete infrastructure development and maintenance.', 'پەرەپێدان و چاکردنەوەی ژێرخانی تەواو.', 'تطوير وصيانة البنية التحتية الكاملة.', 'fas fa-road', NULL, NULL, 1, 3, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(4, 'Renovation', 'نوێکردنەوە', 'التجديد', 'Professional renovation and restoration services.', 'خزمەتگوزاری نوێکردنەوە و گەڕاندنەوەی پیشەیی.', 'خدمات التجديد والترميم المهنية.', 'fas fa-tools', NULL, NULL, 1, 4, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(5, 'Exterior Design', 'دیزاینی دەرەوە', 'التصميم الخارجي', 'Beautiful exterior design and landscaping services.', 'خزمەتگوزاری دیزاینی دەرەوەی جوان و باخچەسازی.', 'خدمات التصميم الخارجي الجميل وتنسيق الحدائق.', 'fas fa-tree', NULL, NULL, 1, 5, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(6, 'Interior Design', 'دیزاینی ناوەوە', 'التصميم الداخلي', 'Modern interior design and decoration services.', 'خزمەتگوزاری دیزاینی ناوەوەی مۆدێرن و جوانکاری.', 'خدمات التصميم الداخلي الحديث والديكور.', 'fas fa-couch', NULL, NULL, 1, 6, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(7, 'Commercial Design', 'دیزاینی بازرگانی', 'التصميم التجاري', 'Professional commercial building design and construction services.', 'خزمەتگوزاری دیزاین و بیناسازی بینای بازرگانی پیشەیی.', 'خدمات التصميم والبناء التجاري المهني.', 'fas fa-building', NULL, NULL, 1, 1, '2025-10-16 07:46:51', '2025-10-16 07:46:51'),
(8, 'Residential Design', 'دیزاینی نیشتەجێبوون', 'التصميم السكني', 'Modern residential construction and design solutions.', 'چارەسەرەکانی دیزاین و بیناسازی نیشتەجێبوونی مۆدێرن.', 'حلول التصميم والبناء السكني الحديث.', 'fas fa-home', NULL, NULL, 1, 2, '2025-10-16 07:46:51', '2025-10-16 07:46:51'),
(9, 'Infrastructure', 'ژێرخانی', 'البنية التحتية', 'Complete infrastructure development and maintenance.', 'پەرەپێدان و چاکردنەوەی ژێرخانی تەواو.', 'تطوير وصيانة البنية التحتية الكاملة.', 'fas fa-road', NULL, NULL, 1, 3, '2025-10-16 07:46:51', '2025-10-16 07:46:51'),
(10, 'Renovation', 'نوێکردنەوە', 'التجديد', 'Professional renovation and restoration services.', 'خزمەتگوزاری نوێکردنەوە و گەڕاندنەوەی پیشەیی.', 'خدمات التجديد والترميم المهنية.', 'fas fa-tools', NULL, NULL, 1, 4, '2025-10-16 07:46:51', '2025-10-16 07:46:51'),
(11, 'Exterior Design', 'دیزاینی دەرەوە', 'التصميم الخارجي', 'Beautiful exterior design and landscaping services.', 'خزمەتگوزاری دیزاینی دەرەوەی جوان و باخچەسازی.', 'خدمات التصميم الخارجي الجميل وتنسيق الحدائق.', 'fas fa-tree', NULL, NULL, 1, 5, '2025-10-16 07:46:51', '2025-10-16 07:46:51'),
(12, 'Interior Design', 'دیزاینی ناوەوە', 'التصميم الداخلي', 'Modern interior design and decoration services.', 'خزمەتگوزاری دیزاینی ناوەوەی مۆدێرن و جوانکاری.', 'خدمات التصميم الداخلي الحديث والديكور.', 'fas fa-couch', NULL, NULL, 1, 6, '2025-10-16 07:46:51', '2025-10-16 07:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `type` enum('text','number','boolean','json','file') DEFAULT 'text',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `description`, `is_public`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Demak Construction Company', 'text', 'Website name', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(2, 'site_name_ku', 'کۆمپانیای بیناسازی دەماک', 'text', 'Website name in Kurdish', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(3, 'site_name_ar', 'شركة دماك للإنشاءات', 'text', 'Website name in Arabic', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(4, 'site_description', 'Professional construction and building services in Kurdistan', 'text', 'Website description', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(5, 'site_description_ku', 'خزمەتگوزاری پیشەیی بیناسازی و بینا لە کوردستان', 'text', 'Website description in Kurdish', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(6, 'site_description_ar', 'خدمات البناء والإنشاء المهنية في كردستان', 'text', 'Website description in Arabic', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(7, 'contact_email', 'info@demak.com', 'text', 'Contact email', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(8, 'contact_phone', '+964 750 123 4567', 'text', 'Contact phone', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(9, 'contact_address', 'Erbil, Kurdistan Region, Iraq', 'text', 'Contact address', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(10, 'contact_address_ku', 'هەولێر، هەرێمی کوردستان، عێراق', 'text', 'Contact address in Kurdish', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(11, 'contact_address_ar', 'أربيل، إقليم كردستان، العراق', 'text', 'Contact address in Arabic', 1, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(12, 'maintenance_mode', '0', 'boolean', 'Maintenance mode', 0, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(13, 'max_file_size', '10485760', 'number', 'Maximum file upload size in bytes', 0, '2025-10-16 07:45:26', '2025-10-16 07:45:26'),
(14, 'allowed_file_types', 'jpg,jpeg,png,gif,pdf,doc,docx', 'text', 'Allowed file types for upload', 0, '2025-10-16 07:45:26', '2025-10-16 07:45:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `design_reconstruction_categories`
--
ALTER TABLE `design_reconstruction_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_key` (`category_key`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `design_reconstruction_features`
--
ALTER TABLE `design_reconstruction_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `design_reconstruction_images`
--
ALTER TABLE `design_reconstruction_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_main` (`is_main`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `design_reconstruction_projects`
--
ALTER TABLE `design_reconstruction_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_key`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `file_uploads`
--
ALTER TABLE `file_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_related` (`related_table`,`related_id`),
  ADD KEY `idx_file_type` (`file_type`),
  ADD KEY `idx_active` (`is_active`);

--
-- Indexes for table `infrastructure_categories`
--
ALTER TABLE `infrastructure_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `infrastructure_projects`
--
ALTER TABLE `infrastructure_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_category` (`category_key`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_table` (`table_name`),
  ADD KEY `idx_read` (`is_read`),
  ADD KEY `idx_created` (`created_at`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_featured` (`is_featured`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_created_by` (`created_by`),
  ADD KEY `idx_infrastructure_category` (`infrastructure_category`);

--
-- Indexes for table `project_downloads`
--
ALTER TABLE `project_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_file` (`file_id`),
  ADD KEY `idx_download_ip` (`download_ip`);

--
-- Indexes for table `project_engineers`
--
ALTER TABLE `project_engineers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `project_features`
--
ALTER TABLE `project_features`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `project_images`
--
ALTER TABLE `project_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_type` (`image_type`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `project_materials`
--
ALTER TABLE `project_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `project_quotes`
--
ALTER TABLE `project_quotes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_project` (`project_id`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_client_email` (`client_email`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_sort` (`sort_order`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`),
  ADD KEY `idx_key` (`key`),
  ADD KEY `idx_public` (`is_public`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `design_reconstruction_categories`
--
ALTER TABLE `design_reconstruction_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `design_reconstruction_features`
--
ALTER TABLE `design_reconstruction_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `design_reconstruction_images`
--
ALTER TABLE `design_reconstruction_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `design_reconstruction_projects`
--
ALTER TABLE `design_reconstruction_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `file_uploads`
--
ALTER TABLE `file_uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `infrastructure_categories`
--
ALTER TABLE `infrastructure_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `infrastructure_projects`
--
ALTER TABLE `infrastructure_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `project_downloads`
--
ALTER TABLE `project_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_engineers`
--
ALTER TABLE `project_engineers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `project_features`
--
ALTER TABLE `project_features`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `project_images`
--
ALTER TABLE `project_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `project_materials`
--
ALTER TABLE `project_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `project_quotes`
--
ALTER TABLE `project_quotes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `design_reconstruction_features`
--
ALTER TABLE `design_reconstruction_features`
  ADD CONSTRAINT `design_reconstruction_features_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `design_reconstruction_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `design_reconstruction_images`
--
ALTER TABLE `design_reconstruction_images`
  ADD CONSTRAINT `design_reconstruction_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `design_reconstruction_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `design_reconstruction_projects`
--
ALTER TABLE `design_reconstruction_projects`
  ADD CONSTRAINT `design_reconstruction_projects_ibfk_1` FOREIGN KEY (`category_key`) REFERENCES `design_reconstruction_categories` (`category_key`) ON DELETE CASCADE,
  ADD CONSTRAINT `design_reconstruction_projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `file_uploads`
--
ALTER TABLE `file_uploads`
  ADD CONSTRAINT `file_uploads_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `infrastructure_projects`
--
ALTER TABLE `infrastructure_projects`
  ADD CONSTRAINT `infrastructure_projects_ibfk_1` FOREIGN KEY (`category_key`) REFERENCES `infrastructure_categories` (`key`) ON DELETE CASCADE,
  ADD CONSTRAINT `infrastructure_projects_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`infrastructure_category`) REFERENCES `infrastructure_categories` (`key`) ON DELETE SET NULL;

--
-- Constraints for table `project_downloads`
--
ALTER TABLE `project_downloads`
  ADD CONSTRAINT `project_downloads_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `infrastructure_projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_downloads_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `file_uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_engineers`
--
ALTER TABLE `project_engineers`
  ADD CONSTRAINT `project_engineers_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `infrastructure_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_features`
--
ALTER TABLE `project_features`
  ADD CONSTRAINT `project_features_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `infrastructure_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_images`
--
ALTER TABLE `project_images`
  ADD CONSTRAINT `project_images_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `infrastructure_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_materials`
--
ALTER TABLE `project_materials`
  ADD CONSTRAINT `project_materials_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `infrastructure_projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_quotes`
--
ALTER TABLE `project_quotes`
  ADD CONSTRAINT `project_quotes_ibfk_1` FOREIGN KEY (`project_id`) REFERENCES `infrastructure_projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
