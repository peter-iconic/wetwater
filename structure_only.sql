-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: school_social_app
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;

/*!40101 SET NAMES utf8mb4 */;

/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;

/*!40103 SET TIME_ZONE='+00:00' */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;

/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `ad_analytics`
--
DROP TABLE IF EXISTS `ad_analytics`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `ad_analytics` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `type` enum('impression', 'click') NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `ad_id` (`ad_id`),
    CONSTRAINT `ad_analytics_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ad_billing`
--
DROP TABLE IF EXISTS `ad_billing`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `ad_billing` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `amount` decimal(10, 2) NOT NULL,
    `status` enum('pending', 'paid') DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `ad_id` (`ad_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `ad_billing_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
    CONSTRAINT `ad_billing_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ads`
--
DROP TABLE IF EXISTS `ads`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `ads` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `link` varchar(255) NOT NULL,
    `status` enum('pending', 'approved', 'rejected') DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `start_time` datetime DEFAULT NULL,
    `end_time` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `advanced_analytics`
--
DROP TABLE IF EXISTS `advanced_analytics`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `advanced_analytics` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ad_id` int(11) DEFAULT NULL,
    `user_id` int(11) DEFAULT NULL,
    `metric` varchar(255) NOT NULL,
    `value` decimal(10, 2) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `ad_id` (`ad_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `advanced_analytics_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
    CONSTRAINT `advanced_analytics_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `calls`
--
DROP TABLE IF EXISTS `calls`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `calls` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `caller_id` int(11) NOT NULL,
    `receiver_id` int(11) NOT NULL,
    `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
    `end_time` timestamp NULL DEFAULT NULL,
    `duration` int(11) DEFAULT NULL,
    `type` enum('voice', 'video') NOT NULL,
    `status` enum('ongoing', 'completed', 'missed') DEFAULT 'ongoing',
    PRIMARY KEY (`id`),
    KEY `caller_id` (`caller_id`),
    KEY `receiver_id` (`receiver_id`),
    CONSTRAINT `calls_ibfk_1` FOREIGN KEY (`caller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `calls_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `comments`
--
DROP TABLE IF EXISTS `comments`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `comments` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `post_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `content` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `parent_comment_id` int(11) DEFAULT NULL,
    `type` enum('post', 'video') NOT NULL,
    PRIMARY KEY (`id`),
    KEY `post_id` (`post_id`),
    KEY `user_id` (`user_id`),
    KEY `parent_comment_id` (`parent_comment_id`),
    CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 17 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `followers`
--
DROP TABLE IF EXISTS `followers`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `followers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `follower_id` int(11) NOT NULL,
    `following_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `follower_id` (`follower_id`),
    KEY `following_id` (`following_id`),
    CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 34 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `friend_requests`
--
DROP TABLE IF EXISTS `friend_requests`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `friend_requests` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `sender_id` int(11) NOT NULL,
    `receiver_id` int(11) NOT NULL,
    `status` enum('pending', 'accepted', 'declined') DEFAULT 'pending',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `sender_id` (`sender_id`),
    KEY `receiver_id` (`receiver_id`),
    CONSTRAINT `friend_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `friend_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 10 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `friends`
--
DROP TABLE IF EXISTS `friends`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `friends` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `friend_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_friendship` (`user_id`, `friend_id`),
    KEY `friend_id` (`friend_id`),
    CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 13 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `messages`
--
DROP TABLE IF EXISTS `messages`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `messages` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `sender_id` int(11) NOT NULL,
    `receiver_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `is_read` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `sender_message` text DEFAULT NULL,
    `audio_path` varchar(255) DEFAULT NULL,
    `is_delivered` tinyint(1) DEFAULT 0,
    `is_seen` tinyint(1) DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `sender_id` (`sender_id`),
    KEY `receiver_id` (`receiver_id`),
    CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 63 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `notifications`
--
DROP TABLE IF EXISTS `notifications`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `notifications` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `message` text NOT NULL,
    `is_read` tinyint(1) DEFAULT 0,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB AUTO_INCREMENT = 35 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `post_tags`
--
DROP TABLE IF EXISTS `post_tags`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `post_tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `post_id` int(11) NOT NULL,
    `tag` varchar(50) NOT NULL,
    PRIMARY KEY (`id`),
    KEY `post_id` (`post_id`),
    CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`)
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `posts`
--
DROP TABLE IF EXISTS `posts`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `posts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `content` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `image` varchar(255) DEFAULT NULL,
    `original_post_id` int(11) DEFAULT NULL,
    `caption` text DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 24 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pricing_plans`
--
DROP TABLE IF EXISTS `pricing_plans`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `pricing_plans` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `type` enum('impression', 'click', 'duration') NOT NULL,
    `rate` decimal(10, 2) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `reactions`
--
DROP TABLE IF EXISTS `reactions`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `reactions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `post_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `type` enum('like', 'dislike') NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_reaction` (`post_id`, `user_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 49 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `stories`
--
DROP TABLE IF EXISTS `stories`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `stories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `media_url` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `expires_at` timestamp NOT NULL DEFAULT(current_timestamp() + interval 24 hour),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 16 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users`
--
DROP TABLE IF EXISTS `users`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `username` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `profile_picture` varchar(255) DEFAULT 'default_profile.jpg',
    `is_admin` tinyint(1) DEFAULT 0,
    `last_activity` timestamp NOT NULL DEFAULT current_timestamp(),
    `full_name` varchar(100) DEFAULT NULL,
    `bio` text DEFAULT NULL,
    `location` varchar(100) DEFAULT NULL,
    `interests` text DEFAULT NULL,
    `education` varchar(100) DEFAULT NULL,
    `work` varchar(100) DEFAULT NULL,
    `website` varchar(255) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `public_key` text NOT NULL,
    `private_key` text NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 18 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `videos`
--
DROP TABLE IF EXISTS `videos`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `videos` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `video_url` varchar(255) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `shares` int(11) DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 4 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `views`
--
DROP TABLE IF EXISTS `views`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `views` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `video_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `video_id` (`video_id`),
    CONSTRAINT `views_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
    CONSTRAINT `views_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`)
  ) ENGINE = InnoDB AUTO_INCREMENT = 205 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webrtc_signaling_logs`
--
DROP TABLE IF EXISTS `webrtc_signaling_logs`;

/*!40101 SET @saved_cs_client     = @@character_set_client */;

/*!40101 SET character_set_client = utf8 */;

CREATE TABLE
  `webrtc_signaling_logs` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `sender_id` int(11) NOT NULL,
    `receiver_id` int(11) NOT NULL,
    `type` enum('offer', 'answer', 'candidate') NOT NULL,
    `data` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `sender_id` (`sender_id`),
    KEY `receiver_id` (`receiver_id`),
    CONSTRAINT `webrtc_signaling_logs_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `webrtc_signaling_logs_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
  ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;

/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-16 22:34:46