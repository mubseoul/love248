-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Apr 24, 2025 at 11:29 PM
-- Server version: 5.7.39
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `love248`
--

-- --------------------------------------------------------

--
-- Table structure for table `bank_customers`
--

CREATE TABLE `bank_customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `customer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `document` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banned`
--

CREATE TABLE `banned` (
  `id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `category`, `icon`) VALUES
(29, 'white', NULL),
(30, 'land', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `category_user`
--

CREATE TABLE `category_user` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int(10) UNSIGNED NOT NULL,
  `roomName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `streamer_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `tip` int(10) UNSIGNED DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `chat_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `streamer_id` int(11) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tokens` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followables`
--

CREATE TABLE `followables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT 'user_id',
  `followable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `followable_id` bigint(20) UNSIGNED NOT NULL,
  `accepted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `followables`
--

INSERT INTO `followables` (`id`, `user_id`, `followable_type`, `followable_id`, `accepted_at`, `created_at`, `updated_at`) VALUES
(8, 23, 'App\\Models\\User', 49, '2024-06-28 11:57:35', '2024-06-28 11:57:35', '2024-06-28 11:57:35'),
(10, 53, 'App\\Models\\User', 49, '2024-09-05 16:34:09', '2024-09-05 16:34:09', '2024-09-05 16:34:09'),
(11, 65, 'App\\Models\\User', 49, '2024-09-06 08:26:39', '2024-09-06 08:26:39', '2024-09-06 08:26:39');

-- --------------------------------------------------------

--
-- Table structure for table `galleries`
--

CREATE TABLE `galleries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `free_for_subs` enum('no','yes') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `views` int(11) NOT NULL DEFAULT '0',
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `galleries`
--

INSERT INTO `galleries` (`id`, `user_id`, `title`, `thumbnail`, `price`, `free_for_subs`, `views`, `disk`, `category_id`, `created_at`, `updated_at`, `status`) VALUES
(84, 174, 'Testing', 'thumbnails/68043550b02b1-174.jpeg', '20.00', 'no', 0, 'public', 1, '2025-04-19 23:44:16', '2025-04-19 23:44:16', 0),
(85, 174, 'Testing', 'thumbnails/6804356e1f664-174.jpeg', '20.00', 'no', 0, 'public', 8, '2025-04-19 23:44:46', '2025-04-19 23:44:46', 0),
(86, 174, 'testing', 'thumbnails/6804363979f73-174.png', '20.00', 'no', 0, 'public', 8, '2025-04-19 23:48:09', '2025-04-19 23:48:09', 1),
(87, 174, 'test', 'thumbnails/6805607d7847b-174.jpeg', '20.00', 'no', 0, 'public', 1, '2025-04-20 16:00:45', '2025-04-20 16:00:45', 1);

-- --------------------------------------------------------

--
-- Table structure for table `gallery_sales`
--

CREATE TABLE `gallery_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `gallery_id` int(11) DEFAULT NULL,
  `streamer_id` int(11) DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `histories`
--

CREATE TABLE `histories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `mercado_accounts`
--

CREATE TABLE `mercado_accounts` (
  `id` int(11) NOT NULL,
  `user` bigint(20) DEFAULT NULL,
  `access_token` varchar(500) DEFAULT NULL,
  `expires_in` varchar(255) DEFAULT NULL,
  `scope` varchar(500) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `refresh_token` varchar(500) DEFAULT NULL,
  `public_key` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `mercado_accounts`
--

INSERT INTO `mercado_accounts` (`id`, `user`, `access_token`, `expires_in`, `scope`, `user_id`, `refresh_token`, `public_key`, `created_at`, `updated_at`) VALUES
(68, 173, 'TEST-8554740214832400-091113-e3b354883665879d83bc04f52fa13f7e-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66e1ce9b1564760001a6cf2f-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-11 17:08:43', '2024-09-11 17:08:43'),
(97, 174, 'TEST-8554740214832400-091113-4c2618f4529def1877e44026edff215f-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66e1d42bd273260001d0ee63-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-11 17:32:27', '2024-09-11 17:32:27'),
(98, 175, 'TEST-8554740214832400-091113-3596515b94615359325577bd4a155025-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66e1d6685519c20001c6855e-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-11 17:42:00', '2024-09-11 17:42:00'),
(99, 71, 'TEST-8554740214832400-091113-23d4efc30d76fb8842391b94b3ef1c75-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66e1da60ee17340001bc3dd2-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-11 17:58:57', '2024-09-11 17:58:57'),
(100, 83, 'TEST-8554740214832400-091612-590466d85263ed6430b768faa5257fec-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66e8626703a7860001fcfd36-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-16 16:52:56', '2024-09-16 16:52:56'),
(101, 84, 'TEST-8554740214832400-091613-5b149137fbb978341d33b4515b6eb2af-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66e86cd2af3af20001f45ee2-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-16 17:37:22', '2024-09-16 17:37:22'),
(102, 85, 'TEST-8554740214832400-091613-c677383ef9ac9641b506f7c8c9805654-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66e86db484f3430001e63d73-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-16 17:41:08', '2024-09-16 17:41:08'),
(103, 81, 'TEST-8554740214832400-092309-d65bd5eeca5627122d16de7e88aae14d-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66f16cef233a0400017a468b-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-23 13:28:15', '2024-09-23 13:28:15'),
(104, 89, 'TEST-8554740214832400-092601-07e5162cccdd105992ac3141dadd032c-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66f4f1bb688d910001261685-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-09-26 05:31:39', '2024-09-26 05:31:39'),
(105, 105, 'TEST-8554740214832400-100408-8c129df9174b80a3d75de46944f58534-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-66ffe3acc208650001be1b45-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-04 12:46:36', '2024-10-04 12:46:36'),
(106, 106, 'TEST-8554740214832400-100701-a064061515eab5e8bf08c6d94196af9e-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-67037200c43258000147f585-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-07 05:30:40', '2024-10-07 05:30:40'),
(107, 107, 'TEST-8554740214832400-100900-6b613d92244191e2ae691509e9f38dad-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-67060646c750e600014a0614-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-09 04:27:50', '2024-10-09 04:27:50'),
(108, 108, 'TEST-8554740214832400-100900-5135d04a14b35dcb0b449178fbd4c844-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-67060c5c301ab000019f2180-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-09 04:53:48', '2024-10-09 04:53:48'),
(109, 109, 'TEST-8554740214832400-100906-ab30f6373bbf479a229b9b1533be6a5c-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-67065a2d7130ee0001bf5257-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-09 10:25:50', '2024-10-09 10:25:50'),
(110, 110, 'TEST-8554740214832400-100906-ab2e7a8089fe3639bda44396d19e6e87-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6706607bc40e160001cb7fa9-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-09 10:52:43', '2024-10-09 10:52:43'),
(111, 110, 'TEST-8554740214832400-100906-72d405591847e0829c5e52e25f28042c-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6706607c22c3590001c2594f-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-09 10:52:44', '2024-10-09 10:52:44'),
(112, 110, 'TEST-8554740214832400-100906-10f44b1f68dc74cf20e70af7cbe1a47e-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-670660c42cd3b400017c517b-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-09 10:53:56', '2024-10-09 10:53:56'),
(113, 111, 'TEST-8554740214832400-101000-ee56a39e8eb188517508a1581cdac3a1-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6707550082c3b50001c9053a-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-10 04:16:00', '2024-10-10 04:16:00'),
(114, 112, 'TEST-8554740214832400-101000-3e5963a8aafa6ed193393cadbf211650-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-67075712ef672600013df017-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-10 04:24:50', '2024-10-10 04:24:50'),
(115, 124, 'TEST-8554740214832400-101005-64cc3e73a3e4774a0b6ed8e81b0e5964-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-67079fe678e3b700012dd916-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-10 09:35:34', '2024-10-10 09:35:34'),
(116, 127, 'TEST-8554740214832400-101006-9cc3220f3ddaaea15e6e4bc1221894f8-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6707b34940fdd000015bb114-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-10 10:58:17', '2024-10-10 10:58:17'),
(117, 134, 'TEST-8554740214832400-102406-67706f6d142a5c56e6a0e82bc5c75067-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-671a1f3b5cfba80001feccf6-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-24 10:19:39', '2024-10-24 10:19:39'),
(118, 136, 'TEST-8554740214832400-102406-02a66f1c8ead49b546333618430a0326-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-671a26e20104d500010c5f28-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-10-24 10:52:18', '2024-10-24 10:52:18'),
(134, 151, 'TEST-8554740214832400-121002-e7362c7959e7fdf4bd7935e294a2a6a6-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6757e00220a1b40001704112-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-12-10 06:30:26', '2024-12-10 06:30:26'),
(135, 152, 'TEST-8554740214832400-121003-bb628cb81c624ffba322cf3b5fd09272-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6757eca5b3199e0001a764c0-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-12-10 07:24:21', '2024-12-10 07:24:21'),
(136, 155, 'TEST-8554740214832400-121006-63f71d34e336091c4e7a80b8c16acb30-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-675813dc3b5c6500015aad84-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2024-12-10 10:11:41', '2024-12-10 10:11:41'),
(137, 163, 'TEST-8554740214832400-010311-96e2f86c49311324a5a5e1ee524f390f-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-67780921e2d3b400015a0872-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2025-01-03 15:58:25', '2025-01-03 15:58:25'),
(138, 165, 'TEST-8554740214832400-011012-816d4ba4f0e4b8fa10c8b6c274223e98-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-678145ae2f137c00016f8fd0-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2025-01-10 16:07:10', '2025-01-10 16:07:10'),
(139, 166, 'TEST-8554740214832400-011012-651af58c966e45e402033bac6c2c3585-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6781488ae0c6eb00012ed4ec-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2025-01-10 16:19:23', '2025-01-10 16:19:23'),
(140, 169, 'TEST-8554740214832400-011511-53aa2ef08d4e7ba04ba0495928ad7a2f-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-6787d77eedb2a8000171f8c0-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2025-01-15 15:42:55', '2025-01-15 15:42:55'),
(141, 171, 'TEST-8554740214832400-012113-d01876027aa20a6c1c5f71ba83f982a3-1892985996', '15552000', 'offline_access read write', 1892985996, 'TG-678fdb5e9766cc0001131be5-1892985996', 'TEST-f9841061-0290-4008-a9da-dd0c88a20838', '2025-01-21 17:37:34', '2025-01-21 17:37:34');

-- --------------------------------------------------------

--
-- Table structure for table `mercado_pix_payments`
--

CREATE TABLE `mercado_pix_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `payment_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mercado_pix_payments`
--

INSERT INTO `mercado_pix_payments` (`id`, `user_id`, `payment_id`, `status`, `amount`, `transaction_id`, `created_at`, `updated_at`) VALUES
(1, 173, '1318915620', 'pending', 1.00, NULL, '2024-07-17 07:18:35', '2024-07-17 07:18:35'),
(2, 174, '1324989295', 'pending', 1.00, NULL, '2024-07-17 07:21:13', '2024-07-17 07:21:13'),
(3, 53, '83019667668', 'pending', 1.00, NULL, '2024-07-17 07:26:13', '2024-07-17 07:26:13'),
(4, 53, '82754359035', 'pending', 1.00, NULL, '2024-07-17 10:52:21', '2024-07-17 10:52:21'),
(5, 53, '83028603688', 'rejected', 1.00, NULL, '2024-07-17 11:25:05', '2024-07-17 11:25:05'),
(6, 17, '83033041054', 'pending', 1.00, NULL, '2024-07-17 12:36:38', '2024-07-17 12:36:38'),
(7, 175, '83033842158', 'approved', 1.00, NULL, '2024-07-17 12:42:17', '2024-07-17 12:42:17'),
(8, 53, '83186452558', 'pending', 1.00, NULL, '2024-07-19 09:48:03', '2024-07-19 09:48:03');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(5, '2024_04_19_073448_create_streaming_prices_table', 1),
(6, '2024_04_19_130453_create_streaming_times_table', 2),
(7, '2024_06_03_102949_create_subscription_plans_table', 3),
(8, '2024_06_03_130040_create_subscription_plan_sells_table', 4),
(9, '2024_07_17_064050_create_mercado_pix_payments_table', 5),
(10, '2024_10_01_110350_create_reports_table', 6),
(11, '2024_10_01_152314_create_report_contents_table', 7),
(12, '2024_10_03_062542_create_media_table', 8),
(13, '2024_07_17_115510_create_payments_table', 9),
(16, '2024_10_03_082926_add_column_to_videos_table', 10),
(17, '2024_10_03_093848_add_column_to_galleries_table', 11),
(18, '2024_10_16_174441_create_permission_tables', 12),
(19, '2024_10_23_175400_add_column_whatsappnum_to_users_table', 13),
(20, '2024_11_14_104224_create_jobs_table', 14),
(21, '2024_11_14_104635_create_report_streams_table', 15),
(22, '2025_01_02_164424_add_columns_to_users_table', 16),
(23, '2025_01_19_140023_add_n_subscription_to_n_subscriptions', 17),
(24, '2025_01_19_140337_create_histories_table', 18);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 129);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('9a7fa2e6-f84d-4feb-a094-1ae72a0700db', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 19, '{\"id\":20,\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-04-15 07:12:14', '2024-04-15 06:06:41', '2024-04-15 07:12:14'),
('bdffa1db-cab0-4e86-a3f2-97fe34ed43d2', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 19, '{\"id\":21,\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-04-15 08:29:43', '2024-04-15 07:59:33', '2024-04-15 08:29:43'),
('26661502-c0dd-474c-aac0-325741a4c596', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 19, '{\"is_streamer\":\"no\",\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":1,\"user_id\":19,\"title\":\"test video\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/661cd473493fc-19.jfif\",\"video\":\"videos\\/6a7ROEPuK4KGqQ4Vk1mpTn8c2jxkrHuExjNywG8k.mp4\",\"price\":1,\"free_for_subs\":\"no\",\"views\":1,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-04-15T07:17:07.000000Z\",\"updated_at\":\"2024-04-15T08:21:04.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6a7ROEPuK4KGqQ4Vk1mpTn8c2jxkrHuExjNywG8k.mp4\",\"slug\":\"test-video\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":1}', '2024-04-15 08:29:47', '2024-04-15 08:21:34', '2024-04-15 08:29:47'),
('19a3624d-6d05-4e46-ac80-18ff819ea692', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 19, '{\"is_streamer\":\"no\",\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":2,\"user_id\":19,\"title\":\"TEST VIDEOS\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/661ce467e618e-19.jfif\",\"video\":\"videos\\/kSfeNKXU00gvvmoMZSuRMvUkxdetCBWtGJOsZyAq.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":3,\"created_at\":\"2024-04-15T08:25:11.000000Z\",\"updated_at\":\"2024-04-15T08:25:11.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/kSfeNKXU00gvvmoMZSuRMvUkxdetCBWtGJOsZyAq.mp4\",\"slug\":\"test-videos\",\"canBePlayed\":true,\"category\":{\"id\":3,\"category\":\"Sports\",\"slug\":\"sports\"}},\"price\":10}', '2024-04-15 08:29:46', '2024-04-15 08:26:27', '2024-04-15 08:29:46'),
('49044e5f-7483-47cc-b913-7a8768f1323f', 'App\\Notifications\\NewSubscriber', 'App\\Models\\User', 19, '{\"username\":\"raj\",\"profilePicture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"tierName\":\"test\",\"tokens\":2,\"renewalDate\":\"2024-05-15\",\"isStreamer\":\"no\"}', '2024-04-15 08:29:45', '2024-04-15 08:28:25', '2024-04-15 08:29:45'),
('ac1d7446-b84f-46d2-baa4-fa78ebc4c289', 'App\\Notifications\\ThanksNotification', 'App\\Models\\User', 21, '{\"username\":\"amar\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"thanks_message\":\"THANKS MY CHANNELS\"}', '2024-04-15 08:28:55', '2024-04-15 08:28:25', '2024-04-15 08:28:55'),
('9a7fa2e6-f84d-4feb-a094-1ae72a0700db', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 19, '{\"id\":20,\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-04-15 07:12:14', '2024-04-15 06:06:41', '2024-04-15 07:12:14'),
('bdffa1db-cab0-4e86-a3f2-97fe34ed43d2', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 19, '{\"id\":21,\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-04-15 08:29:43', '2024-04-15 07:59:33', '2024-04-15 08:29:43'),
('26661502-c0dd-474c-aac0-325741a4c596', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 19, '{\"is_streamer\":\"no\",\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":1,\"user_id\":19,\"title\":\"test video\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/661cd473493fc-19.jfif\",\"video\":\"videos\\/6a7ROEPuK4KGqQ4Vk1mpTn8c2jxkrHuExjNywG8k.mp4\",\"price\":1,\"free_for_subs\":\"no\",\"views\":1,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-04-15T07:17:07.000000Z\",\"updated_at\":\"2024-04-15T08:21:04.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6a7ROEPuK4KGqQ4Vk1mpTn8c2jxkrHuExjNywG8k.mp4\",\"slug\":\"test-video\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":1}', '2024-04-15 08:29:47', '2024-04-15 08:21:34', '2024-04-15 08:29:47'),
('19a3624d-6d05-4e46-ac80-18ff819ea692', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 19, '{\"is_streamer\":\"no\",\"username\":\"raj\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":2,\"user_id\":19,\"title\":\"TEST VIDEOS\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/661ce467e618e-19.jfif\",\"video\":\"videos\\/kSfeNKXU00gvvmoMZSuRMvUkxdetCBWtGJOsZyAq.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":3,\"created_at\":\"2024-04-15T08:25:11.000000Z\",\"updated_at\":\"2024-04-15T08:25:11.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/kSfeNKXU00gvvmoMZSuRMvUkxdetCBWtGJOsZyAq.mp4\",\"slug\":\"test-videos\",\"canBePlayed\":true,\"category\":{\"id\":3,\"category\":\"Sports\",\"slug\":\"sports\"}},\"price\":10}', '2024-04-15 08:29:46', '2024-04-15 08:26:27', '2024-04-15 08:29:46'),
('49044e5f-7483-47cc-b913-7a8768f1323f', 'App\\Notifications\\NewSubscriber', 'App\\Models\\User', 19, '{\"username\":\"raj\",\"profilePicture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"tierName\":\"test\",\"tokens\":2,\"renewalDate\":\"2024-05-15\",\"isStreamer\":\"no\"}', '2024-04-15 08:29:45', '2024-04-15 08:28:25', '2024-04-15 08:29:45'),
('ac1d7446-b84f-46d2-baa4-fa78ebc4c289', 'App\\Notifications\\ThanksNotification', 'App\\Models\\User', 21, '{\"username\":\"amar\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"thanks_message\":\"THANKS MY CHANNELS\"}', '2024-04-15 08:28:55', '2024-04-15 08:28:25', '2024-04-15 08:28:55'),
('6f5224dc-ffc3-49d3-87a3-d3f4cbc92280', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 49, '{\"id\":25,\"username\":\"canws\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-09-05 17:33:03', '2024-06-28 06:50:42', '2024-09-05 17:33:03'),
('83cb8f46-19dd-4d05-84bf-5e0b73605005', 'App\\Notifications\\ThanksNotification', 'App\\Models\\User', 49, '{\"username\":\"canws2414\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/25-667e66331fa4a.jpg\",\"thanks_message\":\"Thanks for subscribing!\\nYolo!\"}', '2024-09-16 15:45:36', '2024-06-28 11:32:07', '2024-09-16 15:45:36'),
('b62613a8-7fe1-4064-8e4b-9515c3c6c747', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 49, '{\"id\":23,\"username\":\"official\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-09-05 17:33:00', '2024-06-28 11:57:35', '2024-09-05 17:33:00'),
('59b74bd3-cea3-4be5-80e3-e43819f4a277', 'App\\Notifications\\ThanksNotification', 'App\\Models\\User', 23, '{\"username\":\"canws2414\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/25-667e66331fa4a.jpg\",\"thanks_message\":\"Thanks for subscribing!\\nYolo!\"}', NULL, '2024-06-28 12:04:18', '2024-06-28 12:04:18'),
('8c829c8f-947d-4d28-982d-7dba34cb4fde', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 49, '{\"id\":53,\"username\":\"bhupendra8058\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/53-667ece3152648.jpg\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-09-05 17:32:56', '2024-09-05 16:34:09', '2024-09-05 17:32:56'),
('dfddb97f-c353-4611-b0a3-3b79a5e76f1e', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 49, '{\"id\":65,\"username\":\"firani123\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', '2024-09-16 15:45:36', '2024-09-06 08:26:39', '2024-09-16 15:45:36'),
('0d5e7fe1-7b69-4545-802a-5917bd2c327e', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"makfrank\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":15,\"user_id\":49,\"title\":\"New Cast\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682af07c6aa4-49.jpg\",\"video\":\"videos\\/SEEfRqXL6wWt7fKGbmuMvD6Rt0qcO95VbKH3c9mU.mp4\",\"price\":20,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":4,\"created_at\":\"2024-06-28T14:59:32.000000Z\",\"updated_at\":\"2024-07-01T13:28:39.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/SEEfRqXL6wWt7fKGbmuMvD6Rt0qcO95VbKH3c9mU.mp4\",\"slug\":\"new-cast\",\"canBePlayed\":true,\"category\":{\"id\":4,\"category\":\"Personal\",\"slug\":\"personal\"}},\"price\":15}', '2024-09-16 15:45:36', '2024-09-11 18:17:44', '2024-09-16 15:45:36'),
('ec8b42c1-f37d-4eeb-8ebc-b8217193975e', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"makfrank\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":16,\"user_id\":49,\"title\":\"chong wang\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aeedbd955-49.jpg\",\"video\":\"videos\\/HMgoEt43E02n5OSbkVaUKLJ4hfRyXHwCD7ysnHZs.mp4\",\"price\":15,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-06-28T15:00:43.000000Z\",\"updated_at\":\"2024-07-01T13:28:13.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/HMgoEt43E02n5OSbkVaUKLJ4hfRyXHwCD7ysnHZs.mp4\",\"slug\":\"chong-wang\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":11.25}', '2024-09-16 15:45:36', '2024-09-11 18:18:56', '2024-09-16 15:45:36'),
('f0d933e6-4bb2-4339-8580-bfc67a3f2233', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"makfrank\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":56,\"user_id\":49,\"title\":\"img 1\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aa9281904-49.jpg\",\"price\":\"4.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-07-01T13:09:38.000000Z\",\"updated_at\":\"2024-07-01T13:09:38.000000Z\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3}', '2024-09-16 15:45:36', '2024-09-11 18:29:25', '2024-09-16 15:45:36'),
('0e79bce0-eafc-4dc7-8f31-2c0ed78c7345', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"bhupendra8058\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/53-667ece3152648.jpg\",\"video\":{\"id\":15,\"user_id\":49,\"title\":\"New Cast\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682af07c6aa4-49.jpg\",\"video\":\"videos\\/SEEfRqXL6wWt7fKGbmuMvD6Rt0qcO95VbKH3c9mU.mp4\",\"price\":20,\"free_for_subs\":\"no\",\"views\":2,\"disk\":\"public\",\"category_id\":4,\"created_at\":\"2024-06-28T14:59:32.000000Z\",\"updated_at\":\"2024-09-11T18:28:43.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/SEEfRqXL6wWt7fKGbmuMvD6Rt0qcO95VbKH3c9mU.mp4\",\"slug\":\"new-cast\",\"canBePlayed\":true,\"category\":{\"id\":4,\"category\":\"Personal\",\"slug\":\"personal\"}},\"price\":15}', '2024-09-16 15:45:34', '2024-09-13 06:06:58', '2024-09-16 15:45:34'),
('8c716f21-a7e1-4974-b16b-b66437b122c5', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"makfrank121\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":17,\"user_id\":49,\"title\":\"Tie win\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aec2232a2-49.jpg\",\"video\":\"videos\\/aRQkOtSvW9Uuasn1FZlD5F9ZftZh3jGiocFKFlAF.mp4\",\"price\":20,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-06-28T15:03:05.000000Z\",\"updated_at\":\"2024-07-01T13:27:30.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/aRQkOtSvW9Uuasn1FZlD5F9ZftZh3jGiocFKFlAF.mp4\",\"slug\":\"tie-win\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":15}', NULL, '2024-09-16 17:08:17', '2024-09-16 17:08:17'),
('757070ae-b36d-4882-a1d3-7a802dcf8b8d', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 49, '{\"id\":82,\"username\":\"makfrank121\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', NULL, '2024-09-16 17:08:45', '2024-09-16 17:08:45'),
('9b4e975c-1c43-46e5-867b-1352ad5911c8', 'App\\Notifications\\NewFollower', 'App\\Models\\User', 49, '{\"id\":82,\"username\":\"makfrank121\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"firstCategory\":{\"id\":null,\"category\":null,\"slug\":null},\"moneyBalance\":null,\"isBanned\":false,\"firstName\":\"\",\"tokens\":[]}', NULL, '2024-09-16 17:09:25', '2024-09-16 17:09:25'),
('582d121c-a4e4-481c-8526-9051fc41b762', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"tester\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":15,\"user_id\":49,\"title\":\"New Cast\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682af07c6aa4-49.jpg\",\"video\":\"videos\\/SEEfRqXL6wWt7fKGbmuMvD6Rt0qcO95VbKH3c9mU.mp4\",\"price\":20,\"free_for_subs\":\"no\",\"views\":2,\"disk\":\"public\",\"category_id\":4,\"created_at\":\"2024-06-28T14:59:32.000000Z\",\"updated_at\":\"2024-09-11T18:28:43.000000Z\",\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/SEEfRqXL6wWt7fKGbmuMvD6Rt0qcO95VbKH3c9mU.mp4\",\"slug\":\"new-cast\",\"canBePlayed\":true,\"category\":{\"id\":4,\"category\":\"Personal\",\"slug\":\"personal\"}},\"price\":15}', NULL, '2024-09-26 05:47:37', '2024-09-26 05:47:37'),
('43594cb8-7c58-4594-b9f0-3e00cbde7629', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"pidiw\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":56,\"user_id\":49,\"title\":\"img 1\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aa9281904-49.jpg\",\"price\":\"4.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-07-01T13:09:38.000000Z\",\"updated_at\":\"2024-07-01T13:09:38.000000Z\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3}', NULL, '2024-09-26 05:52:23', '2024-09-26 05:52:23'),
('9984dbfb-6516-45b0-a1f3-a8069b8cf63b', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 89, '{\"is_streamer\":\"no\",\"username\":\"madetu\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":68,\"user_id\":89,\"title\":\"first image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/66f4f2242b227-89.PNG\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-09-26T05:33:24.000000Z\",\"updated_at\":\"2024-10-03T10:12:30.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2024-10-04 12:33:30', '2024-10-04 12:33:30'),
('99e4f19a-388a-4ecf-9f4f-4ee6fc160dd2', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 89, '{\"is_streamer\":\"no\",\"username\":\"jipuju\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":68,\"user_id\":89,\"title\":\"first image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/66f4f2242b227-89.PNG\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-09-26T05:33:24.000000Z\",\"updated_at\":\"2024-10-03T10:12:30.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2024-10-04 12:37:18', '2024-10-04 12:37:18'),
('541ace40-515d-4910-8f1b-54cf6d8f519d', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 89, '{\"is_streamer\":\"no\",\"username\":\"alpha101\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":68,\"user_id\":89,\"title\":\"first image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/66f4f2242b227-89.PNG\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-09-26T05:33:24.000000Z\",\"updated_at\":\"2024-10-03T10:12:30.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2024-10-04 12:40:27', '2024-10-04 12:40:27'),
('c70dbf21-fc6d-4fe0-b365-26f8d686dd21', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"Testuser\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":75,\"user_id\":127,\"title\":\"charlie image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b39ecbe7e-127.png\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":8,\"created_at\":\"2024-10-10T10:59:42.000000Z\",\"updated_at\":\"2024-10-10T11:00:24.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":8,\"category\":\"Inspirational\",\"slug\":\"inspirational\"}},\"price\":3.75}', NULL, '2024-10-10 11:03:06', '2024-10-10 11:03:06'),
('9a825732-733a-4428-8094-7e19dcd24010', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"Testuser\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":42,\"user_id\":127,\"title\":\"charlie vidoe\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b37752e4a-127.png\",\"video\":\"videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-10T10:59:03.000000Z\",\"updated_at\":\"2024-10-10T10:59:18.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"slug\":\"charlie-vidoe\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-10 11:03:43', '2024-10-10 11:03:43'),
('9f03ddd7-94a3-4f7d-945d-23b6e2154b62', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"24oct\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":76,\"user_id\":49,\"title\":\"new image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6710e6bfc56d5-49.jpg\",\"price\":\"10.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-17T10:28:15.000000Z\",\"updated_at\":\"2024-10-17T10:31:04.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-24 09:05:12', '2024-10-24 09:05:12'),
('01f1f7c2-b37c-4f15-9541-f327528c739a', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"24oct\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":42,\"user_id\":127,\"title\":\"charlie vidoe\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b37752e4a-127.png\",\"video\":\"videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-10T10:59:03.000000Z\",\"updated_at\":\"2024-10-10T10:59:18.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"slug\":\"charlie-vidoe\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-24 09:06:01', '2024-10-24 09:06:01'),
('217d7d34-df2d-4ed8-85b2-10a5ae808594', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 134, '{\"is_streamer\":\"no\",\"username\":\"hasogaxyzu\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":44,\"user_id\":134,\"title\":\"jollyllb video first\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a201b8f1f9-134.png\",\"video\":\"videos\\/lhSk7zEatAxfnA4Lfa9paKTe3CsRF9rqxlcHSPND.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:23:23.000000Z\",\"updated_at\":\"2024-10-24T10:23:43.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/lhSk7zEatAxfnA4Lfa9paKTe3CsRF9rqxlcHSPND.mp4\",\"slug\":\"jollyllb-video-first\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-24 10:53:13', '2024-10-24 10:53:13'),
('9a7959a7-45d6-4b23-862c-0a404c2837c3', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 134, '{\"is_streamer\":\"no\",\"username\":\"hasogaxyzu\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":77,\"user_id\":134,\"title\":\"jollyllb image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a204eaf0d3-134.png\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:24:14.000000Z\",\"updated_at\":\"2024-10-24T10:24:36.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2024-10-24 10:53:55', '2024-10-24 10:53:55'),
('31f5d68b-4d7d-4f02-a2bc-ee47149c4120', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 134, '{\"is_streamer\":\"no\",\"username\":\"User234\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":44,\"user_id\":134,\"title\":\"jollyllb video first\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a201b8f1f9-134.png\",\"video\":\"videos\\/lhSk7zEatAxfnA4Lfa9paKTe3CsRF9rqxlcHSPND.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:23:23.000000Z\",\"updated_at\":\"2024-10-24T10:23:43.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/lhSk7zEatAxfnA4Lfa9paKTe3CsRF9rqxlcHSPND.mp4\",\"slug\":\"jollyllb-video-first\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-24 10:54:57', '2024-10-24 10:54:57'),
('7c6c6a50-1893-4835-a409-ddb00bd6e2c1', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 136, '{\"is_streamer\":\"no\",\"username\":\"User234\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":45,\"user_id\":136,\"title\":\"jollyllb2 video\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a2720a5aef-136.png\",\"video\":\"videos\\/Ty2fyyQnL8HanuL79lGIv2ZSGoaurLf7oPOL6guQ.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:53:20.000000Z\",\"updated_at\":\"2024-10-24T10:53:39.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/Ty2fyyQnL8HanuL79lGIv2ZSGoaurLf7oPOL6guQ.mp4\",\"slug\":\"jollyllb2-video\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-24 10:56:09', '2024-10-24 10:56:09'),
('cb590124-d8d9-486f-86e0-c91e084d3bff', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 134, '{\"is_streamer\":\"no\",\"username\":\"User234\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":77,\"user_id\":134,\"title\":\"jollyllb image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a204eaf0d3-134.png\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:24:14.000000Z\",\"updated_at\":\"2024-10-24T10:24:36.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2024-10-24 10:57:04', '2024-10-24 10:57:04'),
('b5a4516e-88b1-42b1-8404-0bebf4daba73', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 136, '{\"is_streamer\":\"no\",\"username\":\"alpha04\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":78,\"user_id\":136,\"title\":\"jollyllb2 image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a275e58c7a-136.png\",\"price\":\"4.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:54:22.000000Z\",\"updated_at\":\"2024-10-24T10:54:36.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3}', NULL, '2024-10-24 11:00:11', '2024-10-24 11:00:11'),
('b0dd66f7-771b-42cc-a708-85189fb09c83', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 136, '{\"is_streamer\":\"no\",\"username\":\"nafehik\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":45,\"user_id\":136,\"title\":\"jollyllb2 video\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a2720a5aef-136.png\",\"video\":\"videos\\/Ty2fyyQnL8HanuL79lGIv2ZSGoaurLf7oPOL6guQ.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:53:20.000000Z\",\"updated_at\":\"2024-10-24T10:53:39.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/Ty2fyyQnL8HanuL79lGIv2ZSGoaurLf7oPOL6guQ.mp4\",\"slug\":\"jollyllb2-video\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-24 11:00:47', '2024-10-24 11:00:47'),
('99f0d313-ca20-4569-af0c-996495baf9d5', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 136, '{\"is_streamer\":\"no\",\"username\":\"nafehik\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":78,\"user_id\":136,\"title\":\"jollyllb2 image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a275e58c7a-136.png\",\"price\":\"4.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:54:22.000000Z\",\"updated_at\":\"2024-10-24T10:54:36.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3}', NULL, '2024-10-24 11:01:41', '2024-10-24 11:01:41'),
('7a7c0f8d-3f0b-4ae7-9a50-32a6b5dbd866', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 136, '{\"is_streamer\":\"no\",\"username\":\"alpha004\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":78,\"user_id\":136,\"title\":\"jollyllb2 image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a275e58c7a-136.png\",\"price\":\"4.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:54:22.000000Z\",\"updated_at\":\"2024-10-24T10:54:36.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3}', NULL, '2024-10-24 11:05:17', '2024-10-24 11:05:17'),
('4a5cbfd3-68a9-4df3-a579-df66cbc424a6', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 136, '{\"is_streamer\":\"no\",\"username\":\"alpha004\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":45,\"user_id\":136,\"title\":\"jollyllb2 video\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a2720a5aef-136.png\",\"video\":\"videos\\/Ty2fyyQnL8HanuL79lGIv2ZSGoaurLf7oPOL6guQ.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:53:20.000000Z\",\"updated_at\":\"2024-10-24T10:53:39.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/Ty2fyyQnL8HanuL79lGIv2ZSGoaurLf7oPOL6guQ.mp4\",\"slug\":\"jollyllb2-video\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-10-24 11:06:26', '2024-10-24 11:06:26'),
('ec0db14e-e905-4feb-83ce-2035d232239d', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"24oct\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":75,\"user_id\":127,\"title\":\"charlie image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b39ecbe7e-127.png\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":8,\"created_at\":\"2024-10-10T10:59:42.000000Z\",\"updated_at\":\"2024-10-10T11:00:24.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":8,\"category\":\"Inspirational\",\"slug\":\"inspirational\"}},\"price\":3.75}', NULL, '2024-10-24 15:53:09', '2024-10-24 15:53:09'),
('5a9b5941-7715-4847-afc6-6186cea7f6d1', 'App\\Notifications\\NewSubscriber', 'App\\Models\\User', 49, '{\"username\":\"24oct\",\"profilePicture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"tierName\":\"Tier 1\",\"tokens\":10,\"renewalDate\":\"2024-11-24\",\"isStreamer\":\"no\"}', NULL, '2024-10-24 15:54:13', '2024-10-24 15:54:13'),
('c471fe6a-a9fb-4083-8b62-9889768c5013', 'App\\Notifications\\ThanksNotification', 'App\\Models\\User', 133, '{\"username\":\"prem80589\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/49-667e9c7e99b63.jpeg\",\"thanks_message\":\"Thanks for subscribing\"}', NULL, '2024-10-24 15:54:13', '2024-10-24 15:54:13'),
('f5f9edff-d38d-4e67-b66e-2da4dac13c99', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"bhupendra8058\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/53-667ece3152648.jpg\",\"video\":{\"id\":42,\"user_id\":127,\"title\":\"charlie vidoe\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b37752e4a-127.png\",\"video\":\"videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-10T10:59:03.000000Z\",\"updated_at\":\"2024-10-10T10:59:18.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"slug\":\"charlie-vidoe\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-11-15 09:08:35', '2024-11-15 09:08:35'),
('cb925b01-1e02-4661-9891-7510c3e178e3', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"john\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":75,\"user_id\":127,\"title\":\"charlie image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b39ecbe7e-127.png\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":8,\"created_at\":\"2024-10-10T10:59:42.000000Z\",\"updated_at\":\"2024-10-10T11:00:24.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":8,\"category\":\"Inspirational\",\"slug\":\"inspirational\"}},\"price\":3.75}', NULL, '2024-12-06 04:52:54', '2024-12-06 04:52:54'),
('5a2efc55-658a-4eeb-a239-6161bd32335c', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"john\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":42,\"user_id\":127,\"title\":\"charlie vidoe\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b37752e4a-127.png\",\"video\":\"videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":1,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-10T10:59:03.000000Z\",\"updated_at\":\"2024-12-05T10:53:11.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"slug\":\"charlie-vidoe\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-12-06 04:54:24', '2024-12-06 04:54:24'),
('9ea87a53-614b-463d-a900-fa9e6ac7aa67', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"sasalyxumy\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":42,\"user_id\":127,\"title\":\"charlie vidoe\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b37752e4a-127.png\",\"video\":\"videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":1,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-10T10:59:03.000000Z\",\"updated_at\":\"2024-12-05T10:53:11.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"slug\":\"charlie-vidoe\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-12-06 04:57:54', '2024-12-06 04:57:54'),
('489a7f08-f404-43c4-9bdc-123e47ecb570', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 89, '{\"is_streamer\":\"no\",\"username\":\"sasalyxumy\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":68,\"user_id\":89,\"title\":\"first image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/66f4f2242b227-89.PNG\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-09-26T05:33:24.000000Z\",\"updated_at\":\"2024-10-03T10:12:30.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2024-12-06 04:59:18', '2024-12-06 04:59:18'),
('ced733f1-5900-498e-a4e2-b6d4baebcdf3', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 134, '{\"is_streamer\":\"no\",\"username\":\"john\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":77,\"user_id\":134,\"title\":\"jollyllb image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/671a204eaf0d3-134.png\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-24T10:24:14.000000Z\",\"updated_at\":\"2024-10-24T10:24:36.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2024-12-06 05:06:55', '2024-12-06 05:06:55'),
('d646c3d7-5645-4a38-86ae-aa744d7244ea', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"johndoe0108\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":56,\"user_id\":49,\"title\":\"img 1\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aa9281904-49.jpg\",\"price\":\"4.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-07-01T13:09:38.000000Z\",\"updated_at\":\"2024-10-10T07:18:16.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3}', NULL, '2024-12-06 05:10:08', '2024-12-06 05:10:08'),
('8c07e2dd-8925-4625-a887-4d44169a6e5f', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"HarryHall\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":56,\"user_id\":49,\"title\":\"img 1\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aa9281904-49.jpg\",\"price\":\"4.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-07-01T13:09:38.000000Z\",\"updated_at\":\"2024-10-10T07:18:16.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3}', NULL, '2024-12-06 05:14:06', '2024-12-06 05:14:06'),
('da04e1eb-3f3a-4b2b-8cb6-35f9b93ea58e', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"HarryHall\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":42,\"user_id\":127,\"title\":\"charlie vidoe\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6707b37752e4a-127.png\",\"video\":\"videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":1,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-10T10:59:03.000000Z\",\"updated_at\":\"2024-12-05T10:53:11.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/6V5hZ4WnJF7meTFcRQvDc9pusJQYQgPF1qSTATCH.mp4\",\"slug\":\"charlie-vidoe\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-12-06 05:15:37', '2024-12-06 05:15:37'),
('45140baf-2591-4b97-9dd5-7a1fb6d46564', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"navjot10985\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":16,\"user_id\":49,\"title\":\"chong wang\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aeedbd955-49.jpg\",\"video\":\"videos\\/HMgoEt43E02n5OSbkVaUKLJ4hfRyXHwCD7ysnHZs.mp4\",\"price\":15,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-06-28T15:00:43.000000Z\",\"updated_at\":\"2024-12-06T13:47:41.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/HMgoEt43E02n5OSbkVaUKLJ4hfRyXHwCD7ysnHZs.mp4\",\"slug\":\"chong-wang\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":11.25}', NULL, '2024-12-10 10:22:48', '2024-12-10 10:22:48'),
('58832adf-3223-4c95-9645-779d553a2d3f', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"navjot10985\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":75,\"user_id\":127,\"title\":\"charlie image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/67530a16b4ce3-127.jpg\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":8,\"created_at\":\"2024-10-10T10:59:42.000000Z\",\"updated_at\":\"2024-12-06T14:28:38.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":8,\"category\":\"Inspirational\",\"slug\":\"inspirational\"}},\"price\":3.75}', NULL, '2024-12-10 10:24:07', '2024-12-10 10:24:07'),
('03a0dbb6-968a-4965-9a2c-bf1370235436', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"Jack\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":42,\"user_id\":127,\"title\":\"charlie vidoe\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/675303b5e3e3b-127.jpg\",\"video\":\"videos\\/eviUF4hEB2jyduuMvHUpKE1UVMRuN7N7UsuXojQI.mp4\",\"price\":10,\"free_for_subs\":\"no\",\"views\":1,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-10T10:59:03.000000Z\",\"updated_at\":\"2024-12-06T14:01:25.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/eviUF4hEB2jyduuMvHUpKE1UVMRuN7N7UsuXojQI.mp4\",\"slug\":\"charlie-vidoe\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-12-10 10:25:45', '2024-12-10 10:25:45'),
('c67a3cda-862b-43d1-b98c-62607f05c049', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"Jack\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":75,\"user_id\":127,\"title\":\"charlie image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/67530a16b4ce3-127.jpg\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":8,\"created_at\":\"2024-10-10T10:59:42.000000Z\",\"updated_at\":\"2024-12-06T14:28:38.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":8,\"category\":\"Inspirational\",\"slug\":\"inspirational\"}},\"price\":3.75}', NULL, '2024-12-10 10:26:58', '2024-12-10 10:26:58'),
('f39b4395-e244-4a2d-8a12-7fded2527657', 'App\\Notifications\\NewVideoSale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"johnpaul\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"video\":{\"id\":16,\"user_id\":49,\"title\":\"chong wang\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6682aeedbd955-49.jpg\",\"video\":\"videos\\/HMgoEt43E02n5OSbkVaUKLJ4hfRyXHwCD7ysnHZs.mp4\",\"price\":15,\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-06-28T15:00:43.000000Z\",\"updated_at\":\"2024-12-06T13:47:41.000000Z\",\"status\":1,\"videoUrl\":\"https:\\/\\/love248.com\\/videos\\/HMgoEt43E02n5OSbkVaUKLJ4hfRyXHwCD7ysnHZs.mp4\",\"slug\":\"chong-wang\",\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":11.25}', NULL, '2024-12-10 10:28:35', '2024-12-10 10:28:35'),
('1a2a03cd-7d43-4c5f-96bb-5289b620fc70', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 49, '{\"is_streamer\":\"no\",\"username\":\"navjot10985\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":76,\"user_id\":49,\"title\":\"new image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/6710e6bfc56d5-49.jpg\",\"price\":\"10.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2024-10-17T10:28:15.000000Z\",\"updated_at\":\"2024-10-17T10:31:04.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":7.5}', NULL, '2024-12-10 10:31:16', '2024-12-10 10:31:16'),
('16841444-cd1a-4ff2-8d81-b836d7589f35', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"johnpaul\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":75,\"user_id\":127,\"title\":\"charlie image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/67530a16b4ce3-127.jpg\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":8,\"created_at\":\"2024-10-10T10:59:42.000000Z\",\"updated_at\":\"2024-12-06T14:28:38.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":8,\"category\":\"Inspirational\",\"slug\":\"inspirational\"}},\"price\":3.75}', NULL, '2024-12-10 10:33:51', '2024-12-10 10:33:51'),
('c89b9732-33b6-4fd6-9a44-9beaffebec6f', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 127, '{\"is_streamer\":\"no\",\"username\":\"charlieputh\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/default-profile-pic.png\",\"gallery\":{\"id\":75,\"user_id\":127,\"title\":\"charlie image\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/67530a16b4ce3-127.jpg\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":8,\"created_at\":\"2024-10-10T10:59:42.000000Z\",\"updated_at\":\"2024-12-06T14:28:38.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":8,\"category\":\"Inspirational\",\"slug\":\"inspirational\"}},\"price\":3.75}', NULL, '2024-12-10 10:38:25', '2024-12-10 10:38:25'),
('96360bae-180f-4f58-945f-fb620666cc83', 'App\\Notifications\\NewGallerySale', 'App\\Models\\User', 163, '{\"is_streamer\":\"no\",\"username\":\"bhupendra8058\",\"profile_picture\":\"https:\\/\\/love248.com\\/profilePics\\/53-667ece3152648.jpg\",\"gallery\":{\"id\":81,\"user_id\":163,\"title\":\"ALL THE BEST POSITIONS\",\"thumbnail\":\"https:\\/\\/love248.com\\/thumbnails\\/67780ec0e4c6b-163.jpg\",\"price\":\"5.00\",\"free_for_subs\":\"no\",\"views\":0,\"disk\":\"public\",\"category_id\":1,\"created_at\":\"2025-01-03T16:22:24.000000Z\",\"updated_at\":\"2025-01-03T16:22:35.000000Z\",\"status\":1,\"canBePlayed\":true,\"category\":{\"id\":1,\"category\":\"Fun\",\"slug\":\"fun\"}},\"price\":3.75}', NULL, '2025-01-03 16:25:22', '2025-01-03 16:25:22');

-- --------------------------------------------------------

--
-- Table structure for table `n_subscriptions`
--

CREATE TABLE `n_subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_plan_sells_id` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expired_at` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `subscription_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscription_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `options_table`
--

CREATE TABLE `options_table` (
  `id` int(10) UNSIGNED NOT NULL,
  `option_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `option_value` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `options_table`
--

INSERT INTO `options_table` (`id`, `option_name`, `option_value`) VALUES
(13, 'payment-settings.currency_code', 'USD'),
(14, 'payment-settings.currency_symbol', '$'),
(16, 'STRIPE_PUBLIC_KEY', 'pk_test_51P3GvQJN9OqqM6ftdVHrFrYb2oDB3W70Qo1AUdPpTLqWreSClCOKbKYNtNobRIsNpjdVqJkKT2gERNeqywfXXnWG00j6rgqeat'),
(17, 'STRIPE_SECRET_KEY', 'your_stripe_secret_key_here'),
(18, 'stripeEnable', 'No'),
(19, 'paypalEnable', 'No'),
(21, 'paypal_email', 'paypal@email.com'),
(22, 'admin_email', 'you@example.org'),
(37, 'seo_title', 'Premium Work'),
(38, 'seo_desc', 'Live streaming & clips for sale at your fingertips'),
(39, 'seo_keys', 'live streaming, clips for sale'),
(40, 'site_title', 'Twitcher Title'),
(85, 'default_storage', 'public'),
(101, 'site_entry_popup', 'No'),
(102, 'entry_popup_title', 'Entry popup title'),
(103, 'entry_popup_message', 'Entry popup message'),
(104, 'entry_popup_confirm_text', 'Continue'),
(105, 'entry_popup_cancel_text', 'Cancel'),
(106, 'entry_popup_awayurl', 'https://google.com'),
(109, 'card_gateway', 'Stripe'),
(118, 'enableMediaDownload', 'No'),
(199, 'site_logo', '/images/11385374136802e97ec40f6.png'),
(203, 'token_value', '0.75'),
(204, 'min_withdraw', '500'),
(207, 'bankEnable', 'No'),
(208, 'bankInstructions', NULL),
(209, 'ccbillEnable', 'No'),
(210, 'CCBILL_ACC_NO', NULL),
(211, 'CCBILL_SUBACC_NO', NULL),
(212, 'CCBILL_SALT_KEY', NULL),
(213, 'CCBILL_FLEX_FORM_ID', NULL),
(214, 'streamersIdentityRequired', 'Yes'),
(215, 'favicon', '/images/1182139141666affc2b452b.png'),
(216, 'facebook', NULL),
(217, 'google', NULL),
(218, 'tiktok', NULL),
(219, 'mercado_pago', 'Yes'),
(221, 'MERCADO_PUBLIC_KEY', 'TEST-9744f268-12c8-4584-be5c-23c7b7a33c9b'),
(223, 'MERCADO_SECRET_KEY', 'TEST-8554740214832400-082312-0ae735b3341a8db79a99b8c880510b0a-1957807413'),
(225, 'pagar_me', 'No'),
(226, 'PAGAR_PUBLIC_KEY', 'pk_GrgAmDGU5aSkPWZa'),
(227, 'PAGAR_SECRET_KEY', 'your_pagar_secret_key_here'),
(228, 'streamers_commission_private_room', '50'),
(229, 'streamers_commission_videos', '70'),
(230, 'admin_commission_private_room', '50'),
(231, 'admin_commission_videos', '30'),
(232, 'streamer_commission_photos', '20'),
(234, 'admin_commission_photos', '30'),
(235, 'streamers_commission_photos', '70'),
(236, 'lang', 'pt'),
(237, 'site_logo_footer', ''),
(238, 'RTMP_URL', 'rtmp://love248.com/live'),
(239, 'PUSHER_APP_ID', '1785307'),
(240, 'PUSHER_APP_KEY', 'e6e2e79c72e871c9fc8e'),
(241, 'PUSHER_APP_SECRET', '6f4cba204d5fc89df906'),
(242, 'PUSHER_APP_CLUSTER', 'sa1'),
(243, 'admin_client_id', '8554740214832400'),
(244, 'admin_client_secret', '991uWq1NlTSbvUJ13KYss7S0Q8EtIcA6');

-- --------------------------------------------------------

--
-- Table structure for table `pagar_pix_payments`
--

CREATE TABLE `pagar_pix_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `payment_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pagar_pix_payments`
--

INSERT INTO `pagar_pix_payments` (`id`, `user_id`, `payment_id`, `status`, `amount`, `transaction_id`, `created_at`, `updated_at`) VALUES
(1, 53, 'or_8zJBLmVHbH4pPN0Y', 'pending', 1.00, NULL, '2024-07-17 04:25:18', '2024-07-17 04:25:18'),
(2, 53, 'or_A923OY5I8IjOovPk', 'pending', 1.00, NULL, '2024-07-17 04:26:56', '2024-07-17 04:26:56'),
(3, 53, 'or_1rnzpLl9TOSrpkVe', 'pending', 1.00, NULL, '2024-07-17 04:27:28', '2024-07-17 04:27:28'),
(4, 53, 'or_Xx2JA2jFzEt7AQqz', 'pending', 1.00, NULL, '2024-07-17 04:46:20', '2024-07-17 04:46:20'),
(5, 53, 'or_m5RPBzAUXsdyLGyK', 'pending', 1.00, NULL, '2024-07-17 10:47:43', '2024-07-17 10:47:43'),
(6, 17, 'or_bVejGxNIyIk63BD7', 'pending', 1.00, NULL, '2024-07-17 12:43:26', '2024-07-17 12:43:26'),
(7, 17, 'or_Mv42r47S75fx8BPd', 'pending', 850.00, NULL, '2024-07-18 16:52:27', '2024-07-18 16:52:27'),
(8, 17, 'or_jDR1lmAcx2Fgl0EN', 'pending', 850.00, NULL, '2024-07-18 16:52:41', '2024-07-18 16:52:41'),
(9, 17, 'or_z5yYomXHdlC5L048', 'pending', 850.00, NULL, '2024-07-18 16:56:38', '2024-07-18 16:56:38');

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `page_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_content` mediumtext COLLATE utf8mb4_unicode_ci,
  `page_type` enum('page','post') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `page_title`, `page_slug`, `page_content`, `page_type`, `created_at`, `updated_at`) VALUES
(7, 'Terms of Service', 'terms-of-service', '<p><strong><span style=\"font-size: 18pt;\">Overview</span></strong></p>\r\n<p>This website is operated by Your site name here. Throughout the site, the terms &ldquo;we&rdquo;, &ldquo;us&rdquo; and &ldquo;our&rdquo; refer to Your site name here. Your site name here offers this website, including all information, tools and services available from this site to you, the user, conditioned upon your acceptance of all terms, conditions, policies and notices stated here.</p>\r\n<p>By visiting our site and/ or purchasing something from us, you engage in our &ldquo;Service&rdquo; and agree to be bound by the following terms and conditions (&ldquo;Terms of Service&rdquo;, &ldquo;Terms&rdquo;), including those additional terms and conditions and policies referenced herein and/or available by hyperlink. These Terms of Service apply&nbsp; to all users of the site, including without limitation users who are browsers, vendors, customers, merchants, and/ or contributors of content.</p>\r\n<p>Please read these Terms of Service carefully before accessing or using our website. By accessing or using any part of the site, you agree to be bound by these Terms of Service. If you do not agree to all the terms and conditions of this agreement, then you may not access the website or use any services. If these Terms of Service are considered an offer, acceptance is expressly limited to these Terms of Service.</p>\r\n<p>Any new features or tools which are added to the current store shall also be subject to the Terms of Service. You can review the most current version of the Terms of Service at any time on this page. We reserve the right to update, change or replace any part of these Terms of Service by posting updates and/or changes to our website. It is your responsibility to check this page periodically for changes. Your continued use of or access to the website following the posting of any changes constitutes acceptance of those changes.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Platform Terms</span></strong></p>\r\n<p>By agreeing to these Terms of Service, you represent that you are at least the age of majority in your state or province of residence, or that you are the age of majority in your state or province of residence and you have given us your consent to allow any of your minor dependents to use this site.</p>\r\n<p>You may not use our products for any illegal or unauthorized purpose nor may you, in the use of the Service, violate any laws in your jurisdiction (including but not limited to copyright laws).</p>\r\n<p>You must not transmit any worms or viruses or any code of a destructive nature.</p>\r\n<p>A breach or violation of any of the Terms will result in an immediate termination of your Services.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">General Conditions</span></strong></p>\r\n<p>We reserve the right to refuse service to anyone for any reason at any time.</p>\r\n<p>You understand that your content (not including credit card information), may be transferred unencrypted and involve (a) transmissions over various networks; and (b) changes to conform and adapt to technical requirements of connecting networks or devices. Credit card information is always encrypted during transfer over networks.</p>\r\n<p>You agree not to reproduce, duplicate, copy, sell, resell or exploit any portion of the Service, use of the Service, or access to the Service or any contact on the website through which the service is provided, without express written permission by us.</p>\r\n<p>The headings used in this agreement are included for convenience only and will not limit or otherwise affect these Terms.</p>\r\n<p>Accuracy, Completeness And Timeliness Of Information</p>\r\n<p>We are not responsible if information made available on this site is not accurate, complete or current. The material on this site is provided for general information only and should not be relied upon or used as the sole basis for making decisions without consulting primary, more accurate, more complete or more timely sources of information. Any reliance on the material on this site is at your own risk.</p>\r\n<p>This site may contain certain historical information. Historical information, necessarily, is not current and is provided for your reference only. We reserve the right to modify the contents of this site at any time, but we have no obligation to update any information on our site. You agree that it is your responsibility to monitor changes to our site.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Modifications To The Service And Prices</span></strong></p>\r\n<p>Prices for our products are subject to change without notice.</p>\r\n<p>We reserve the right at any time to modify or discontinue the Service (or any part or content thereof) without notice at any time.</p>\r\n<p>We shall not be liable to you or to any third-party for any modification, price change, suspension or discontinuance of the Service.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Products And Services</span></strong></p>\r\n<p>Certain products or services may be available exclusively online through the website. These products or services may have limited quantities and are subject to return or exchange only according to our Return Policy.</p>\r\n<p>We have made every effort to display as accurately as possible the colors and images of our products that appear at the store. We cannot guarantee that your computer monitor\'s display of any color will be accurate.</p>\r\n<p>We reserve the right, but are not obligated, to limit the sales of our products or Services to any person, geographic region or jurisdiction. We may exercise this right on a case-by-case basis. We reserve the right to limit the quantities of any products or services that we offer. All descriptions of products or product pricing are subject to change at anytime without notice, at the sole discretion of our users. Users reserve the right to discontinue any product at any time. Any offer for any product or service made on this site is void where prohibited.</p>\r\n<p>We do not warrant that the quality of any products, services, information, or other material purchased or obtained by you will meet your expectations, or that any errors in the Service will be corrected.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Accuracy Of Billing And Account Information</span></strong></p>\r\n<p>We reserve the right to refuse any order you place with us. We may, in our sole discretion, limit or cancel quantities purchased per person, per household or per order. These restrictions may include orders placed by or under the same customer account, the same credit card, and/or orders that use the same billing and/or shipping address. In the event that we make a change to or cancel an order, we may attempt to notify you by contacting the e-mail and/or billing address/phone number provided at the time the order was made. We reserve the right to limit or prohibit orders that, in our sole judgment, appear to be placed by dealers, resellers or distributors.</p>\r\n<p>You agree to provide current, complete and accurate purchase and account information for all purchases made at our store.</p>\r\n<p>For more detail, please review our Returns Policy.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Optional Tools</span></strong></p>\r\n<p>We may provide you with access to third-party tools over which we neither monitor nor have any control nor input.</p>\r\n<p>You acknowledge and agree that we provide access to such tools &rdquo;as is&rdquo; and &ldquo;as available&rdquo; without any warranties, representations or conditions of any kind and without any endorsement. We shall have no liability whatsoever arising from or relating to your use of optional third-party tools.</p>\r\n<p>Any use by you of optional tools offered through the site is entirely at your own risk and discretion and you should ensure that you are familiar with and approve of the terms on which tools are provided by the relevant third-party provider(s).</p>\r\n<p>We may also, in the future, offer new services and/or features through the website (including, the release of new tools and resources). Such new features and/or services shall also be subject to these Terms of Service.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Third-party Links</span></strong></p>\r\n<p>Certain content, products and services available via our Service may include materials from third-parties.</p>\r\n<p>Third-party links on this site may direct you to third-party websites that are not affiliated with us. We are not responsible for examining or evaluating the content or accuracy and we do not warrant and will not have any liability or responsibility for any third-party materials or websites, or for any other materials, products, or services of third-parties.</p>\r\n<p>We are not liable for any harm or damages related to the purchase or use of goods, services, resources, content, or any other transactions made in connection with any third-party websites. Please review carefully the third-party\'s policies and practices and make sure you understand them before you engage in any transaction. Complaints, claims, concerns, or questions regarding third-party products should be directed to the third-party.</p>\r\n<p><span style=\"font-size: 18pt;\"><strong>User Comments, Feedback And Other Submissions</strong></span></p>\r\n<p>If, at our request, you send certain specific submissions (for example contest entries) or without a request from us you send creative ideas, suggestions, proposals, plans, or other materials, whether online, by email, by postal mail, or otherwise (collectively, \'comments\'), you agree that we may, at any time, without restriction, edit, copy, publish, distribute, translate and otherwise use in any medium any comments that you forward to us. We are and shall be under no obligation (1) to maintain any comments in confidence; (2) to pay compensation for any comments; or (3) to respond to any comments.</p>\r\n<p>We may, but have no obligation to, monitor, edit or remove content that we determine in our sole discretion are unlawful, offensive, threatening, libelous, defamatory, pornographic, obscene or otherwise objectionable or violates any party&rsquo;s intellectual property or these Terms of Services</p>\r\n<p>You agree that your comments will not violate any right of any third-party, including copyright, trademark, privacy, personality or other personal or proprietary right. You further agree that your comments will not contain libelous or otherwise unlawful, abusive or obscene material, or contain any computer virus or other malware that could in any way affect the operation of the Service or any related website. You may not use a false e-mail address, pretend to be someone other than yourself, or otherwise mislead us or third-parties as to the origin of any comments. You are solely responsible for any comments you make and their accuracy. We take no responsibility and assume no liability for any comments posted by you or any third-party.</p>\r\n<p>Your submission of personal information through the store is governed by our Privacy Policy.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Errors, Inaccuracies And Omissions</span></strong></p>\r\n<p>Occasionally there may be information on our site or in the Service that contains typographical errors, inaccuracies or omissions that may relate to product descriptions, pricing, promotions, offers, product shipping charges, transit times and availability. We reserve the right to correct any errors, inaccuracies or omissions, and to change or update information or cancel orders if any information in the Service or on any related website is inaccurate at any time without prior notice (including after you have submitted your order).</p>\r\n<p>We undertake no obligation to update, amend or clarify information in the Service or on any related website, including without limitation, pricing information, except as required by law. No specified update or refresh date applied in the Service or on any related website, should be taken to indicate that all information in the Service or on any related website has been modified or updated.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Prohibited Uses</span></strong></p>\r\n<p>In addition to other prohibitions as set forth in the Terms of Service, you are prohibited from using the site or its content: (a) for any unlawful purpose; (b) to solicit others to perform or participate in any unlawful acts; (c) to violate any international, federal, provincial or state regulations, rules, laws, or local ordinances; (d) to infringe upon or violate our intellectual property rights or the intellectual property rights of others; (e) to harass, abuse, insult, harm, defame, slander, disparage, intimidate, or discriminate based on gender, sexual orientation, religion, ethnicity, race, age, national origin, or disability; (f) to submit false or misleading information; (g) to upload or transmit viruses or any other type of malicious code that will or may be used in any way that will affect the functionality or operation of the Service or of any related website, other websites, or the Internet; (h) to collect or track the personal information of others; (i) to spam, phish, pharm, pretext, spider, crawl, or scrape; (j) for any obscene or immoral purpose; or (k) to interfere with or circumvent the security features of the Service or any related website, other websites, or the Internet. We reserve the right to terminate your use of the Service or any related website for violating any of the prohibited uses.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Disclaimer Of Warranties; Limitation Of Liability</span></strong></p>\r\n<p>We do not guarantee, represent or warrant that your use of our service will be uninterrupted, timely, secure or error-free.</p>\r\n<p>We do not warrant that the results that may be obtained from the use of the service will be accurate or reliable.</p>\r\n<p>You agree that from time to time we may remove the service for indefinite periods of time or cancel the service at any time, without notice to you.</p>\r\n<p>You expressly agree that your use of, or inability to use, the service is at your sole risk. The service and all products and services delivered to you through the service are (except as expressly stated by us) provided \'as is\' and \'as available\' for your use, without any representation, warranties or conditions of any kind, either express or implied, including all implied warranties or conditions of merchantability, merchantable quality, fitness for a particular purpose, durability, title, and non-infringement.</p>\r\n<p>In no case shall Your site name here, our directors, officers, employees, affiliates, agents, contractors, interns, suppliers, service providers or licensors be liable for any injury, loss, claim, or any direct, indirect, incidental, punitive, special, or consequential damages of any kind, including, without limitation lost profits, lost revenue, lost savings, loss of data, replacement costs, or any similar damages, whether based in contract, tort (including negligence), strict liability or otherwise, arising from your use of any of the service or any products procured using the service, or for any other claim related in any way to your use of the service or any product, including, but not limited to, any errors or omissions in any content, or any loss or damage of any kind incurred as a result of the use of the service or any content (or product) posted, transmitted, or otherwise made available via the service, even if advised of their possibility. Because some states or jurisdictions do not allow the exclusion or the limitation of liability for consequential or incidental damages, in such states or jurisdictions, our liability shall be limited to the maximum extent permitted by law.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Indemnification</span></strong></p>\r\n<p>You agree to indemnify, defend and hold harmless Your site name here and our parent, subsidiaries, affiliates, partners, officers, directors, agents, contractors, licensors, service providers, subcontractors, suppliers, interns and employees, harmless from any claim or demand, including reasonable attorneys&rsquo; fees, made by any third-party due to or arising out of your breach of these Terms of Service or the documents they incorporate by reference, or your violation of any law or the rights of a third-party.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Severability</span></strong></p>\r\n<p>In the event that any provision of these Terms of Service is determined to be unlawful, void or unenforceable, such provision shall nonetheless be enforceable to the fullest extent permitted by applicable law, and the unenforceable portion shall be deemed to be severed from these Terms of Service, such determination shall not affect the validity and enforceability of any other remaining provisions.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Termination</span></strong></p>\r\n<p>The obligations and liabilities of the parties incurred prior to the termination date shall survive the termination of this agreement for all purposes.</p>\r\n<p>These Terms of Service are effective unless and until terminated by either you or us. You may terminate these Terms of Service at any time by notifying us that you no longer wish to use our Services, or when you cease using our site.</p>\r\n<p>If in our sole judgment you fail, or we suspect that you have failed, to comply with any term or provision of these Terms of Service, we also may terminate this agreement at any time without notice and you will remain liable for all amounts due up to and including the date of termination; and/or accordingly may deny you access to our Services (or any part thereof).</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Entire Agreement</span></strong></p>\r\n<p>The failure of us to exercise or enforce any right or provision of these Terms of Service shall not constitute a waiver of such right or provision.</p>\r\n<p>These Terms of Service and any policies or operating rules posted by us on this site or in respect to The Service constitutes the entire agreement and understanding between you and us and govern your use of the Service, superseding any prior or contemporaneous agreements, communications and proposals, whether oral or written, between you and us (including, but not limited to, any prior versions of the Terms of Service).Any ambiguities in the interpretation of these Terms of Service shall not be construed against the drafting party.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Governing Law</span></strong></p>\r\n<p>These Terms of Service and any separate agreements whereby we provide you Services shall be governed by and construed in accordance with the laws of Your Location Here.</p>\r\n<p>Changes To Terms Of Service</p>\r\n<p>You can review the most current version of the Terms of Service at any time at this page.</p>\r\n<p>We reserve the right, at our sole discretion, to update, change or replace any part of these Terms of Service by posting updates and changes to our website. It is your responsibility to check our website periodically for changes. Your continued use of or access to our website or the Service following the posting of any changes to these Terms of Service constitutes acceptance of those changes.</p>\r\n<p>Contact Information</p>\r\n<p>Questions about the Terms of Service should be sent to us over the contact page</p>', NULL, '2022-11-16 11:10:31', '2022-11-16 11:26:36'),
(8, 'Privacy Policy', 'privacy-policy', '<p>This Privacy Policy describes how your personal information is collected, used, and shared when you visit or make a purchase from https://your-domain.com (the \"Site\"). Continuing using this site means you agree to all of the mentions below.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Personal information we collect</span></strong></p>\r\n<p>When you visit the Site, we automatically collect certain information about your device, including information about your web browser, IP address, time zone, and some of the cookies that are installed on your device. Additionally, as you browse the Site, we collect information about the individual web pages or products that you view, what websites or search terms referred you to the Site, and information about how you interact with the Site. We refer to this automatically-collected information as \"Device Information.\"</p>\r\n<p><span style=\"font-size: 12pt;\">We collect Device Information using the following technologies:</span></p>\r\n<p>- \"Cookies\" are data files that are placed on your device or computer and often include an anonymous unique identifier. For more information about cookies, and how to disable cookies, visit cookiesandyou.com.</p>\r\n<p>- &ldquo;Log files&rdquo; track actions occurring on the Site, and collect data including your IP address, browser type, Internet service provider, referring/exit pages, and date/time stamps.</p>\r\n<p>The payments on our marketplace are made via Paypal and Stripe, so we do not store any transactions related information, like credit card number, billing information etc. We do however store the payment gateway transaction ID for easier reference in case of any disputes. We refer to this information as &ldquo;Order Information.&rdquo;</p>\r\n<p>When we talk about \"Personal Information\" in this Privacy Policy, we are talking both about Device Information and Order Information.</p>\r\n<p><span style=\"font-size: 18pt;\"><strong>How do we use your personal information?</strong></span></p>\r\n<p>We use the Order Information that we collect generally to fulfill any orders placed through the Site. Additionally, we use this Order Information to:</p>\r\n<p>Communicate with you, Screen our orders for potential risk or fraud; and When in line with the preferences you have shared with us, provide you with information or advertising relating to our products or services</p>\r\n<p>We use the Device Information that we collect to help us screen for potential risk and fraud (in particular, your IP address), and more generally to improve and optimize our Site (for example, by generating analytics about how our customers browse and interact with the Site, and to assess the success of our marketing and advertising campaigns).</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Sharing your personal information</span></strong></p>\r\n<p>We do not share your Personal Information with third parties.</p>\r\n<p>We use Google Analytics to help us understand how our customers use the Site. You can read more about how Google uses your Personal Information on their site. You can also opt-out of Google Analytics .</p>\r\n<p>Finally, we may also share limited Personal Information to comply with applicable laws and regulations, to respond to a subpoena, search warrant or other lawful request for information we receive, or to otherwise protect our rights.</p>\r\n<p><span style=\"font-size: 18pt;\"><strong>Do not track</strong></span></p>\r\n<p>Please note that we do not alter our Site&rsquo;s data collection and use practices when we see a Do Not Track signal from your browser.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Your rights</span></strong></p>\r\n<p>If you are a European resident, you have the right to access personal information we hold about you and to ask that your personal information be corrected, updated, or deleted. If you would like to exercise this right, please contact us through the contact information below.</p>\r\n<p>Additionally, if you are a European resident we note that we are processing your information in order to fulfill contracts we might have with you (for example if you make an order through the Site), or otherwise to pursue our legitimate business interests listed above.</p>\r\n<p><span style=\"font-size: 18pt;\"><strong>Data retention</strong></span></p>\r\n<p>When you place an order through the Site, we will maintain your Order Information for our records unless and until you ask us to delete this information.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Changes</span></strong></p>\r\n<p>We may update this privacy policy from time to time in order to reflect, for example, changes to our practices or for other operational, legal or regulatory reasons.</p>\r\n<p><strong><span style=\"font-size: 18pt;\">Contact us</span></strong></p>\r\n<p>For more information about our privacy practices, if you have questions, or if you would like to make a complaint, please get in touch by sending us a contact message.</p>', NULL, '2022-11-17 12:19:41', '2022-11-17 12:19:41');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('Streamer@yopmail.com', '$2y$10$fVQW40qWhmmOYAS7cwuCaehROwFB.w93XaYQBpHlv6MKEMtZmPr9m', '2024-06-27 05:29:26'),
('admin12@gmail.com', '$2y$10$OSHUehT8rwH6oJ.IFv3bEOiFV9HncerfedF0xYIaeQGu6mYxMdHwu', '2024-06-27 11:33:38'),
('Test123@yopmail.com', '$2y$10$fcSX7x3vqArQXjfwKbSPOuAAN4o.Dqtuf01q04cE/WkSiVRN2YQKm', '2024-06-27 11:46:18'),
('bhupendra.canws@gmail.com', '$2y$10$7KoYSlvJpQwu4AamjFWHw.N7e75Bok09pupMKB4wJ6ze/RJupaF/u', '2024-06-28 12:01:51'),
('prem.canws@gmail.com', '$2y$10$/1Xn5r9CjF4jwKAxYco5WetJV8QPs/HajRXTl.6TJOOwkt3amFIsK', '2024-07-23 10:14:36'),
('navjot.canws@gmail.com', '$2y$10$mUmLXAifuc/dHCZ3yWPF9.gZ8xZq0dNO4nMKf2U8jrCLluJSY2Jdu', '2024-07-23 10:15:10');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_stream_payments`
--

CREATE TABLE `pending_stream_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `streamer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'role-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(2, 'role-create', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(3, 'role-edit', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(4, 'role-delete', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(5, 'user-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(6, 'user-update', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(7, 'streamer-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(8, 'streamer-update', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(9, 'subscription-plan-sell-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(10, 'subscription-plan-sell-delete', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(11, 'subscription-plan-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(12, 'subscription-plan-create', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(13, 'subscription-plan-edit', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(14, 'subscription-plan-delete', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(15, 'videos-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(16, 'videos-edit', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(17, 'videos-delete', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(18, 'commission-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(19, 'streamer-catgory-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(20, 'streamer-catgory-create', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(21, 'streamer-catgory-edit', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(22, 'streamer-catgory-delete', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(23, 'video-catgory-list', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(24, 'video-catgory-create', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(25, 'video-catgory-edit', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(26, 'video-catgory-delete', 'web', '2024-10-16 17:50:55', '2024-10-16 17:50:55'),
(27, 'stream-earning-list', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(28, 'video-sales-list', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(29, 'gallery-list', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(30, 'gallery-delete', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(31, 'gallery-sales-list', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(32, 'pages-manger-list', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(33, 'pages-manger-create', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(34, 'pages-manger-edit', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(35, 'pages-manger-delete', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(36, 'send-mails-list', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(37, 'send-mails-create', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(38, 'send-mails-edit', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(39, 'send-mails-delete', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(40, 'mail-configuration', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(41, 'cloud-storage', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(42, 'report-users', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(43, 'report-content', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(44, 'config-login', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(45, 'app-config', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56'),
(46, 'report-stream', 'web', '2024-10-16 17:50:56', '2024-10-16 17:50:56');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `private_streams`
--

CREATE TABLE `private_streams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `streamer_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tokens` decimal(8,2) DEFAULT NULL,
  `stream_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`id`, `email`, `reason`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'aniiyaprem123@gmail.com', 'test', '25', '2024-10-01 12:57:41', '2024-10-01 12:57:41'),
(2, 'aniiyaprem123@gmail.com', 'test', '25', '2024-10-01 12:59:58', '2024-10-01 12:59:58'),
(3, 'wogeh@mailinator.com', 'Animi vel consequat', '49', '2024-12-06 05:00:26', '2024-12-06 05:00:26');

-- --------------------------------------------------------

--
-- Table structure for table `report_contents`
--

CREATE TABLE `report_contents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_streams`
--

CREATE TABLE `report_streams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `report_streams`
--

INSERT INTO `report_streams` (`id`, `email`, `reason`, `user_id`, `item_id`, `item`, `created_at`, `updated_at`) VALUES
(1, 'bhupendra.canws@gmail.com', 'new luchi stream', '49', 'https://love248.com/channel/live-stream/prem80589', 'stream', '2024-11-14 16:26:46', '2024-11-14 16:26:46'),
(2, 'prem.canws@gmail.com', 'test', '49', 'https://love248.com/channel/live-stream/prem80589', 'stream', '2024-12-05 09:45:53', '2024-12-05 09:45:53'),
(3, 'navjot10985@yopmail.com', 'Not liked streamer', '155', 'https://love248.com/channel/live-stream/devcanws', 'stream', '2024-12-10 10:26:13', '2024-12-10 10:26:13'),
(4, 'navjot10985@yopmail.com', 'Not interest', '155', 'https://love248.com/channel/live-stream/devcanws', 'stream', '2024-12-10 10:32:53', '2024-12-10 10:32:53'),
(5, 'jackleo@gmail.com', 'not working :)', '155', 'https://love248.com/channel/live-stream/devcanws', 'stream', '2024-12-10 10:34:25', '2024-12-10 10:34:25'),
(6, 'john@gmail.com', 'reporting this steamer', '155', 'https://love248.com/channel/live-stream/devcanws', 'stream', '2024-12-10 10:37:13', '2024-12-10 10:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2024-10-16 17:55:52', '2024-10-16 17:55:52'),
(2, 'subadmin', 'web', '2024-10-17 09:29:35', '2024-10-17 09:29:35');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(5, 2),
(6, 2),
(20, 2),
(34, 2),
(37, 2),
(42, 2),
(43, 2),
(46, 2);

-- --------------------------------------------------------

--
-- Table structure for table `room_bans`
--

CREATE TABLE `room_bans` (
  `id` int(10) UNSIGNED NOT NULL,
  `streamer_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `send_mails`
--

CREATE TABLE `send_mails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `send_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receiver_email` text COLLATE utf8mb4_unicode_ci,
  `message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `send_mails`
--

INSERT INTO `send_mails` (`id`, `send_email`, `receiver_email`, `message`, `created_at`, `updated_at`) VALUES
(3, 'admin@gmail.com', '[\"just@user.com\",\"ajay@dexdel.com\",\"amar.canws@gmail.com\"]', 'test code', '2024-05-08 00:51:22', '2024-05-08 00:51:22'),
(4, 'admin@gmail.com', '[\"just@user.com\",\"trsrufino@gmail.com\",\"ajay@dexdel.com\",\"amar.canws@gmail.com\"]', 'test data', '2024-05-08 00:52:54', '2024-05-08 00:52:54'),
(7, 'admin@gmail.com', '[\"just@user.com\",\"trsrufino@gmail.com\",\"ajay@dexdel.com\"]', 'test multiple emails', '2024-05-08 07:17:14', '2024-05-08 07:17:14');

-- --------------------------------------------------------

--
-- Table structure for table `streaming_prices`
--

CREATE TABLE `streaming_prices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `streamer_id` int(11) DEFAULT NULL,
  `token_amount` decimal(8,2) DEFAULT NULL,
  `streamer_time_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `streaming_times`
--

CREATE TABLE `streaming_times` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `streamer_id` int(11) DEFAULT NULL,
  `streaming_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` int(10) UNSIGNED NOT NULL,
  `tier_id` int(10) UNSIGNED NOT NULL,
  `streamer_id` int(11) NOT NULL,
  `subscriber_id` int(10) UNSIGNED NOT NULL,
  `subscription_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `subscription_expires` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` enum('Active','Canceled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Active',
  `subscription_tokens` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `subscriptions`
--

INSERT INTO `subscriptions` (`id`, `tier_id`, `streamer_id`, `subscriber_id`, `subscription_date`, `subscription_expires`, `status`, `subscription_tokens`) VALUES
(53, 6, 1, 5, '2023-03-27 11:48:35', '2023-06-19 21:00:00', 'Active', 450),
(54, 14, 25, 49, '2024-06-28 11:32:05', '2024-07-28 11:32:05', 'Active', 12),
(55, 14, 25, 23, '2024-06-28 12:04:17', '2024-07-28 12:04:17', 'Active', 12),
(56, 15, 49, 133, '2024-10-24 15:54:11', '2024-11-24 15:54:10', 'Active', 10);

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `subscription_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subscription_price` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `days` int(11) NOT NULL DEFAULT '0',
  `Is_purchase` int(11) NOT NULL DEFAULT '0',
  `details` text COLLATE utf8mb4_unicode_ci,
  `status` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subscription_plans`
--

INSERT INTO `subscription_plans` (`id`, `subscription_name`, `subscription_price`, `days`, `Is_purchase`, `details`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Free Basic Plan', '0', 30, 0, 'Free access to public rooms for viewing streams.,\n    No capability to send messages or make proposals for private rooms. Basic advertising is displayed during viewing.,\n    Option to purchase individual tokens for viewing photo and video galleries.,\n    The user cannot send proposals for private rooms in this plan.', 0, '2024-06-12 11:21:44', '2024-06-15 11:06:19'),
(2, 'Monthly Premium Plan', '3', 30, 0, 'Acesso total a salas p&uacute;blicas., Capacidade de enviar mensagens escritas para streamers durante transmiss&otilde;es p&uacute;blicas. Permiss&atilde;o para enviar propostas para entrar em salas privadas., Acesso a galerias de fotos e v&iacute;deos por meio de pagamento de token., Atendimento priorit&aacute;rio ao cliente e suporte t&eacute;cnico.', 0, '2024-06-15 11:09:12', '2024-10-19 16:19:59'),
(3, 'Quarterly Premium Plan', '6', 182, 0, 'Todos os benef&iacute;cios do Plano Premium Mensal, com desconto para pagamento trimestral., Acesso total &agrave;s salas p&uacute;blicas., Capacidade de enviar mensagens escritas aos streamers durante transmiss&otilde;es p&uacute;blicas., Permiss&atilde;o para enviar propostas para entrar em salas privadas., Acesso a galerias de fotos e v&iacute;deos por meio de pagamento simb&oacute;lico., Atendimento ao cliente e suporte t&eacute;cnico priorit&aacute;rios., Acesso exclusivo a eventos especiais ou transmiss&otilde;es premium.', 0, '2024-06-15 11:10:05', '2024-10-19 16:21:08');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plan_sells`
--

CREATE TABLE `subscription_plan_sells` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_plan` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `expire_date` datetime NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `gateway` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subs_cron_jobs`
--

CREATE TABLE `subs_cron_jobs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tag_pixels`
--

CREATE TABLE `tag_pixels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tiers`
--

CREATE TABLE `tiers` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `tier_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `perks` text COLLATE utf8mb4_unicode_ci,
  `price` int(11) NOT NULL,
  `six_months_discount` int(11) DEFAULT NULL,
  `one_year_discount` int(11) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tiers`
--

INSERT INTO `tiers` (`id`, `user_id`, `tier_name`, `perks`, `price`, `six_months_discount`, `one_year_discount`, `deleted_at`) VALUES
(14, 25, 'tier  1', 'g fbhrthrh', 12, 10, 20, '2024-12-29 12:27:15'),
(15, 173, 'sdsd', 'asdada', 32, 10, 20, '2025-04-24 17:38:02');

-- --------------------------------------------------------

--
-- Table structure for table `tips`
--

CREATE TABLE `tips` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `streamer_id` int(10) UNSIGNED NOT NULL,
  `tokens` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `token_packs`
--

CREATE TABLE `token_packs` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokens` int(10) UNSIGNED NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `token_packs`
--

INSERT INTO `token_packs` (`id`, `name`, `tokens`, `price`) VALUES
(1, 'Starter', 100, 100),
(2, 'Bronze', 500, 450),
(3, 'Silver', 750, 500),
(4, 'Gold', 1000, 850),
(5, 'Platinum', 5000, 3900);

-- --------------------------------------------------------

--
-- Table structure for table `token_sales`
--

CREATE TABLE `token_sales` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `tokens` int(10) UNSIGNED NOT NULL,
  `amount` double NOT NULL,
  `gateway` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('paid','pending') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `skin_tone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cover_picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `headline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_unicode_ci,
  `tokens` decimal(8,2) DEFAULT '0.00',
  `is_streamer` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `is_streamer_verified` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `streamer_verification_sent` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `live_status` enum('online','offline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offline',
  `popularity` int(11) NOT NULL DEFAULT '0',
  `is_admin` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `is_supper_admin` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_payment_method_id` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stripe_customer_id` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message_video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `whatsapp_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `name`, `email`, `skin_tone`, `dob`, `email_verified_at`, `password`, `profile_picture`, `cover_picture`, `headline`, `about`, `tokens`, `is_streamer`, `is_streamer_verified`, `streamer_verification_sent`, `live_status`, `popularity`, `is_admin`, `is_supper_admin`, `ip`, `remember_token`, `stripe_payment_method_id`, `stripe_customer_id`, `message_video`, `created_at`, `updated_at`, `whatsapp_number`) VALUES
(129, 'Twitcher Admin', 'TheAdmin', 'trsrufino@gmail.com', NULL, NULL, NULL, '$2y$10$L5WG71fq.QPMAXX.ILTCCuSL1.ZaqVYZ13V.u.FnJ1DZ2j778.U4a', NULL, NULL, NULL, NULL, '0.00', 'no', 'no', 'no', 'offline', 0, 'yes', 'yes', '103.84.165.38', NULL, NULL, NULL, NULL, '2024-10-16 17:55:52', '2025-04-19 23:25:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_meta`
--

CREATE TABLE `user_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `meta_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `free_for_subs` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL,
  `views` int(11) NOT NULL DEFAULT '0',
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_categories`
--

CREATE TABLE `video_categories` (
  `id` int(11) NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `video_categories`
--

INSERT INTO `video_categories` (`id`, `category`) VALUES
(1, 'Fun'),
(3, 'Sports'),
(4, 'Personal'),
(5, 'Other'),
(8, 'Inspirational');

-- --------------------------------------------------------

--
-- Table structure for table `video_sales`
--

CREATE TABLE `video_sales` (
  `id` int(11) NOT NULL,
  `video_id` int(10) UNSIGNED NOT NULL,
  `streamer_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `price` decimal(8,2) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `tokens` int(10) UNSIGNED NOT NULL,
  `amount` double(10,2) NOT NULL,
  `status` enum('Pending','Paid','Canceled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bank_customers`
--
ALTER TABLE `bank_customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banned`
--
ALTER TABLE `banned`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_user`
--
ALTER TABLE `category_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `followables`
--
ALTER TABLE `followables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `followables_followable_type_followable_id_index` (`followable_type`,`followable_id`),
  ADD KEY `followables_followable_type_accepted_at_index` (`followable_type`,`accepted_at`),
  ADD KEY `followables_user_id_index` (`user_id`);

--
-- Indexes for table `galleries`
--
ALTER TABLE `galleries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gallery_sales`
--
ALTER TABLE `gallery_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `histories`
--
ALTER TABLE `histories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Indexes for table `mercado_accounts`
--
ALTER TABLE `mercado_accounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mercado_pix_payments`
--
ALTER TABLE `mercado_pix_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `n_subscriptions`
--
ALTER TABLE `n_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options_table`
--
ALTER TABLE `options_table`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `options_table_option_name_unique` (`option_name`),
  ADD KEY `options_table_option_name_index` (`option_name`);

--
-- Indexes for table `pagar_pix_payments`
--
ALTER TABLE `pagar_pix_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pending_stream_payments`
--
ALTER TABLE `pending_stream_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `private_streams`
--
ALTER TABLE `private_streams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_contents`
--
ALTER TABLE `report_contents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_streams`
--
ALTER TABLE `report_streams`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `room_bans`
--
ALTER TABLE `room_bans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `send_mails`
--
ALTER TABLE `send_mails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `streaming_prices`
--
ALTER TABLE `streaming_prices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `streaming_times`
--
ALTER TABLE `streaming_times`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_plan_sells`
--
ALTER TABLE `subscription_plan_sells`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscription_plan_sells_user_id_foreign` (`user_id`);

--
-- Indexes for table `subs_cron_jobs`
--
ALTER TABLE `subs_cron_jobs`
  ADD UNIQUE KEY `id` (`id`);

--
-- Indexes for table `tag_pixels`
--
ALTER TABLE `tag_pixels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tiers`
--
ALTER TABLE `tiers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tips`
--
ALTER TABLE `tips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `token_packs`
--
ALTER TABLE `token_packs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `token_sales`
--
ALTER TABLE `token_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_meta`
--
ALTER TABLE `user_meta`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_categories`
--
ALTER TABLE `video_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_sales`
--
ALTER TABLE `video_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bank_customers`
--
ALTER TABLE `bank_customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `banned`
--
ALTER TABLE `banned`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `category_user`
--
ALTER TABLE `category_user`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followables`
--
ALTER TABLE `followables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `galleries`
--
ALTER TABLE `galleries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `gallery_sales`
--
ALTER TABLE `gallery_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `histories`
--
ALTER TABLE `histories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=219;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mercado_accounts`
--
ALTER TABLE `mercado_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `mercado_pix_payments`
--
ALTER TABLE `mercado_pix_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `n_subscriptions`
--
ALTER TABLE `n_subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- AUTO_INCREMENT for table `options_table`
--
ALTER TABLE `options_table`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=245;

--
-- AUTO_INCREMENT for table `pagar_pix_payments`
--
ALTER TABLE `pagar_pix_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pending_stream_payments`
--
ALTER TABLE `pending_stream_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `private_streams`
--
ALTER TABLE `private_streams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `report_contents`
--
ALTER TABLE `report_contents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `report_streams`
--
ALTER TABLE `report_streams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `room_bans`
--
ALTER TABLE `room_bans`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `send_mails`
--
ALTER TABLE `send_mails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `streaming_prices`
--
ALTER TABLE `streaming_prices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `streaming_times`
--
ALTER TABLE `streaming_times`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subscription_plan_sells`
--
ALTER TABLE `subscription_plan_sells`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `subs_cron_jobs`
--
ALTER TABLE `subs_cron_jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tag_pixels`
--
ALTER TABLE `tag_pixels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tiers`
--
ALTER TABLE `tiers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tips`
--
ALTER TABLE `tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `token_packs`
--
ALTER TABLE `token_packs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `token_sales`
--
ALTER TABLE `token_sales`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=176;

--
-- AUTO_INCREMENT for table `user_meta`
--
ALTER TABLE `user_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `video_categories`
--
ALTER TABLE `video_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `video_sales`
--
ALTER TABLE `video_sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscription_plan_sells`
--
ALTER TABLE `subscription_plan_sells`
  ADD CONSTRAINT `subscription_plan_sells_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
