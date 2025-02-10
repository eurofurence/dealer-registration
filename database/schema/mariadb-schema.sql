/*!999999\- enable the sandbox mode */ 
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `applications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `applications` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `table_type_requested` bigint(20) unsigned DEFAULT NULL,
  `table_type_assigned` bigint(20) unsigned DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `table_number` varchar(255) DEFAULT NULL,
  `invite_code_shares` varchar(255) DEFAULT NULL,
  `invite_code_assistants` varchar(255) DEFAULT NULL,
  `merchandise` text DEFAULT NULL,
  `wanted_neighbors` text DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `is_afterdark` tinyint(1) DEFAULT NULL,
  `is_power` tinyint(1) DEFAULT NULL,
  `is_wallseat` tinyint(1) DEFAULT NULL,
  `waiting_at` timestamp NULL DEFAULT NULL,
  `offer_sent_at` timestamp NULL DEFAULT NULL,
  `offer_accepted_at` timestamp NULL DEFAULT NULL,
  `checked_in_at` timestamp NULL DEFAULT NULL,
  `canceled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `checked_out_at` timestamp NULL DEFAULT NULL,
  `additional_space_request` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `applications_invite_code_shares_unique` (`invite_code_shares`),
  UNIQUE KEY `applications_invite_code_assistants_unique` (`invite_code_assistants`),
  KEY `applications_user_id_foreign` (`user_id`),
  KEY `applications_table_type_requested_foreign` (`table_type_requested`),
  KEY `applications_table_type_assigned_foreign` (`table_type_assigned`),
  KEY `applications_parent_id_foreign` (`parent_id`),
  CONSTRAINT `applications_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applications_table_type_assigned_foreign` FOREIGN KEY (`table_type_assigned`) REFERENCES `table_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applications_table_type_requested_foreign` FOREIGN KEY (`table_type_requested`) REFERENCES `table_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `applications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `comments` (
  `uuid` char(36) NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `application_id` bigint(20) unsigned NOT NULL,
  `text` text NOT NULL,
  `admin_only` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `comments_user_id_foreign` (`user_id`),
  KEY `comments_application_id_foreign` (`application_id`),
  CONSTRAINT `comments_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keyword_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keyword_profile` (
  `profile_id` bigint(20) unsigned NOT NULL,
  `keyword_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  KEY `keyword_profile_profile_id_foreign` (`profile_id`),
  KEY `keyword_profile_keyword_id_foreign` (`keyword_id`),
  CONSTRAINT `keyword_profile_keyword_id_foreign` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `keyword_profile_profile_id_foreign` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `keywords`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `keywords` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(255) NOT NULL,
  `category_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `keywords_slug_unique` (`slug`),
  KEY `keywords_category_id_foreign` (`category_id`),
  CONSTRAINT `keywords_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profiles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profiles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `application_id` bigint(20) unsigned DEFAULT NULL,
  `short_desc` text DEFAULT NULL,
  `artist_desc` text DEFAULT NULL,
  `art_desc` text DEFAULT NULL,
  `website` text DEFAULT NULL,
  `twitter` text DEFAULT NULL,
  `telegram` text DEFAULT NULL,
  `discord` text DEFAULT NULL,
  `tweet` text DEFAULT NULL,
  `art_preview_caption` text DEFAULT NULL,
  `image_thumbnail` varchar(255) DEFAULT NULL,
  `image_art` varchar(255) DEFAULT NULL,
  `image_artist` varchar(255) DEFAULT NULL,
  `attends_thu` tinyint(1) DEFAULT NULL,
  `attends_fri` tinyint(1) DEFAULT NULL,
  `attends_sat` tinyint(1) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `mastodon` varchar(255) DEFAULT NULL,
  `bluesky` varchar(255) DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `profiles_application_id_foreign` (`application_id`),
  CONSTRAINT `profiles_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `table_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `table_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `spaces` int(11) NOT NULL,
  `seats` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `package` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reg_id` varchar(255) DEFAULT NULL,
  `identity_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `groups` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

/*!999999\- enable the sandbox mode */ 
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (1,'2014_10_12_000000_create_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (2,'2019_08_19_000000_create_failed_jobs_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (3,'2019_12_14_000001_create_personal_access_tokens_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (4,'2023_02_28_211414_create_table_types_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (5,'2023_02_28_212504_create_applications_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (6,'2023_02_28_214535_create_profiles_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (7,'2023_05_03_204632_add_mail_status_to_application',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (8,'2023_05_03_213105_add_package_name_to_table_type',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (9,'2023_05_16_192248_remove_mail_status_is_notified_from_applications',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (10,'2023_08_07_194554_add_groups_to_users_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (11,'2023_08_08_000000_alter_users_groups_to_longtext',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (12,'2023_08_08_215547_create_comments_table',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (13,'2023_08_09_222220_add_checked_out_status',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (14,'2023_08_13_135050_increase_comment_text_length',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (15,'2024_01_26_160127_recreate_categories_keywords_tables',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (16,'2024_01_26_201135_drop_profile_categories',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (17,'2024_01_29_222300_add_bluesky_mastodon_to_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (18,'2024_01_30_001055_alter_application_invite_codes_to_unique',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (19,'2024_02_01_001244_add_is_hidden_to_profile',1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (20,'2024_02_13_230449_alter_user_email_not_unique',2);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES (21,'2024_02_18_125612_add_space_request_to_application',3);
