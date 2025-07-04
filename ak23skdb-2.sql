-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jul 02, 2025 at 12:49 PM
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
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Audio Plugins', 'audio-plugins', 'Plugin software for music production and effects.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', '2025-06-27 20:13:49'),
(2, 'Digital Audio Workstations', 'digital-audio-workstations', 'Full DAW software for composing and editing.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', '2025-07-02 12:12:19'),
(3, 'Kontakt Libraries', '\r\nkontakt-libraries', 'Sample libraries for Native Instruments Kontakt.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', '2025-07-02 06:05:56'),
(4, 'Audio Samples3', 'audio-samples', 'Single sample packs and loops.', NULL, 1, '2025-06-24 00:00:00', '2025-07-02 05:51:05', '2025-07-02 12:12:25'),
(5, 'Synth Presets', 'synth-presets', 'Presets for synths and softsynths.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(6, 'Plugins Tools & Utilities', 'plugins-tools-utilities', 'Tools like audio editors, uploaders, analyzers.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(7, 'Photo Editing Software', 'photo-editing-software', 'Tools for image editing and post-processing. and testing', NULL, 1, '2025-06-24 00:00:00', '2025-07-02 05:51:27', NULL),
(8, 'Video Editing Software', 'video-editing-software', 'Tools for video creation and post-production.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(9, 'Graphic Design Tools', 'graphic-design-tools', 'Software for creating and editing graphics.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(10, 'Screen Capture & Recorder', 'screen-capture-recorder', 'Applications to record or capture screen.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(11, 'Converters', 'converters', 'Conversion tools for media and documents.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(12, 'Security Tools', 'security-tools', 'Antivirus, malware removal and protection tools.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', '2025-06-27 20:13:42'),
(13, 'System Utilities', 'system-utilities', 'System maintenance and optimization tools.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(14, 'Download Managers', 'download-managers', 'Tools to manage and accelerate downloads.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(15, 'Office Tools', 'office-tools', 'Document creation and productivity tools.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(16, 'Audio Libraries', 'audio-libraries', 'Big multisample libraries for production.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(17, 'Activators', 'activators', 'Software activation and keygens.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', NULL),
(18, 'Operating Systems', 'operating-systems', 'Full OS installation packages.', NULL, 1, '2025-06-24 00:00:00', '2025-06-24 00:00:00', '2025-06-27 21:19:34'),
(19, 'RENATUS NDONGEYE KAYANDA', 'rn', 'renatus dev', NULL, 1, '2025-06-27 20:22:57', '2025-06-27 20:22:57', '2025-06-27 20:25:47'),
(20, 'AK23', 'shared-hosting', 'test', NULL, 1, '2025-06-27 20:24:59', '2025-06-27 20:24:59', '2025-06-27 20:41:24'),
(21, 'LOINESS BERNARDO', 'lo', 'testing factories', NULL, 1, '2025-06-27 20:45:45', '2025-06-27 20:45:45', '2025-06-27 20:47:57'),
(22, 'LOINESS', 'kata-standard', 'BERNARDO', NULL, 1, '2025-06-27 21:05:53', '2025-06-27 21:05:53', '2025-06-27 21:06:14'),
(23, 'TEST2', 'test1', 'TEST UNYAMA', NULL, 1, '2025-06-27 21:09:06', '2025-06-27 21:09:06', '2025-06-27 21:18:55');

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
-- Table structure for table `file_storage`
--

DROP TABLE IF EXISTS `file_storage`;
CREATE TABLE IF NOT EXISTS `file_storage` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g., Google Drive',
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `access_token` text COLLATE utf8mb4_unicode_ci,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `medias`
--

DROP TABLE IF EXISTS `medias`;
CREATE TABLE IF NOT EXISTS `medias` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `storage_config_id` int UNSIGNED DEFAULT NULL,
  `products_id` bigint UNSIGNED NOT NULL,
  `type` enum('upload','url') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Upload or URL-based media',
  `file_type` enum('image','zip','pdf','wav') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Type of media file',
  `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'File path or URL',
  `share_links` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Generated shareable URL',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_id` (`products_id`),
  KEY `fk_media_storage_config` (`storage_config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medias`
--

INSERT INTO `medias` (`id`, `storage_config_id`, `products_id`, `type`, `file_type`, `value`, `share_links`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 1, 'url', 'image', 'https://i1.sndcdn.com/artworks-000321524016-jw118f-t500x500.jpg', 'https://i1.sndcdn.com/artworks-000321524016-jw118f-t500x500.jpg', '2025-06-28 00:18:09', '2025-06-29 23:21:14', NULL);

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
  `download_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesapal_tracking_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesapal_merchant_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesapal_confirmation_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `download_expiry` datetime DEFAULT NULL,
  `download_count` int UNSIGNED NOT NULL DEFAULT '0',
  `last_downloaded_at` timestamp NULL DEFAULT NULL,
  `pesapal_reference` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evmak_reference` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `orders_user_id_foreign` (`user_id`),
  KEY `orders_product_id_foreign` (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `total`, `status`, `download_token`, `pesapal_tracking_id`, `pesapal_merchant_reference`, `pesapal_confirmation_code`, `download_expiry`, `download_count`, `last_downloaded_at`, `pesapal_reference`, `evmak_reference`, `paid_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 2, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-06-22 23:54:43', '2025-06-22 23:54:43', NULL),
(2, 2, 7, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, '2025-06-23 00:09:45', '2025-06-23 00:09:45', NULL),
(3, 1, 6, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, '57263a9c-3b34-4195-b310-dba5dcd35039', NULL, NULL, '2025-06-25 05:11:28', '2025-06-25 05:11:34', NULL),
(4, 3, 6, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'ee071279-8d4b-433e-977d-dba491478d7a', NULL, NULL, '2025-06-26 10:33:10', '2025-06-26 10:33:19', NULL),
(5, 3, 6, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, 'cdb3870f-9d61-4749-ba09-dba4fb16b114', NULL, NULL, '2025-06-26 14:22:44', '2025-06-26 14:22:48', NULL),
(6, 5, 6, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, NULL, '9d1b124c-10d0-44ee-822a-dba4afc53eb3', NULL, NULL, '2025-06-26 18:00:51', '2025-06-26 18:00:56', NULL),
(7, 6, 2, 24999.00, 'pending', '6fde3e9e697b6144087f3e414884044001a2193d3bd2ec09115e301ca2a2109c', NULL, NULL, NULL, '2025-07-01 10:27:23', 0, NULL, NULL, 'EVMK_1751106443_13719fa5', NULL, NULL, NULL, NULL),
(8, 7, 2, 24999.00, 'pending', 'ccdad20594ef264c1c49ac3bb98e1e628af640c1c3faf68aed9774d794425f58', NULL, NULL, NULL, '2025-07-01 10:29:12', 0, NULL, NULL, 'EVMK_1751106552_28d6dc9d', NULL, NULL, NULL, NULL),
(9, 8, 2, 24999.00, 'pending', '7413eb99a90fb85c7a1fc980c47ba58292685653333d3c752f80d4449bab9d40', NULL, NULL, NULL, '2025-07-01 10:32:02', 0, NULL, NULL, 'EVMK_1751106722_c6a01167', NULL, NULL, NULL, NULL),
(10, 9, 2, 24999.00, 'pending', 'e7cb07510972fe092c432f7b54542dc461bf2aa035ce6ed1b3ebba0a8a5099aa', NULL, NULL, NULL, '2025-07-01 10:35:26', 0, NULL, NULL, 'EVMK_1751106926_e95b5b5c', NULL, NULL, NULL, NULL),
(11, 10, 2, 24999.00, 'pending', 'e63409a79ff3c6804d20cd5ae32439fbb9eeb5ff190ccd4a41eb3d234ecd242d', NULL, NULL, NULL, '2025-07-01 10:53:11', 0, NULL, NULL, 'EVMK_1751107991_1ecc996c', NULL, NULL, NULL, NULL),
(12, 11, 4, 18900.00, 'paid', '7522d5593b52aebb8d46dd388d022139a0e5b7740acf002f026528d64352fa1d', 'b32dd943-f6dc-4c21-b1d0-dba170f854f7', 'AK23-1751216685-4', NULL, '2025-07-01 10:54:27', 0, NULL, 'Development Work TZ', 'EVMK_1751108067_1006423b', NULL, NULL, NULL, NULL),
(13, 12, 2, 24999.00, 'pending', '19ed8155da94796f4f83e00b9f3ab104bb239e16055d9d471b8e2b1a555bc3bc', NULL, NULL, NULL, '2025-07-01 11:09:38', 0, NULL, NULL, 'EVMK_1751108978_6f58d2a0', NULL, NULL, NULL, NULL),
(14, 13, 2, 24999.00, 'pending', '6430f2a972785707c37b3026d2473842a313fa0c398218fa7c7d4ecee72401a3', NULL, NULL, NULL, '2025-07-01 11:10:03', 0, NULL, NULL, 'EVMK_1751109003_c0b988a2', NULL, NULL, NULL, NULL),
(15, 14, 2, 24999.00, 'pending', '11f6911afecedc754911b3f0adb824473e899ba17a7c12bd31a9d724ddc38cea', NULL, NULL, NULL, '2025-07-01 12:19:24', 0, NULL, NULL, 'EVMK_1751113164_212614b8', NULL, NULL, NULL, NULL),
(16, 16, 2, 24999.00, 'pending', 'd2f75a1160960797654f438fce8b3c5f4df5f0629c56cbb7e470d6ae2814db17', NULL, NULL, NULL, '2025-07-02 15:51:57', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 17, 2, 24999.00, 'pending', 'b17f529034dbb75d0ea259517e1ffd3219fc797ba1c34ff5feb8dd033b4025ab', NULL, NULL, NULL, '2025-07-02 16:09:22', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 17, 3, 9900.00, 'pending', '3ae38cb678e89a15aed4e74cd8a6230567ecfb25642d45e725034f8afcb716b6', NULL, NULL, NULL, '2025-07-02 16:18:44', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 17, 3, 9900.00, 'pending', 'e35f9564faf4503f2c6778b83b573e46ae579a5789b14b4c8d844b09e3baf86f', NULL, NULL, NULL, '2025-07-02 16:46:48', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 18, 3, 9900.00, 'pending', 'd8431345baca9e9744dd0035e3f061fb43ed39567e7fe38863002f318e0c5d84', NULL, NULL, NULL, '2025-07-02 16:58:33', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 19, 3, 9900.00, 'pending', 'b0f4528946fcd2627e628ec08d559e3e943d27d9aaf7045e5d74a3e89a938159', NULL, NULL, NULL, '2025-07-02 16:59:01', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 20, 3, 9900.00, 'pending', 'c1499db27dfc8ed2f08332b806b5302a41c7a3d25826c771a3d4eefda7da3119', NULL, NULL, NULL, '2025-07-02 16:59:45', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_downloads`
--

DROP TABLE IF EXISTS `order_downloads`;
CREATE TABLE IF NOT EXISTS `order_downloads` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `storage_config_id` int UNSIGNED DEFAULT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `token` varchar(191) NOT NULL,
  `expiry` datetime NOT NULL,
  `download_count` int UNSIGNED DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `order_id` (`order_id`),
  KEY `fk_downloads_storage_config` (`storage_config_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3;

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
  `user_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_method` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evmak_transaction_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesapal_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pesapal_status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evmak_status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ipn_received` tinyint(1) NOT NULL DEFAULT '0',
  `status` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_details` json DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payments_transaction_id_unique` (`transaction_id`),
  KEY `payments_order_id_foreign` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `user_id`, `order_id`, `amount`, `phone`, `payment_method`, `transaction_id`, `evmak_transaction_id`, `pesapal_transaction_id`, `reference`, `pesapal_status`, `evmak_status`, `ipn_received`, `status`, `payment_details`, `paid_at`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 0, 3, 35000.00, '0655991973', '', NULL, NULL, NULL, '57263a9c-3b34-4195-b310-dba5dcd35039', '500', NULL, 0, NULL, '{\"card_type\": \"Visa\", \"last_four\": \"1234\"}', NULL, NULL, '2025-06-25 05:11:34', '2025-06-25 05:11:45', NULL),
(2, 0, 4, 35000.00, '0747003357', 'pesapal', NULL, NULL, NULL, 'ee071279-8d4b-433e-977d-dba491478d7a', 'pending', NULL, 0, NULL, NULL, NULL, NULL, '2025-06-26 10:33:19', '2025-06-26 10:33:19', NULL),
(3, 0, 5, 35000.00, '0747003357', 'pesapal', NULL, NULL, NULL, 'cdb3870f-9d61-4749-ba09-dba4fb16b114', 'pending', NULL, 0, NULL, NULL, NULL, NULL, '2025-06-26 14:22:48', '2025-06-26 14:22:48', NULL),
(4, 6, 6, 35000.00, '0655991973', 'TigoPesa', NULL, NULL, NULL, '9d1b124c-10d0-44ee-822a-dba4afc53eb3', 'pending', NULL, 0, 'completed', NULL, NULL, NULL, '2025-06-26 18:00:56', '2025-07-02 06:03:41', NULL),
(5, 1, 0, 12000.00, NULL, 'TigoPesa', NULL, NULL, NULL, 'TXN001', NULL, NULL, 0, 'completed', NULL, NULL, NULL, '2025-06-26 09:00:00', '2025-06-26 09:00:00', NULL),
(6, 2, 0, 45000.00, NULL, 'AirtelMoney', NULL, NULL, NULL, 'TXN002', NULL, NULL, 0, 'pending', NULL, NULL, NULL, '2025-06-26 10:00:00', '2025-06-26 10:00:00', NULL),
(7, 4, 0, 20000.00, NULL, 'Mpesa', NULL, NULL, NULL, 'TXN003', NULL, NULL, 0, 'completed', NULL, NULL, NULL, '2025-06-26 12:30:00', '2025-06-26 12:30:00', NULL),
(8, 5, 0, 5000.00, NULL, 'Cash', NULL, NULL, NULL, 'TXN004', NULL, NULL, 0, 'completed', NULL, NULL, NULL, '2025-06-26 13:00:00', '2025-06-26 13:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
CREATE TABLE IF NOT EXISTS `payment_methods` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'e.g., Pesapal, EvMak',
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `storage_config_id` int UNSIGNED DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('upload','url') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'General type if needed at top-level',
  `files` json DEFAULT NULL COMMENT 'Array of downloadable files: {type: url|upload, file_type: zip/pdf/wav, value: path or URL}',
  `images` json DEFAULT NULL COMMENT 'Array of image entries: {type: url|upload, value: image path or URL}',
  `license` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'Commercial',
  `file_size` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '120 MB',
  `publisher` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'AK23STUDIOKITS',
  `password_hint` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'ak23studiokits.com',
  `rating` float DEFAULT '4.9',
  `votes` int DEFAULT '128',
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `features` json DEFAULT NULL COMMENT 'Array of features: {title, description}',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_products_storage_config` (`storage_config_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `storage_config_id`, `category_id`, `name`, `slug`, `price`, `description`, `type`, `files`, `images`, `license`, `file_size`, `publisher`, `password_hint`, `rating`, `votes`, `is_featured`, `is_active`, `features`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 8, 'Lo-Fi Beat Kit Vol. 1', 'lofi-beat-kit-vol-1', 2000.00, 'Warm dusty loops and textures for Lo-Fi production.', 'url', '[{\"type\": \"upload\", \"value\": \"uploads/lofi_kit.zip\", \"file_type\": \"zip\"}]', '[{\"type\": \"upload\", \"value\": \"images/lofi_kit.jpg\"}]', 'Royalty-Free', '340 MB', 'LoFi Labs', 'lofiworld.com', 4.6, 145, 1, 1, '[{\"title\": \"Analog Vibes\", \"description\": \"Sounds recorded from vintage gear.\"}, {\"title\": \"Drag and Drop Ready\", \"description\": \"Works in all DAWs.\"}]', '2025-06-27 09:00:00', '2025-06-29 23:17:29', NULL),
(2, NULL, 2, 'Trap Essentials Vol. 2', 'trap-essentials-vol-2', 24999.00, 'Next-gen trap sounds including 808s, snares, and loops.', 'upload', '[{\"type\": \"upload\", \"value\": \"uploads/trap_essentials_2.wav\", \"file_type\": \"wav\"}, {\"type\": \"url\", \"value\": \"https://kits.com/docs/trap2-guide.pdf\", \"file_type\": \"pdf\"}]', '[{\"type\": \"upload\", \"value\": \"images/trap2_cover.jpg\"}, {\"type\": \"url\", \"value\": \"https://kits.com/images/trap2-banner.jpg\"}]', 'Commercial', '210 MB', 'TrapProducers', 'trappro.com', 4.8, 302, 0, 1, '[{\"title\": \"Hard 808s\", \"description\": \"Thick and punchy 808s.\"}, {\"title\": \"Custom Loops\", \"description\": \"Unique royalty-free melodies.\"}]', '2025-06-26 06:00:00', '2025-06-26 06:00:00', NULL),
(3, NULL, 3, 'Ultimate Vocal FX', 'ultimate-vocal-fx', 9900.00, 'FX pack with shouts, transitions, and vocal drops.', 'url', '[{\"type\": \"url\", \"value\": \"https://files.example.com/vocal_fx.zip\", \"file_type\": \"zip\"}]', '[{\"type\": \"url\", \"value\": \"https://files.example.com/images/vocalfx-banner.jpg\"}]', 'Commercial', '120 MB', 'FXLab', 'vocalzone.com', 4.4, 98, 0, 1, '[{\"title\": \"Dry + Wet Versions\", \"description\": \"Use raw or processed.\"}, {\"title\": \"One-Shots\", \"description\": \"Quick-use vocal elements.\"}]', '2025-06-20 13:33:00', '2025-06-20 13:33:00', NULL),
(4, NULL, 4, 'R&B Melody Loops', 'rnb-melody-loops', 18900.00, 'Smooth and soulful melodies perfect for R&B and Soul.', 'upload', '[{\"type\": \"upload\", \"value\": \"uploads/rnb_melodies.zip\", \"file_type\": \"zip\"}]', '[{\"type\": \"upload\", \"value\": \"images/rnb_pack.jpg\"}]', 'Commercial', '275 MB', 'RnBSounds', 'rnbpack.com', 4.7, 154, 1, 1, '[{\"title\": \"Soulful Keys\", \"description\": \"Melodic chord progressions.\"}, {\"title\": \"Multi-Key Formats\", \"description\": \"Loops in various scales.\"}]', '2025-06-25 10:40:00', '2025-06-25 10:40:00', NULL),
(5, NULL, 5, 'EDM Drop FX Kit', 'edm-drop-fx-kit', 12900.00, 'High-energy risers, impacts, and transitions for EDM.', 'url', '[{\"type\": \"url\", \"value\": \"https://cdn.fxkits.com/edm_drops.zip\", \"file_type\": \"zip\"}]', '[{\"type\": \"url\", \"value\": \"https://cdn.fxkits.com/img/edm_cover.jpg\"}]', 'Commercial', '300 MB', 'FXEmpire', 'fxempire.com', 4.9, 412, 0, 1, '[{\"title\": \"Drag-Ready FX\", \"description\": \"Simple use in any DAW.\"}, {\"title\": \"Energy Builders\", \"description\": \"Perfect for build-ups and transitions.\"}, {\"title\": \"Bonus Pack\", \"description\": \"Includes 10 bonus sound effects.\"}]', '2025-06-24 07:20:00', '2025-06-27 23:30:27', '2025-06-27 23:30:27');

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
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`, `created_at`, `updated_at`) VALUES
(1, 'system_name', 'AK23StudioKITS', '2025-06-29 22:45:13', '2025-07-02 05:13:01');

-- --------------------------------------------------------

--
-- Table structure for table `storage_config`
--

DROP TABLE IF EXISTS `storage_config`;
CREATE TABLE IF NOT EXISTS `storage_config` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `provider` varchar(100) NOT NULL,
  `client_id` varchar(255) DEFAULT NULL,
  `client_secret` varchar(255) DEFAULT NULL,
  `redirect_uri` varchar(255) DEFAULT NULL,
  `bucket_name` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `storage_config`
--

INSERT INTO `storage_config` (`id`, `provider`, `client_id`, `client_secret`, `redirect_uri`, `bucket_name`, `is_active`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'google_drive', 'google-client-id', 'google-client-secret', 'https://dummy-redirect-uri.com/google', 'google-bucket', 1, '2025-06-29 22:55:48', '2025-06-29 22:55:48', NULL),
(2, 'aws_s3', 'aws-access-key', 'aws-secret-key', 'https://dummy-redirect-uri.com/aws', 'aws-bucket', 0, '2025-06-29 22:55:48', '2025-06-29 22:55:48', NULL),
(3, 'dropbox', 'dropbox-client-id', 'dropbox-client-secret', 'https://dummy-redirect-uri.com/dropbox', 'dropbox-folder', 0, '2025-06-29 22:55:48', '2025-06-29 22:55:48', NULL),
(4, 'local', NULL, NULL, NULL, 'local-folder', 0, '2025-06-29 22:55:48', '2025-06-29 22:55:48', NULL);

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
  `temp_password` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_verified_at` timestamp NULL DEFAULT NULL,
  `address` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `postal_code` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_photo_path` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_guest` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `email_verified_at`, `password`, `temp_password`, `phone`, `mobile_verified_at`, `address`, `city`, `state`, `postal_code`, `country`, `profile_photo_path`, `is_admin`, `is_guest`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Admin User', 'admin@example.com', '2025-06-26 10:27:42', '$2y$12$EYI1hYB0ONiZletKwcmivOabgRJggQjpFEoSYMj2/M1hFztHGQFPm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '2025-06-26 10:27:42', '2025-06-26 10:27:42', NULL),
(2, 2, 'Super Admin', 'info@wolinet.com', '2025-06-26 10:27:43', '$2y$12$nfcjObkzd6Zcn04qYzAGtOgRoqfMzJ3XiT8NGathjWe990.u6QC/W', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '2025-06-26 10:27:43', '2025-06-26 10:27:43', NULL),
(3, 2, 'Developer', 'dev@wolinet.com', '2025-06-26 10:27:43', '$2y$12$y9iiU9ZeQlwGGtq97inDWe5WZs8pk12CkUvVj93IyH9bjI0PFOkJ.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, '2025-06-26 10:27:43', '2025-06-26 10:27:43', NULL),
(4, 2, 'RENATUS BERNARD', 'tanzanitehost@gmail.com', NULL, '$2y$12$BCILluDRAH8pHpNiTHHlEuUE97UlxEWeDTqxsBNWEDH1xAqxFEsTa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, '2025-06-26 10:53:25', '2025-06-26 10:53:25', NULL),
(5, 2, 'Guest_HWtDCY', 'guest_1750971651_0655991973@ak23studiokits.com', NULL, '$2y$12$Gvf13Ws0UC3EKmmmd.m.jePay20Kg65YMo1seqWz/1a5Y7zPnj3Xq', NULL, '0655991973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 'Nd1yk9HQDz3iDxAPU8ymhkqiYzLoM1AoU1APb9BczGyTbDXhhq1WWXr4LdYC', '2025-06-26 18:00:51', '2025-06-26 18:00:51', NULL),
(6, 2, 'Guest_3357', 'guest_1751106443_255747003357@ak23studiokits.com', NULL, '$2y$10$dr98Y58fLpqWan83crLVK.zhXOG/LPHByzLjVmt1zm6PYLdJiCKjm', '1b92c5a46b40', '255747003357', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(7, 2, 'Guest_1973', 'guest_1751106552_255655991973@ak23studiokits.com', NULL, '$2y$10$HxDzFSvI6Wu91HH7R6sHjui/34LYyeyECFZPOPJYesbhSeQ3BEgKy', '355ee2395de6', '255655991973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(8, 2, 'Guest_1973', 'guest_1751106721_255655991973@ak23studiokits.com', NULL, '$2y$10$4b9L8/wZUCNuYU6PeXCBP.kEyjK/IYrCislG6y7DSazERVlMDZzuy', 'aee08049383d', '255655991973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(9, 2, 'Guest_1973', 'guest_1751106926_255655991973@ak23studiokits.com', NULL, '$2y$10$JBiY/VpI3.gZ.Znsy12WAO6AXzPcyT5mcyorkYQ.N/gxy7Pjsh3Xa', '04676cf631af', '255655991973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(10, 2, 'Guest_3357', 'guest_1751107991_255747003357@ak23studiokits.com', NULL, '$2y$10$vgV85hKBwL7QUYn84oNy7.CXC2p8e9HcUPHHB/ImcM21SZd4A7W7y', '5969f497ca44', '255747003357', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(11, 2, 'Guest_1973', 'guest_1751108067_255655991973@ak23studiokits.com', NULL, '$2y$10$FMrfoh4daB8Fv1i2J7XgJu53l79V3waBZuvwsXHmqkcT8nF0VrQ6W', 'f188d456566f', '255655991973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(12, 2, 'Guest_3357', 'guest_1751108978_255747003357@ak23studiokits.com', NULL, '$2y$10$yKNImH9ham.YHBMIcsMpi.Du0rTAGX7f2nm36xziQK/6yKXvfUjDa', '23f9cc2525df', '255747003357', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(13, 2, 'Guest_1973', 'guest_1751109003_255655991973@ak23studiokits.com', NULL, '$2y$10$LlbgHa/vjM2mj4GKAilWhuB6dBcbZrGrsVoN7O0SfzGRqZFJk5xAS', 'c426c58658fd', '255655991973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(14, 2, 'Guest_3357', 'guest_1751113163_255747003357@ak23studiokits.com', NULL, '$2y$10$2n14nrh.xjgVcRPMr/UpaenvFyRUtSDn3RcRTiaygJrYzNNQ6TvG2', '47b9cdb7233c', '255747003357', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(15, 2, 'Guest_1751212134_9597', 'bernadorenatus39@gmail.com', NULL, '$2y$10$kFEV9X7tnCNT5QaRlF9bneAnHmp5dpKmgVhODJGXDidq.QJxCfcXK', '7ed12fdbacb0', '255686539597', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(16, 2, 'Guest_1751212317_6058', 'felix@gmail.com', NULL, '$2y$10$BKoO2.euezT7naKsO3UtVeHi7tWkksNf72szAP.3wWzF0hU/zQU5G', '5cb96c35ca31', '255754816058', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(17, 2, 'Guest_1751213362_8495', 'patrick@gmail.com', NULL, '$2y$10$RXeT.g4s43W6Kyi99SHQnuLMi7kkrsMqS.GDCXEjaKLCVmfC6E02m', '115d21713c1c', '255767798495', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(18, 2, 'Guest_1751216313_1773', 'tanzanitehost12@gmail.com', NULL, '$2y$10$96unyHzqb8/eAE659Mh1SOavaJVU8ubHJPPtDIoWWjjsbbqohRS12', '754549befb81', '255655991773', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(19, 2, 'Guest_1751216341_1973', 'tanzanitehost123@gmail.com', NULL, '$2y$10$d5UI3XzFKly4BbchTC0brOHtL.Qj82aI2eHF8Wu2SYasbxcZnK7XO', '2b9da1351753', '255655991973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(20, 2, 'Guest_1751216385_3374', 'tanzanitehost143@gmail.com', NULL, '$2y$10$PQU4oKEuZFArV9w3.SPvseVJefRNWnV8nBUJEzb2LOA3wFViwTjOa', '042f16f1451b', '255747003374', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(21, 2, 'Guest_1751216685_9597', 'wenstore36@gmail.com', NULL, '$2y$10$yeT/LryZh4bPtnL93fGGWerlgFM3QBvF.qNugk6BUq.1Ftxlu50Vy', 'de6aff85ee60', '255686539597', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(22, 2, 'Guest_1751218290_8899', 'info23@wolinet.com', NULL, '$2y$10$jSpqc8BVWlVUvXvkKW.yd.SeBYnUnljeQAsJrmDkkdC6rD.LVkMAG', '4e9a68edd28d', '255784868899', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(23, 2, 'Guest_1751232189_3357', 'kigodimeWet@gmail.com', NULL, '$2y$10$/zKP9aDUzqIi2/ekEfQlNuDA4ifY2.cZdyc4oB3qb0tydHPQ4x3CG', '8fe7551fdebd', '255747003357', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL),
(24, 2, 'Guest_1751377924_1773', 'info28@wolinet.com', NULL, '$2y$10$6CPvOfunACZCjk/GlutmdOV6zzkTD9ApQC4fV8uUplw6UZtaezU.2', '2a45fab8291a', '255655991773', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, NULL);

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

--
-- Constraints for dumped tables
--

--
-- Constraints for table `medias`
--
ALTER TABLE `medias`
  ADD CONSTRAINT `fk_media_storage_config` FOREIGN KEY (`storage_config_id`) REFERENCES `storage_config` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `medias_ibfk_1` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_storage_config` FOREIGN KEY (`storage_config_id`) REFERENCES `storage_config` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
