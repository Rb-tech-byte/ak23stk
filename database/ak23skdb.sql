-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 27, 2025 at 08:29 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ak23skdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `log_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint UNSIGNED DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint UNSIGNED DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `activity_log_subject_type_subject_id_index` (`subject_type`,`subject_id`),
  KEY `activity_log_causer_type_causer_id_index` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cache_key_unique` (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks_ta`
--

DROP TABLE IF EXISTS `cache_locks_ta`;
CREATE TABLE IF NOT EXISTS `cache_locks_ta` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `image` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Audio Plugins', 'audio-plugins', 'Plugin software for music production and effects.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(2, 'Digital Audio Workstations', 'digital-audio-workstations', 'Full DAW software for composing and editing.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(3, 'Kontakt Libraries', '\r\nkontakt-libraries', 'Sample libraries for Native Instruments Kontakt.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(4, 'Audio Samples', 'audio-samples', 'Single sample packs and loops.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(5, 'Synth Presets', 'synth-presets', 'Presets for synths and softsynths.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(6, 'Plugins Tools & Utilities', 'plugins-tools-utilities', 'Tools like audio editors, uploaders, analyzers.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(7, 'Photo Editing Software', 'photo-editing-software', 'Tools for image editing and post-processing.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(8, 'Video Editing Software', 'video-editing-software', 'Tools for video creation and post-production.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(9, 'Graphic Design Tools', 'graphic-design-tools', 'Software for creating and editing graphics.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(10, 'Screen Capture & Recorder', 'screen-capture-recorder', 'Applications to record or capture screen.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(11, 'Converters', 'converters', 'Conversion tools for media and documents.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(12, 'Security Tools', 'security-tools', 'Antivirus, malware removal and protection tools.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(13, 'System Utilities', 'system-utilities', 'System maintenance and optimization tools.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(14, 'Download Managers', 'download-managers', 'Tools to manage and accelerate downloads.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(15, 'Office Tools', 'office-tools', 'Document creation and productivity tools.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(16, 'Audio Libraries', 'audio-libraries', 'Big multisample libraries for production.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(17, 'Activators', 'activators', 'Software activation and keygens.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(18, 'Operating Systems', 'operating-systems', 'Full OS installation packages.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2024_06_25_000000_create_reviews_table', 1),
(2, '2025_06_24_000001_create_users_table', 1),
(3, '2025_06_24_000002_create_roles_table', 1),
(4, '2025_06_24_202111_create_tickets_table', 1),
(5, '2025_06_24_202304_create_ticket_replies_table', 1),
(6, '2025_06_24_214954_add_role_id_to_users_table', 1),
(7, '2025_06_25_000000_add_guard_name_to_roles_table', 1),
(8, '2025_06_25_000001_drop_permission_tables', 1),
(9, '2025_06_25_000002_create_permission_tables', 1),
(10, '2025_06_25_072000_setup_permission_tables', 1),
(11, '2025_06_25_073000_add_guard_name_to_roles_table', 1),
(12, '2025_06_25_093750_create_wishlists_table', 1),
(13, '2025_06_26_000001_create_activity_log_table', 2),
(14, '2025_06_26_000002_add_soft_deletes_to_categories_and_products', 3);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','paid','completed','failed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `download_count` int UNSIGNED NOT NULL DEFAULT '0',
  `last_downloaded_at` timestamp NULL DEFAULT NULL,
  `pesapal_reference` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `total`, `status`, `download_count`, `last_downloaded_at`, `pesapal_reference`, `paid_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 2, 0.00, 'pending', 0, NULL, NULL, NULL, '2025-06-22 23:54:43', '2025-06-22 23:54:43', NULL),
(2, 2, 7, 0.00, 'pending', 0, NULL, NULL, NULL, '2025-06-23 00:09:45', '2025-06-23 00:09:45', NULL),
(3, 1, 6, 0.00, 'pending', 0, NULL, '57263a9c-3b34-4195-b310-dba5dcd35039', NULL, '2025-06-25 05:11:28', '2025-06-25 05:11:34', NULL),
(4, 3, 6, 0.00, 'pending', 0, NULL, 'ee071279-8d4b-433e-977d-dba491478d7a', NULL, '2025-06-26 10:33:10', '2025-06-26 10:33:19', NULL),
(5, 3, 6, 0.00, 'pending', 0, NULL, 'cdb3870f-9d61-4749-ba09-dba4fb16b114', NULL, '2025-06-26 14:22:44', '2025-06-26 14:22:48', NULL),
(6, 5, 6, 0.00, 'pending', 0, NULL, '9d1b124c-10d0-44ee-822a-dba4afc53eb3', NULL, '2025-06-26 18:00:51', '2025-06-26 18:00:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  KEY `order_items_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE IF NOT EXISTS `payments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesapal_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipn_received` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_details` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  KEY `payments_order_id_foreign` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `amount`, `phone`, `payment_method`, `transaction_id`, `reference`, `pesapal_status`, `ipn_received`, `status`, `payment_details`, `paid_at`, `notes`, `created_at`, `updated_at`) VALUES
(1, 3, 35000.00, '0655991973', '', NULL, '57263a9c-3b34-4195-b310-dba5dcd35039', '500', 0, NULL, NULL, NULL, NULL, '2025-06-25 05:11:34', '2025-06-25 05:11:45'),
(2, 4, 35000.00, '0747003357', 'pesapal', NULL, 'ee071279-8d4b-433e-977d-dba491478d7a', 'pending', 0, NULL, NULL, NULL, NULL, '2025-06-26 10:33:19', '2025-06-26 10:33:19'),
(3, 5, 35000.00, '0747003357', 'pesapal', NULL, 'cdb3870f-9d61-4749-ba09-dba4fb16b114', 'pending', 0, NULL, NULL, NULL, NULL, '2025-06-26 14:22:48', '2025-06-26 14:22:48'),
(4, 6, 35000.00, '0655991973', 'pesapal', NULL, '9d1b124c-10d0-44ee-822a-dba4afc53eb3', 'pending', 0, NULL, NULL, NULL, NULL, '2025-06-26 18:00:56', '2025-06-26 18:00:56');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'sysmpanel.dashboard.view', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(2, 'sysmpanel.categories.view', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(3, 'sysmpanel.categories.create', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(4, 'sysmpanel.categories.edit', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(5, 'sysmpanel.categories.delete', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(6, 'sysmpanel.products.view', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(7, 'sysmpanel.products.create', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(8, 'sysmpanel.products.edit', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55'),
(9, 'sysmpanel.products.delete', 'web', '2025-06-26 17:21:55', '2025-06-26 17:21:55');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Commercial',
  `file_size` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '120 MB',
  `publisher` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'AK23STUDIOKITS',
  `password_hint` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'ak23studiokits.com',
  `rating` float DEFAULT '4.9',
  `votes` int DEFAULT '128',
  `is_featured` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_slug_unique` (`slug`),
  KEY `products_category_id_foreign` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `price`, `description`, `image`, `license`, `file_size`, `publisher`, `password_hint`, `rating`, `votes`, `is_featured`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Premium Melody Pack Vol. 1', 'premium-melody-pack-vol-1', 25000.00, '100+ royalty-free melodies for Hip-Hop, R&B, and Trap.', 'https://ak23studiokits.com/wp-content/uploads/2025/05/custom-image-08-copyright.jpg', 'Commercial', '3420 MB', 'AK23STUDIOKITS', 'ak23studiokits.com', 4.9, 128, 0, 1, '2025-06-21 04:48:10', '2025-06-21 04:48:10', NULL),
(2, 2, 'Massive X - Synth Collection', 'massive-x-synth-collection', 45000.00, '200+ presets for Native Instruments Massive X.', 'https://ak23studiokits.com/wp-content/uploads/2025/05/image-13-copyright.jpg', 'Commercial', '120 MB', 'AK23STUDIOKITS', 'ak23studiokits.com', 4.9, 128, 0, 1, '2025-06-21 04:48:10', '2025-06-21 04:48:10', NULL),
(3, 3, 'Afrobeat Production Template', 'afrobeat-production-template', 30000.00, 'FL Studio template for Afrobeat production.', NULL, 'Commercial', '120 MB', 'AK23STUDIOKITS', 'ak23studiokits.com', 4.9, 128, 0, 1, '2025-06-21 04:48:10', '2025-06-21 04:48:10', NULL),
(4, 4, 'Ultimate Drum Kit 2023', 'ultimate-drum-kit-2023', 35000.00, '1000+ drum samples for any genre.', NULL, 'Commercial', '120 MB', 'AK23STUDIOKITS', 'ak23studiokits.com', 4.9, 128, 0, 1, '2025-06-21 04:48:10', '2025-06-21 04:48:10', NULL),
(5, 2, 'Trap Essentials Preset Bank', 'trap-essentials-preset-bank-1', 20000.00, 'Essential presets for Trap music production.', NULL, 'Commercial', '120 MB', 'AK23STUDIOKITS', 'ak23studiokits.com', 4.9, 128, 0, 1, '2025-06-21 04:48:10', '2025-06-21 04:48:10', NULL),
(6, 1, 'SmartReverb v1.2', 'smartreverb-v1-2', 35000.00, 'SmartReverb is a next-gen reverb plugin with AI-powered algorithms for natural sound.', 'smartreverb.jpg', 'Commercial', '45 MB', 'Sonible', 'ak23studiokits.com', 4.8, 210, 0, 1, '2025-06-21 08:21:56', '2025-06-21 08:21:56', NULL),
(7, 2, 'Trap Essentials Preset Bank 2', 'trap-essentials-preset-bank-2', 1220000.00, 'Essential presets for Trap music production.', 'trap-essentials.jpg', 'Commercial', '20 MB', 'AK23STUDIOKITS', 'AK23', 4.7, 98, 0, 1, '2025-06-21 08:21:56', '2025-06-21 08:21:56', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_features`
--

DROP TABLE IF EXISTS `product_features`;
CREATE TABLE IF NOT EXISTS `product_features` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_features_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_features`
--

INSERT INTO `product_features` (`id`, `product_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'AI-Powered Reverb', 'Automatically adapts to your audio for a natural sound.', NULL, NULL),
(2, 1, 'Intuitive Interface', 'Easy-to-use controls with real-time visual feedback.', NULL, NULL),
(3, 1, 'Presets', 'Includes 50+ factory presets for instant results.', NULL, NULL),
(4, 1, 'Low CPU Usage', 'Optimized for efficient performance in any DAW.', NULL, NULL),
(5, 2, '808 Bass', 'Hard-hitting 808s for modern trap beats.', NULL, NULL),
(6, 2, 'Leads & Plucks', 'Unique leads and plucks for catchy melodies.', NULL, NULL),
(7, 2, 'Pads', 'Lush pads for atmospheric layers.', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
CREATE TABLE IF NOT EXISTS `product_images` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_id` bigint UNSIGNED NOT NULL,
  `image_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('upload','url') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'upload',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_images_product_id_foreign` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `image_path`, `image_url`, `type`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'https://ak23studiokits.com/wp-content/uploads/2025/05/custom-image-08-copyright.jpg', 'url', '2025-06-21 14:19:59', '2025-06-21 14:19:59'),
(2, 1, NULL, 'https://ak23studiokits.com/wp-content/uploads/2025/05/image-13-copyright.jpg', 'url', '2025-06-21 14:19:59', '2025-06-21 14:19:59'),
(3, 1, 'products/premium_melody_thumb.jpg', NULL, 'upload', '2025-06-21 14:19:59', '2025-06-21 14:19:59'),
(4, 2, 'products/massivex_main.jpg', NULL, 'upload', '2025-06-21 14:19:59', '2025-06-21 14:19:59'),
(5, 2, 'products/massivex_thumb1.jpg', NULL, 'upload', '2025-06-21 14:19:59', '2025-06-21 14:19:59'),
(6, 3, 'products/afrobeat_template_main.jpg', NULL, 'upload', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(7, 3, NULL, 'https://dummyimages.com/afrobeat_cover.jpg', 'url', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(8, 4, 'products/drumkit2023.jpg', NULL, 'upload', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(9, 4, NULL, 'https://dummyimages.com/drumkit_banner.jpg', 'url', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(10, 5, NULL, 'https://ak23studiokits.com/images/trap-essentials-banner.jpg', 'url', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(11, 5, 'products/trapessentials_alt.jpg', NULL, 'upload', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(12, 6, 'products/smartreverb_thumb2.jpg', NULL, 'upload', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(13, 6, NULL, 'https://dummyimages.com/smartreverb_ui.jpg', 'url', '2025-06-21 14:20:00', '2025-06-21 14:20:00'),
(14, 7, 'products/trapessentials_thumb2.jpg', NULL, 'upload', '2025-06-21 14:20:00', '2025-06-21 14:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reviews_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `reviews_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(125) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'sysmpanel-admin', 'web', '2025-06-26 17:09:21', '2025-06-26 17:09:21'),
(2, 'sysmpanel-editor', 'web', '2025-06-26 17:09:21', '2025-06-26 17:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(2, 1),
(2, 2),
(3, 1),
(3, 2),
(4, 1),
(4, 2),
(5, 1),
(6, 1),
(6, 2),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('3n4Hscklm8js1xeqgfnjsGAdgORK4rJsi3m8Lb9Z', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiV3VkVzlpdUkwRGZsazRXbnB4aGZOMmJoUFdNSGJGZlo2ZFVITFViUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zeXNtcGFuZWwiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1750972212),
('Y2VBhlxDdH0oxGPB2wzlC6qqdE0UHtKaXWqlAx4S', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidDgwb1h3NmoyN2ZRWWx2THBPaDVoUFpvczdLQW1pTTFQV285UmxxVCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4vZGFzaGJvYXJkIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1750958652),
('h6Bb63Pz9uXuSwdxOECtaScAfAQ8FTVgsGB6yeWC', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaUZqeldrTmx2aHBRaE5WNW1ER2wzNnhJNXAxRFo0ZDV1RFJPdTRzMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1750965442),
('zmDI8OYkgNZV5zvv49Tff70z5iM77ZlnXjI8Gwnd', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiREZSaldjdGhCdGFhTllqY0ZYNnU5ajUzYW9uZmplUENnQWRSZWZFWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYWRtaW4iO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1750964607),
('MBUnpEhazFQIPNuhxM1drGZWBKelPI7mY4UYm7R4', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiT3psSVFncDNmaEVGT3FqWVdKMEJUOXV3ZjFPekFKMXhkUUpSZ3lWQiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjMxOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvc3lzbXBhbmVsIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1750971548);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `subject` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `priority` enum('low','medium','high') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tickets_user_id_foreign` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_replies`
--

DROP TABLE IF EXISTS `ticket_replies`;
CREATE TABLE IF NOT EXISTS `ticket_replies` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `ticket_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_replies_user_id_foreign` (`user_id`),
  KEY `ticket_replies_ticket_id_index` (`ticket_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` bigint UNSIGNED NOT NULL DEFAULT '2',
  `name` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `email_verified_at`, `password`, `phone`, `address`, `city`, `state`, `postal_code`, `country`, `profile_photo_path`, `is_admin`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Admin User', 'admin@example.com', '2025-06-26 10:27:42', '$2y$12$EYI1hYB0ONiZletKwcmivOabgRJggQjpFEoSYMj2/M1hFztHGQFPm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-26 10:27:42', '2025-06-26 10:27:42', NULL),
(2, 2, 'Super Admin', 'info@wolinet.com', '2025-06-26 10:27:43', '$2y$12$nfcjObkzd6Zcn04qYzAGtOgRoqfMzJ3XiT8NGathjWe990.u6QC/W', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-26 10:27:43', '2025-06-26 10:27:43', NULL),
(3, 2, 'Developer', 'dev@wolinet.com', '2025-06-26 10:27:43', '$2y$12$y9iiU9ZeQlwGGtq97inDWe5WZs8pk12CkUvVj93IyH9bjI0PFOkJ.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL, '2025-06-26 10:27:43', '2025-06-26 10:27:43', NULL),
(4, 2, 'RENATUS BERNARD', 'tanzanitehost@gmail.com', NULL, '$2y$12$BCILluDRAH8pHpNiTHHlEuUE97UlxEWeDTqxsBNWEDH1xAqxFEsTa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, '2025-06-26 10:53:25', '2025-06-26 10:53:25', NULL),
(5, 2, 'Guest_HWtDCY', 'guest_1750971651_0655991973@ak23studiokits.com', NULL, '$2y$12$Gvf13Ws0UC3EKmmmd.m.jePay20Kg65YMo1seqWz/1a5Y7zPnj3Xq', '0655991973', NULL, NULL, NULL, NULL, NULL, NULL, 0, 'Nd1yk9HQDz3iDxAPU8ymhkqiYzLoM1AoU1APb9BczGyTbDXhhq1WWXr4LdYC', '2025-06-26 18:00:51', '2025-06-26 18:00:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_id_product_id_unique` (`user_id`,`product_id`),
  KEY `wishlists_product_id_foreign` (`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
