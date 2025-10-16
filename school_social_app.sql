-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2025 at 03:36 AM
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
-- Database: `school_social_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE `ads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `link` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `advanced_analytics`
--

CREATE TABLE `advanced_analytics` (
  `id` int(11) NOT NULL,
  `ad_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `metric` varchar(255) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_analytics`
--

CREATE TABLE `ad_analytics` (
  `id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `type` enum('impression','click') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_billing`
--

CREATE TABLE `ad_billing` (
  `id` int(11) NOT NULL,
  `ad_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `calls`
--

CREATE TABLE `calls` (
  `id` int(11) NOT NULL,
  `caller_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_time` timestamp NULL DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `type` enum('voice','video') NOT NULL,
  `status` enum('ongoing','completed','missed') DEFAULT 'ongoing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_comment_id` int(11) DEFAULT NULL,
  `type` enum('post','video') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`, `parent_comment_id`, `type`) VALUES
(1, 1, 1, 'so what', '2025-03-05 15:59:40', NULL, 'post'),
(2, 1, 1, 'who', '2025-03-05 16:04:41', 1, 'post'),
(3, 1, 1, 'what', '2025-03-05 16:07:02', 2, 'post'),
(4, 2, 1, 'yo', '2025-03-07 10:20:04', NULL, 'post'),
(5, 2, 1, 'whats yo bro', '2025-03-07 10:25:02', 4, 'post'),
(6, 7, 7, 'so what', '2025-03-10 17:05:18', NULL, 'post'),
(7, 7, 7, 'get out', '2025-03-10 17:13:40', 6, 'post'),
(8, 9, 6, 'Peter ', '2025-03-13 16:04:19', NULL, 'post'),
(9, 9, 4, 'today', '2025-03-15 10:27:30', 8, 'post'),
(10, 10, 4, 'man', '2025-03-15 10:57:46', NULL, 'post'),
(11, 11, 10, 'wassup', '2025-04-03 16:16:48', NULL, 'post'),
(12, 13, 10, 'yes\r\n', '2025-04-03 16:43:35', NULL, 'post'),
(13, 15, 10, 'Yo bro\r\n', '2025-04-03 16:47:45', NULL, 'post'),
(14, 16, 6, 'hey', '2025-04-24 17:31:35', NULL, 'post'),
(15, 16, 13, 'Thank Mr Mike????????', '2025-04-24 17:32:23', 14, 'post'),
(16, 22, 17, 'bomboclat,,let me find u with my girl', '2025-10-15 14:26:32', NULL, 'post');

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `following_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `follower_id`, `following_id`, `created_at`) VALUES
(2, 7, 2, '2025-03-09 21:36:39'),
(3, 7, 3, '2025-03-09 21:40:15'),
(4, 7, 4, '2025-03-09 21:40:25'),
(5, 7, 1, '2025-03-09 21:40:38'),
(6, 6, 7, '2025-03-10 04:20:45'),
(7, 7, 6, '2025-03-10 09:41:57'),
(8, 4, 7, '2025-03-10 13:15:04'),
(9, 6, 5, '2025-03-10 13:23:05'),
(10, 5, 7, '2025-03-10 17:16:56'),
(11, 5, 6, '2025-03-10 17:24:59'),
(12, 1, 6, '2025-03-14 10:53:17'),
(13, 6, 1, '2025-03-14 10:55:56'),
(14, 8, 6, '2025-03-14 11:16:30'),
(15, 6, 8, '2025-03-14 11:18:12'),
(16, 9, 6, '2025-03-21 12:06:08'),
(17, 7, 9, '2025-03-21 12:08:44'),
(18, 9, 7, '2025-03-21 12:09:28'),
(19, 10, 6, '2025-04-03 16:19:22'),
(20, 6, 10, '2025-04-03 16:22:55'),
(21, 6, 13, '2025-04-24 17:22:42'),
(22, 13, 6, '2025-04-24 17:23:22'),
(23, 12, 1, '2025-04-24 17:30:28'),
(24, 12, 6, '2025-04-24 17:30:51'),
(25, 14, 6, '2025-04-28 17:51:46'),
(26, 6, 14, '2025-04-28 17:58:09'),
(27, 1, 3, '2025-04-30 09:33:58'),
(28, 3, 1, '2025-04-30 09:35:31'),
(29, 15, 6, '2025-05-22 16:53:17'),
(30, 6, 15, '2025-05-22 16:54:41'),
(31, 14, 16, '2025-06-07 16:22:50'),
(32, 16, 1, '2025-06-07 16:28:16'),
(33, 16, 14, '2025-06-07 16:28:38');

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friends`
--

INSERT INTO `friends` (`id`, `user_id`, `friend_id`, `created_at`) VALUES
(1, 3, 1, '2025-03-07 13:19:31'),
(2, 1, 3, '2025-03-07 13:19:31'),
(3, 5, 3, '2025-03-08 23:21:38'),
(4, 3, 5, '2025-03-08 23:21:38'),
(7, 5, 6, '2025-03-08 23:31:29'),
(8, 6, 5, '2025-03-08 23:31:29'),
(9, 6, 7, '2025-03-09 10:03:20'),
(10, 7, 6, '2025-03-09 10:03:20'),
(11, 5, 7, '2025-03-09 11:31:21'),
(12, 7, 5, '2025-03-09 11:31:21');

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`id`, `sender_id`, `receiver_id`, `status`, `created_at`) VALUES
(1, 1, 2, 'pending', '2025-03-07 13:18:33'),
(2, 1, 3, 'accepted', '2025-03-07 13:18:42'),
(3, 3, 5, 'accepted', '2025-03-08 23:21:06'),
(4, 5, 3, 'accepted', '2025-03-08 23:21:23'),
(5, 6, 5, 'accepted', '2025-03-08 23:29:55'),
(6, 7, 1, 'pending', '2025-03-09 10:00:23'),
(7, 7, 2, 'pending', '2025-03-09 10:00:31'),
(8, 7, 6, 'accepted', '2025-03-09 10:02:42'),
(9, 7, 5, 'accepted', '2025-03-09 11:31:02');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sender_message` text DEFAULT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `is_delivered` tinyint(1) DEFAULT 0,
  `is_seen` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`, `sender_message`, `audio_path`, `is_delivered`, `is_seen`) VALUES
(1, 1, 3, 'hello', 0, '2025-03-07 13:35:18', NULL, NULL, 0, 0),
(2, 3, 1, 'what is your prob', 0, '2025-03-07 14:05:30', NULL, NULL, 0, 0),
(3, 6, 5, 'eaMj9Ykjq4yX6MYhQHOenJlo+Etc/jL4+Iri26TKUaUThS/n0bt4YQMKKlswnS7edphRjwmIzQi3gR6hS2cSJ8mA3DVPd6AqdOiXFLu9Y5mXFzPyE6UrA2MlIgK8rpmBL49H1FxrZaSLDP9GeWf2+QvMf6cSRIGPXgCEeCCpR+kJ0IzJMlQDJcVEZ/HwDJ+ZMza+mDK751aEqkrSN/zmPBrf4VjYEoK5WtI3AoWWOUbP15fQsFvt4e8FRQwSYUNluEzDtHzkAKre3e0Vbt6AnBmemuDrQN4t6qFx34GMv6XMhvT7SSOpIYsJ1EiSvmdpYXRP7qUTsu0Z8vJZfsFdxQ==', 0, '2025-03-08 23:44:22', NULL, NULL, 0, 0),
(4, 5, 6, 'S5ENkdI3m1tqjb1q/vrdO4REgZIVdD8grvUIuk26V+gYdTASuv3dcHCOIKvt4n/fxRPyZWpgJEyAXOK1nY7X8kXkyDscKTWGZQ6xk+qv4G/R+TDUyVHvA+JgQZTxVxXlvED+sohqszF9w55LBuY4RgFrl5tQQkE14Y9RFOticsbmutvAdWyvayzB43tZxDzlfCYcVuzImNnnRfVUY1xF3F+EgQ5CteOfCTb2TkYnruHvE0HGKDT4EuO4be8gEeLvwX3JBSf7dwBRua9S3eRXi88qyMsGxVcVhcr2CzT+0qViNYDjEFu2n4hqxvrhvprUmxq9ge0UuOeEQWz2QffC2A==', 0, '2025-03-08 23:50:08', NULL, NULL, 0, 0),
(5, 6, 5, 'ka4SfysDgwracEJHd1GlrBVf9gJeb//gizrREjvmp/O6lEyRJsfBEzPS2nWiiwp3T6p14sBizBLkwz6dD4W9Zo6E5L+CmXeqemjPN0HgMLkIiJf4mkxWTvHjfZtXvb8TzB/yu0sUYvi9uInUdKzUxH0m+zaiq3R9tnvhQQUkAYfGCmuqvbBQIsxHzH7FgfzkwblzKK6T/w9Oc1zooVTDyFIRKqFDG6UY1o8P+lcieka9BGvOeI9xRUaI2xlRjMdBe/e0Lw6cXyLwstc2YIWHxSsAz3lmov/ZZcoA28M74kmRRpXeui4KW4SzPfTw7uP2PgkCahB2416dTRNlCrKZqg==', 0, '2025-03-08 23:53:40', NULL, NULL, 0, 0),
(6, 6, 5, 'EEh1izydpltz/p4OoQujzprTqr11gjjoOhTLpKLuD4p87bptHGPprn5Lb0y18fHjWN36XG/SKExiE0+e60ZSjrlCNnlpdtBvHvzv6XY5RL9f84BG0gRJMlOGQByoRU4jvutNrT68sZSDtLyTKwrL1F+rTpvD5JeFv5t90SsX7BPIuYQQiEA9m3C9QHSfYp/7vs3JCa5HJidiL+kFlIHh5WlKjmKZ/UOFVmmzU5E2PoxqSsyDFmIMZoHKnRyyw8p6b2wsHgbsEXu408H4oowbZVzeY3mZ0HkcWhnIsXkm+Ya7dc4Sco3CphU1UjQHPHW5elrggvJwH1r5z1yZQH7LGQ==', 0, '2025-03-09 00:01:26', 'hey', NULL, 0, 0),
(7, 5, 6, 'WuUFfLHGDfxiJc0u4emqc81b9r/7QP5y8HniDJCdupTMUK9fZndgnAxMoBOEh1qtV+TFNhg0qWqUFlbtm3whTgXfE6Wxt5IKALCfnnoJcUn5DIv2Ve3n1IyXC6R+lBmUsyfmCYtchJcejmDRIWFM8hYTarPBWg0BAgr9BS/aTTel+6WLrVb5ABEBIei1pVJEO9qF36mldpK0cxuCcxM7gE+kerQRkIXrlIarBoiSvJ379JsEkAuc6VvU78am/7HB0xlDBpIu/iBf1SpN+osWCj1r8H0YM8/U3uJeHeQ5zxeN3DOfs+l9v1uGUR5PSCczQ7UgZeHQzG7yi3axSRR48w==', 0, '2025-03-09 00:02:31', 'my gee', NULL, 0, 0),
(8, 5, 6, 'AoEBMv+kGFvUYTAAdWPeLOna5ko16xOfcfB5vwRMDAYZX7oSy5jKo9KovHM2/4pNauZTKhdLpMIzOWQwI6Tlo+3GLzvqGbpJvamMKmp6wCf3TXhuRZEiypeqFKyFBWamzIF6H98BVeC1nNh53C6unaLm1nGT9vyywdUjNQx0VrJmF1vYvNceSSqIoGClgOx9SvzlKv0f1FftI+P4d1kQHe9sy3RcknEv4gxVPRpag86lusxjxS7QPQJFCCCFkaMy78yeaxFJ/N8PYVo+ZbHb/qcK66cU/NM7dlt8oUbQ0BUg+wqiAG7pRuJq+h9Dy7BaID2Xfjp0HFOliC9Enylo0w==', 0, '2025-03-09 00:09:22', 'wassp', NULL, 0, 0),
(9, 6, 5, 'EI2nNkVCiKiDst8NWVv6E8SfVbsQbXG6yNKhQzzuKv9I7d44yq4d0bKwRZ4FnpCt87i21B6wdzwptDpiqsMR+vNyaJPiX8KGXuO+Nx8C8569Z3OqFKZu/eC3WGRoJC/S0hbFtlOGRDPW6ThVg/MZcMUhdkPoyyhpYtKJo9q05CzLIqaME8V3AcXwdc5p6W8WXa6JEwFy9W4E4+tedb+6kA9rGJ2zROuv8Ykj7diVfltlzumgsW5/93uDOGBfDQUU+QyxygWri6uNf3DiVLgtSusgua5s3fmDtY0XFN/ZIWNb+LjCe7idf9Ty1LjlzrFZBSPql4bp2OVxLAYHEd0eIA==', 0, '2025-03-09 00:42:56', 'hey', NULL, 0, 0),
(10, 6, 5, 'P5qzC36Yw7zGdEZ4YRPcIX/6tB6SZbe+i3VHUNoj5FbXpM8LFBzVUgArSf/+60klLPqGdfgJ7W2DvQbWfB6AKY6xH74oYxbM4EUD92sONoe+ABe1uzqV9pu388t5FBuvndzyV3PJq5PXNO5nw8mtc/DtT+7kLY/OBa7t1rxgLWeE5IzDyXknE34LHfHeQ4rQktWgzIXFriogp8Zxmv266gkFC2xpb+RTBrn1QXzx6YKyAo/DYVLhrUSIzziykgN3Fj4hQjMRaAkBH0Mt5mYBQ2roNvmMY5plDCY1Ms3TWLVsQ8Peie6dOJtCb9Vy0KmVQuIscHPQFb18MUt5OY+o5w==', 0, '2025-03-09 00:43:17', 'hey', NULL, 0, 0),
(11, 5, 6, 'L8YbHGn8Vb3GCoO3xsvkabYVJmrg21Vw5lMwhuKVTvDpOH577Es3sRVX5zBCChbBd/+Xv0g6/AAfOrr9FWGwCtFuq/eeYn71ZPVHKQIF8KX54/0yZiZLare8zJlqOia18MputdwlnqDG15BfqybVLn+CmI4KfZJRZIwbuClStlsXkAZnqd2L1vhOgLhsjWlzRRs8XTQI/AHYPO/6LK2MhpCx1DwiggHRSdBFqXsRSYmRGMgwIGKIJBDv6flOZkG2VhsT90ASw/hh6HGfkF7rQAtmVVzRcLcNHwkMWrToahZLgDVrUlG/9YbXSenOsfVnKfg0WEsC539P6IN2Nbrdbw==', 0, '2025-03-09 00:43:44', 'waasp gee', NULL, 0, 0),
(12, 5, 6, 'dQKIYBNNw9PLiW8thSkWNFnXLYorqNMQb2gWQd72Xj6/hbOxZ9hlhwzSs/cU3nv21QJ1yG7bJnmJ1vCMtbS5aRdqLqT+/nllHOlc+H2hjqaDb2E8QLRC9Bu3QaXnQG8wAk4y750Jl5CZbVJ0TWDbyAbgZ2Fausv9G5ok1ARuFDRO+945z4ULyCkGJHmnxliJCN1eDjl/ayRiX0vOQdoiCLjjEk4bcv+8L+CkZn8IOAp02ojBatXWs5NPhOo2M2eUJi17+YQxW/OFyE0Le+K6wlQqzqKTe1nZxNgbz2lSrvypep/n4hlQ5kvGTmDcS/1mPuYXORvS54qE9WpoUz0Pwg==', 0, '2025-03-09 00:56:51', 'peter', NULL, 0, 0),
(13, 5, 6, 'czlDs3HzMMyyv+/9VCD54Z/pkbFc+SIhWpepW57eEJ5qArs5xXXAWt+t2jPrlhh9X5JL334LTvCMBfRjwA2BC+MuEJCG8EsUhO3B9Mim/0iSiOdWX9yr6492tsrp74+gv2xCoTubVGs58SmdWnyYsu9I8Gr/zBM3AfcM8N08XdHx1QPNbmQmyvscPWmSWk5pkpzDeRWCzCP4txKBXRac4hLv9mUdEBAY33uG9XbkvNO47zuCBFOAyd4P902rDn+nRURy59jSXkwPGaL79NR62prMrEO6xlMrQq+JhfynROB2uQ6z3Cv3AT0WiHRJP+PehLHQyQKW2E3CElK5WpkAlw==', 0, '2025-03-09 00:57:41', '', NULL, 0, 0),
(14, 6, 5, 'TEe0JyWqGov1/zSxo77kTR8n3VyKsY9EYb74svNixFdAwpfbJ8m2mIBhMExJW8/fbQwMMsv+rXrNHjzr8N62+LaTzFUx0FuZvVN1hzQYOA99eBE3+5gtB8+fr2TuAYnnYlzOKOC4Wf6urpxxpuDJIMaSxMoOSZQ2Cbs1B7EdWgYeO1FSG0LeC6T5qkASziBQhsPvBwAgquXsXqNtsCQUYUGN/GrHFwwWK+0ktpHACAIH1FACsLxh9a9C1oDfcOjeYOa9S8Ku+YN+KL9Z49e24NoNbUd3pNZ40Exq3PYx6Gu10k2SvVqgILPKQ9T9doqcJraarIR0/ucdagE354EA5A==', 0, '2025-03-09 01:02:35', '', NULL, 0, 0),
(15, 5, 6, 'O2mddZQPXlMTIgnaS/E4BS8iMdjMrKs+AuOn7DnksgrJUiPzPZdgpvbT4+5JjN2IJC/lbzdkEwiHbdSvSnHI8S+l+6C7e0ySrQ0iMNISPYnnfGTUu7v9u2CagMzGXZ4oMqjPm8blP8+9if0PTSldyodcscEqhCq8hC7oa0lnv4Ht0oRFvHlwYQZeOHeAqGOyv3A3mT9fFgHG/6K+VfHf7bMGKitIdDYVM8eRwGdrGAmKip822vkEh6cWm4D3mNKKpKoSl/+s7YxzYgEVcNXrHjdqVq42IDHiCgW0oUaZyaQLuV7w2RWYZC2zTbW1ZVOcrhwa2JLO6a+5w1OMj8F7ZQ==', 0, '2025-03-09 08:20:04', 'Man you know Natasha', NULL, 0, 0),
(16, 6, 5, 'Mu7Y4/tJL0theJvDGG5cj5xgKLfi2JXObToJYHP9Egihi7HlLoG3cWmiQsE/a6mK+U9wuObgPzB9aVSohoUh1QcG3Vjk/Pxcule7X05GFCJTl1TfL/azIlQXJ1XB/UhRY7bLi7uCH1chvAO2ytDwQ548AA6FcC3WLFh0jOmyAqD6K4tXGI44l213Z29DRIAdTeU5wuXTMh2X+HW1MnnHfIFC5fKiiPTGTczQmrY6NByoJuavstoyGNywZwqBaYfwhTDE8zfYCPI711phhtRBIgc36g8kuvj1sgG+M1mgv2FQCFsOM/t/y0Y4fZIvNOF8H+EqP2I3a37963PeILFArA==', 0, '2025-03-09 08:21:15', 'Yeah Man that hole', NULL, 0, 0),
(17, 7, 6, 'jamoAoWgAY40ZdtN7NldweN21tX0rkfRSH41hJ4WgKtGbuU2KUv5xnE+rVjmEBsdUEH+u4ZhoUFykI75DkAgiWq6ZyrB++ilicaGJh8A3ddCkpBpVLgK9x8pG7LS8r3OspaLKoOZ4iamziPcd6Bae2ZqhyGnF6CBPK1tpaRL6hR+0l78MW9+C1JEhPmPHOiboRMDAd7/o+4EzZUlvrgnq3/vXuJO+AXzrO7dCmjyi3UnoKi1cBajB62pp1sFxmF3Qn4vBaQudcEQL/cQSIwrjHBnz8nK5C6BGGNR/W3NNjTM/pIS4t/arzLwwHbLEwZkU/ROpQs7KqrOeQcIhcMohw==', 0, '2025-03-09 10:04:37', 'Am with Mercy', NULL, 0, 0),
(18, 6, 7, 'GBpd+JrJ4ufCAPuhl4HSux+L6pO9XWNYAdE1krm/5eUjKpxSj1gJo3So/VH8CIo5L9Zynmof9pFpTDH/ko07g9E1gE8alzSYh7M6en2f/lL49aEZjebUQcZUu6i9hXkhGQc7GxEVW2SoZ5yNeG6+LfatfdYdQR6o1e4uRe7PmkUtK6pwf+KYh8AIIg6yiEf+ENu/8vcQbIvUL06nhGP56hOVBbKJkYijaiD+EN11uGdmcGP6kei8ewmatB36TGOMJCMhizpuTEEsWJbAN6S4qVVHdxKUtLn91b6E37/22UvpMG99o7D4Vt7Z4f6195RjeJ72LX2sr/Eux2MCkV3E2Q==', 0, '2025-03-09 10:05:17', 'okay, you guys are enjoying', NULL, 0, 0),
(19, 5, 7, 'G2XAjKvch5ctl0I10SESXea+ql9pktEoo9S1aWL2pgqa/AVtJgMZhK6rQK7yMhzbDTA/tLEcLzTGJmpyf9Tl6lSOy1F2V3gz7QlwIGApzSmXzazlYq1S8P3vYDMlgNF34KJrLpIlLPmRY5J5YVE2pRY9X0FeIrJLn9Xpavv5jd7sccJaoawv8uwosN73rPeNbOIWHi5A0pRM92t3PG5UMrUQFq5w96Dxr9ZM3dhf5taJfPzRcXNl2u8VJfLJJ/ISh4bKsoMsTbbA8rQF+Vbif7xFi4UE+pSA/YMSuVdt5xyr76DhJzzdoMBgl2DJsATWOpk2rd52ed6eAptNo0h4/Q==', 0, '2025-03-09 12:10:28', 'man', NULL, 0, 0),
(20, 7, 6, 'HiJTrleLzHjgqZOh48A3wcT2OA3xkOwwRmNhM5zBv5XAqGhnv/gMkQbwQLjL1OnqQ7L1FVNbAa4l+EMu6e6/6Vy/M3/NwE1bcAsroZuQX+lNulOwqn5p//1CJlv2EuYn8nZLL4Vncrpio+Tmk3e97NQ5uugkV/fKK3sJI/zjmvUDoAw9sp6tLllVU4a+4Oa7BZVISlThCH+wzxMJggVUdhHr+OESnOmf6jiBkSYkywkYV22rfvqdrMYxZCf8IztfVyiU4chk7osRK59OYNZ5L5BW34wszBmDH4v7afC83clQLTrVJ+x3Tk4mDYd9hew3WWZx9XgkwP3ZjCZzdZjllQ==', 0, '2025-03-09 12:56:02', 'https://fe56-41-223-117-68.ngrok-free.app/Social/conversation.php?id=5', NULL, 0, 0),
(21, 7, 6, 'LCQ0zIQLVgRLrrZp0SPAeV/5YtNfeerxSj0w224kNtvudUvdRHKgq7kKroaANeciO4ao/jJJI2lCuSEJ9AWq+BEKjLz+etxVMdRU2VnP89KEW+TLvQE0ZJChXCzWTG9XlzfemMzH0NjTHebzoETFjFWYJH7NiJNvPWr/XS8lEg3PtPFM17Kj59PLd4amqMLpX8DwMC0+YyBl0/T4igFkyDpXPO7wd2AZKzajG1CQG4cAPLzHopU978iWPNegyajUKzHhjZorClm6IAWDaeBTENytvvAxzlEPwqIpapYi4YyddxUI0l33gCRjqfjrFsqw/CjTSqTjgHT/a59T+xRACw==', 0, '2025-03-09 13:13:33', '🤣', NULL, 0, 0),
(22, 6, 7, 'LxaWSXpbMsvWO7ECGO68L7A9NgOOcgiAJkuMpb8goeT4QH9QyvlAFaCMYZFCOPI0Pymd0UnLHn0JXYLDzR2GXCHrSaqgZ2XgMtJ5ykmSYKzZm6gdwpeQNksf0PixzL1n2gl8pZoXNOzdmnm7ixDpzokkfgbmXhfenPlUTRFRLFZa+4S4estic6mitrF6VTsze4MFk714reP7lOQdY8lISZzmrRkTjBqBCcIDrSbY5s5pesLMRfnK5PY4GNtr0J5h5fVxRKqvN6p6PiVGKlhvBkzxeUmq97dwlH/xP+GqlBxSYcag/XLf4lBd1xxuyFab/35+HbNJxvuWxvwzz4um7A==', 0, '2025-03-09 13:14:17', '😀😄😄😁😆😆', NULL, 0, 0),
(23, 5, 7, 're6k+cFjyXRKtBrTXtaTxPeTzLW+/57hmMT11yNkjtvsrvCwdGNBjRgjZaT0Jou65+aaL5FrpZIF4eXCI8VpmgCziIp78UYy6dxs8LF/AwwaWOjECnd/JqneuA+rvZ3jGTVaTA5q9uUwcNp4wrY2NHEREN5iRLoubVApB4hxMgT+Dg/CiRtAfyIwr52BssOV6sLvzlgYiJsRECbDwBLNno1VKoveGKJccn0x6pYUeiknv30/6298H3Hxml5mBCd7f5pdAifawbJiffswOsnVLdEGAKQ1/sZ00bMesDXPO2deObxT5nNwVvgdjGdDBkP4Oh3ln22TiVyZlhiAEF0G4A==', 0, '2025-03-09 13:25:10', 'gyg', NULL, 0, 0),
(24, 5, 7, 'YTuXMSg272V+7nvnYqpUEkgIRwz+idsMq+RTBLdOnuSqtAYdMv+aLYO3orrHxmwWhD5Uz0tMKuicQwGngFeR/Xee9KPeagDmyVAFykNZdNLzsGJEV4Qx7xKQucFK33+F30o2qYoj+FFDQVUb523IIE+N/6QQ3dMynYJEvwIJ6mBFH+EFHrB9s8+gNR/gM6Nl6mOuBeF56Rbf9lBtFUglyiZLn9fr2vsTKmuQmH0pdJP/SIaaqFwY9ZBfLS/1UHcecDUPSrlne4SGMarcCrOnLilqbq+kdJRCAk/+Z4gXRE2KTFIWy3bPRcftsDcxOa8YB7JQhzioyBmw7JouPm2Gxg==', 0, '2025-03-09 14:12:15', 'hey', NULL, 0, 0),
(25, 5, 7, 'qKyY8jpK+ogYDSQB+RIqTkED0F+W4HBpW9hzAavL5upst6CluAnbO42SKb6ACYfy9uIWOvousrPyKCTLoUEIY0ioQqSi3FgD5NLgELRi863l4QQFLHbnZbNse2XFB8F0d1R/6Bi1AAEUmhAXKbGGhw9uknT38G3AvS+GF702J3dhCHDKQ2DRr+fFcAipZgxmtwTsNgJpNVVLupXwpWZuKGbbi4eI5+S1nVUZ+Ai0C/rtW/dkDzLE9bukiPh/c1JSZBAiObMdTti/lMbCBiyawg18fqQsQbKy8eLenRZW85VIjBq4jQZBgJUUf6FS3caD5NGRuM0ulzLJkT7iLm2Y0Q==', 0, '2025-03-09 14:12:36', 'ioiuyt', NULL, 0, 0),
(26, 7, 6, 'RHnhCen3sslmNOEYEuLxdgTolHJ8YAAQIrWdFPYjeZI8c1zdKfiZ+/tf85aUlR0a4hNsfjUOJ0nDGMVGFV3DK1FPpEmF+54S4OQd21Uam7w+mrb75pu0mpzPpMe1q02dtpZCadpe7m+Xm1Gs76ak1uEc/HqRCsSpOj4YPLrJlIZrJ5QwXkEmaWjCeaQ9wqcEWxuRrTkZak+wrpq6pMS44G8OyhDeHlp+noUtesEBDhTbmjJHn8df6TdwzYJu42n1eouLIJPGXrY+dO+yKOgvrHKgVQNCHRYzBu54QiA60DFVE4j6E+35qW/zTddtVltTpkEgJTlqFyxrYhabmod37Q==', 0, '2025-03-09 21:04:02', 'yo', NULL, 0, 0),
(27, 7, 6, 'fkjp6msGLvTp3Hk+Im9OvF8MlbA2jVryHUHVaNZNuaV77xcs8UTP0VGL8ptN0W4OZVbcKUM2Thn+bu4p9I8bcGayUBV78swKNKINOEQhNGQo11/hEDIMsMlT57KFmsnCWORUsJLXihFrVpa1TmbQRO3+WQx9eu1lIszKc5JH7ZwXXDMWjw96SvPPPN+Iahmu/B7Iav42K4XYSrrVbGI/cECP+yp8+eU8hroJNc3ODN3FmnUYYfZxc+JWUe/keCUbQVxEPK/vfhyhFVgEkkgzSx2GJS8hyHw+Nb4rc7CtXiiNGWlOMwRIHtKyr8SgvextNG8iCjsqd2VRn2LZOICAOg==', 0, '2025-03-09 21:04:19', 'peter', NULL, 0, 0),
(28, 6, 7, 'liVRl10R7txwMOfyOiUt1A04ifPg7vi58p3DbubP6bC6IwB51U1dy/V9Q/ii0CzR0UL/Ovi3RDTrhOU1ceecBiZIRZFczM5NWTUZiJ5jjpBNeR1O77btBXf1R2lkz6uW0qGQC04jVm4xj1eQuv5tTczDYlBc1VHquHMvjws/p53a5ScdjhB2ia3uOgux2KHiOiasaldvm01xdKRlRPIhrA4pbSWPyl87xv9OvrXKvB/9aLED3ys1bT3qRYNZ4cIU15Meqp8c0lueQNaJFCeTvuHzzOlRCdoYrb7VKScakLjXIOVPARUrSUNuV0K9+/uxetrghmwhFCFOtp/x4e9Lcg==', 0, '2025-03-10 04:25:02', 'Yo man', NULL, 0, 0),
(29, 6, 7, 'AWqWHKujRG5yB6Iozg7AnZPxJKeM6Ub0DUxlcm9ZyU5NVfwqKmk83y60DXx2Y/n71q7qgbZXjZQ/aZlEpABjsTp2f2F0xU6h3O9CY/SOZrOn3VftBjv1PFCN6GXgYbcqznZjZGlyaH35vq2cuda2a6eme+Mui70GFaeR6SdGOYndWU7y8vcmFTCl49dkBT8wM7AFHoeS1EB/AsIGpIlx/GtukJyLZvhQrGlqJCa0PIzsd6Aq+i+OhJ0D7j60UVo/y9joJYG4nhIpKWk4ftRnp/4xk2Umi46gmEedvRrhbmFF3OwH1hlFvu5w4sKaA/Hd3D1mm5ZvVInTmazt/xC/gQ==', 0, '2025-03-10 09:43:21', 'Wassup', NULL, 0, 0),
(30, 7, 6, 'DIInmfjdNZUHfM/uxafrkWWLMpHa4KNgFxR/+aj6AIiNe8ThtB2O1Xd1QWReBKnDqhUXtfvAYAJ7veTr9O+Fc726ouwFeBGFdv/d6IASx3YRcHyrSIAnE+TQnlkNR5rirGmfdiBVh7LNtc6yz3Qdji65OMTX/6156YSA+jlsA0OsK8oWW9h+bSyIiUgXpVCDtWUTW5P5oCXeO3OYtT93bRGieOdZSqDheMfrmcR4w79TVVCEshts6tZkbZU4ItJOIrZVMjcLLvCqc161b7XV6KEtr2l6GuVBCZ5rjmr1SoYFe9X/9dqB5mwFBvllYmjVUJP0D3kNMb3FlHbznT4FMw==', 0, '2025-03-10 09:44:17', 'am good gee', NULL, 0, 0),
(31, 6, 7, 'CtwjE+6ph2ur8tWB+Y+2ZlDkDiXSeXmvrDlrgh6sBvZ1BllW4uOYa1F8uyWVnBkQRb/9x0YWn2M1V5Wioj8t7iUgdxYOu5Xx2hPOIaQNr0w/mNh4MswKE07n/rY/TfFjazSudX83/fzwHDsD49lizl9aYWPFeVfsILYTCTRC+5Y8kMv1XD++L+0dJyaQzCHKZ8FK7JU5cGIihidfxT1AuK8eU89TJyk3MgQS7nxEr6MJi+jDNn5pDfYTWkOzE17kmwYi/U2Y4zt3VVC1t3Cuef2hIlI/bEKE04sWOFBM8rggg3fv5yXY5ilPNOGgQX4aWx7RMxX49D6v9hfWL2H6sA==', 0, '2025-03-10 12:35:28', 'Yo man', NULL, 0, 0),
(32, 6, 7, 'Q2/ApWE2Xc5FM0T6MBMq61Wu1VkmV1EypDjUAve+dmpm5HcvPyFoJPc5PZTT9n2CDehs+bspAfAj3MZrk0CK/K/z7IX2GKhzji462YhKazgMZgFhIFFnd6a3s5adbXrgcB2v3x/L/rb7MEaPh66zFRCxcHVa1VS6Ut4aWqGQpDEQyUioCIb1kD2mgKmvyEWNhW5O8h4ibX5OHamzWjgN1aEgMhbBVsCF5SYtl6pzh/cMu9raItVDzR+DmYFWpyNnR4urQn6UOMQ1VMUGACVzKtT0DlddQoV7dGzErRg1KtWtk23yymsaeCDniZ0lPXMG+jBrpHy7+hqmiOucWTOXTQ==', 0, '2025-03-10 12:35:28', 'Yo man', NULL, 0, 0),
(33, 7, 6, 'hI9ba+pw/KN7eSVUTo3xgmAreTiBfRNiCZf7Au97JFLoJP5GhF0iXKjJOeSPsyDPW+CoFH1/B5OJKnhlNzBhDYXbSOIWdNdX2/gC+xJkDRzIygjH4dE0YYnOgcbAyL8LQFwOf+bBPWvmtxbBQ8b1/jc0NLNG/7ZGQUuKQTShlmD4W+VpgSSnxZLi57EP5al46qJD16ws57Q/oE+Sav6Sw/W9NUsBgjdnlfjKZekmOUFY1Ghg7ansZfj46GVQJrnzgFxa1HLhEGIg9zG8M6jXVuRFMQb0kNBcXLYCRZzAOlwVYVomnj1aR8E9VH+VQBA06S68cpDqqDFNqfEr2ttTcg==', 0, '2025-03-10 12:35:41', 'yo', NULL, 0, 0),
(34, 6, 7, 'fra98HhbwzzASShLm5SfjVDQytf/l4X1ABpRk2zsLbb4PWOXizx8zX3aw5ELM2tZY+GWGgJc4f5SGIjTiGHvW0BFxxEln5mENZmJPKy/idrPwS7e/VGSNnZq9gsBPMI2djhjDAfngKIiu+SgNk6N8zU0eOYm4r/MkWPT07EZYcfX91bLVD6XMoVAdeovgrrvi+nKmlBGBdbOiuCpO1zafsChAgVmbXAtBMVL6Qs0br1jdFd4n2IOiLU7X8kMdlUifSgpElIL+mzwcIjDLPTijCR/TBNxk3GCh9ur08KRjO4CEWnFfNZXOw/e4VkWMLnwuV/V5YdKY6RQCOr0vZYxeg==', 0, '2025-03-10 12:41:31', 'Hey kng', NULL, 0, 0),
(35, 4, 7, 'TRaTuLipSeLo5EmOJYuEJQpXhwjyDaqy458zXOe1+g6xwY0Q/k395fRakTxt1+qyQmPImjdnu6N74WbPRIrkq2c/luDXLD6RaS/O179snnXoY0l8fKtl13HS5eK0NIKjvTPkrDUJh9mtvQnzFwgkoLKNnOI8tkRBoIcnJL+X/MZCdaN/CAcEzJbywaQJMaKdssCW/fnoGUjUkBnjevnNxtbu/5c9AKziFoXN4tCixqWh6aVsQmXd2LDQuWE+osW2iSN3hNsOWE7UtX4SK83hfGpZR5EPkwZgDTzTYD+cv780gGrptw2d5Go3HFtemEiCQmE/+Du2iahVdD+23OYfUw==', 0, '2025-03-10 13:15:24', 'hey man', NULL, 0, 0),
(36, 4, 7, 'BqTV8M3ZWbF0Pt6nV09o3pqjgnZ4Gg0x8cL5zVVD5BUuSWyQJDbDZtXA4Umya8ZGppu2pcwW2vqXkT9qi+fvYIHHe1rxoGiY9kwLk5s49e3rxnIKe2z3eRCzH149upVXdgieZtmFmqYjLu/9+OVp0meAbu21YkoZhZ1jF/0FmMWDqNUm7a5ENO5WzPWdcdnZEMra/QdsYyG89L6GVeOPiswFLlZkAaKmzKV+ryt/MWOq85jBFE6JmcSdK3eZ6zLcZc00zBEnYuTNlsCFuBwzKOLey4BhXCPapbPt65oZtGZZ3bSVJWYE179lc8T5evA8J7WQg8RZ1tEJY/cdAThbRg==', 0, '2025-03-10 13:18:13', 'peter iconic', NULL, 0, 0),
(37, 6, 5, 'WpI4ohtsEBaqBtZHcy2iXnG5ywadgkojMLi1JE7BLrFMnDjdW8Zrm7uINAg4jZzlmLtIQMN47yjdNxHGBxKR5ixwUiQ2BnduPl8lm6X2LYvHyynqAwoxEXobw6qhIl1bIvZDmlE69tQRjDJAfcx4LzaJjpnu27fOO2ZmHFopddAmkcF3bqovYuQkooM+vvpgY3dTX4x7w3s7scmM6aalZJPAqPsiAzhLuXRxBPGtcGbH95Q1MKhkQEcCCuORsSMa32fsTl1r6s4ilfRiOu0qdNjkYqRMCpFeQe8/ViTh33kMGBZdep/eAW/GyR80JtClY+rBzVS62XLySdg2YQL8BQ==', 0, '2025-03-10 13:23:44', 'Yo', NULL, 0, 0),
(38, 7, 5, 'CLTx7bO5HNewZColxoIg6rIj608MD30+T2Eo8lLLWWd9VQob9piBMP924N9xrLe97zRlaDyEKX5emWmR8uzI0cYL+eOdWtKm/+9WDCFIw7/42U12kxrHRufxg5aBmPMwbbrG4Au7CrAgHPm8lf/xYS8qgP/tAZf2xhTBwa15b8zymmRqd/7NXUewKuDfnhxEw8fXXLE+9/VeHuMlv8UIj8HmkhKPGp4/6/88a8TmEGg2l+7hWDu24Cv9DJC1KAUn2MSs2F1AQeqxAcDyjvOxsZn+JXWIQTtaSiaiEajGzAstEVEpBp1ahbW7neGWuvZN4ZfIps6NTszqmNzWiB5PNA==', 0, '2025-03-10 17:15:27', 'hey', NULL, 0, 0),
(39, 5, 7, 'r/UMVSIoLjuqt1xAz+smNLND7MnS2cVbotajZF6sbliVsrQnsfVJWK/cpH1GpwJDFxaL8y2ipG5nniRnKLapoV1RH2VN1HFbOXHZtW0Iujl+N6Zy60P1utacbrQxIdQGdcNRttOngbmhRwLfQz0Qv5mEUve55Bp53T/7bLJaBvuMMIGlz1GdU/jP+MXgiXu3QPg/DwI56JdYC1F4jBY1Vbyf6nt7p4hSv2pG1SXiTg0haFQY93Xxev+YEUHBePHUBq3FtwgIj/jyBTErqrCOBJ30qrrHhpB0E6/AWUeNOW0hJGSqLwuY4GZuqEQKaDiWNaO4AFnU0mofFrgcN6ybjw==', 0, '2025-03-10 17:16:24', 'wassup', NULL, 0, 0),
(40, 5, 6, 'a47U1Qnn10ZknV+IcRSi3Z3MoncO9ij1yoU8FSyQ485oI4F23N3ZRZFmOoUlFQWhxi2AT9S4GlyvXpnjjb6KO52m0QRwADAJRwqQAMekQil5Iu0p6DAWW6BFA7t3423v+VeaOXamKRpES+i9C/VuE3MVZkYx1VvgFdH9flqBiCwJg+3gEDwZjdIkawW+jC1pdNVTgrg8V0PpFivhB4+nfgFcuqFmpIQKgMMod1r+XixekMVtaZbCsab9B4YhfnWNAP3s5qtU5R+aFWyHX9aR1x5e2gFCOKiC4j2kytSsLx+HZTv3bhfgqSv0RxLexhfq8m1h7p6IdnoYt6zqjHGkgw==', 0, '2025-03-10 17:25:33', 'yo', NULL, 0, 0),
(41, 6, 5, 'hKDLhji8PMwFrLVdcPePekC3S7GknMZQkxQ+Xbj8RQPLrK4g/puWYn6GDCTVS6WpFK/2YxmsyvapnbrEgHspmk9suPx2juLo+ptl1EGS79ve0/9JQ5PTOWJhy6S8E+7qSNCNlyTP4HVhIp5X1LbxJ9uR6/xjF2beFmBa1+gTLS2F8yiRCxtSn3PAVz6zKQmCzOzoDHAGywmK42jDHVnzQNUF6GnRaH1iq+erXmx1lSitbvSMO/T5mS0bzQwt0yE+rX/mR2RZPWuBATD8YP5jcYZzQ/yx+MjnHfMHAUa9J37cA0ze3LSRKlIa8jEfdlJPPsA/7IHgx+q+qSdwenZl+w==', 0, '2025-03-10 17:26:06', 'i can hear man', NULL, 0, 0),
(42, 6, 7, 'b4i2GxWAX+gbuJsjrv5VPW4Ifa8Vka90iP3smtfJxUpOJ7ZYfFwq0uWMcFZUbZ8gp5sNqJtxZFI800Bebl36G2a6X0/ZfR+sjsifi5v/B/b/7AHM9Y1JMambwQmapjKl349L49CqkwqEg0cQEF8S1RyCld+xJm72fbGlI3r3s4O0hylWyAzHOGi9oB67qdQVB32R9tBLb0JIEXm2hQq25wPiRlePxQVuidb8t+lDASyS+ha7ol+aVfk5SHMh5ZMZdxSgITG9m5M1V4uuflvjv+5nyKPBYKKXVBPgq2oiwkA3c8KkwPCIRVH1V0UF+bFYnMySl84zLxnISguGBqWpFA==', 0, '2025-03-10 18:05:32', 'yo man', NULL, 0, 0),
(43, 1, 6, 'iCDgiIwCzWLyyLdXts1L5m4J0tqdaAagNC8dnqAWfKCFUxewnRcI/jdvNwNcbOSEVf2O6vEsrwfUdlBewKXD0xlpfKpV7/djTIOAAxvCetW12P1QATw6JTdRXP42TbWyf9qoiPiHWHKS8dOcazvGJgpjiYiVPr3hIL61gBBa5o1kPhxCYUREzowfucOgOvCd9JJN4jEn6VYUznVZh1CjUrtPFQmtndKK5P7FL65C0s8aaHK19ZSR90u2Uq/EG2Gc3MIq6H+d+99pNZkqzgYSm0AH7OLgc26g9mR8pEJAmT/86T299vS5TTEviPCM4WWcyzsitbc8QL2ciLXqQY/7bA==', 0, '2025-03-14 10:57:56', 'peter', NULL, 0, 0),
(44, 8, 6, 'SCnbbhuLN+Q8l2SnfITz+KmE1DV4xNvj69uZ/RtznD9ND+YjbTJdB3FU3Sy1EOn/VC42W6kI/TG204CTdm8CPL0gek5sHu6e/3tmatb9eywqtLlixSFv7dAl7/tivyKDxlprrGWxU4P014i6qniPot2eTHGOI9z34VWNcsyiz/231PJjcK36zr0ukyK8aFQx2+io+Pmq9jLJ5kMmOYm3w/gCXbW40Pf5Dh7HGP6K/RxHuCncDD5AZhQGiND9S4aGKYOHzLWD186SLxuf+wCZOrvRwVgKZkhVD7KLYMgH8SNETh8/B2XSxwe/MRpvUGPUMMF24Dj4I0QKG5N1hP674Q==', 0, '2025-03-14 11:19:26', 'man', NULL, 0, 0),
(45, 6, 8, 'BlIdhKqtLaM8NsUlmipXq8GitpJyk7smlJKzp0e1LDSMWOMO8w5rDu0Uvw2dJkWG3spmEHBGD/feJzXK4/nf89CJYIbdoy5ia/x7f8kON0Enev4M7DwCyy+zjSpS7P8qNwi1H9W5gZBPZHQfZ4jJt4jk8AENACkMAbOil5ul7GicdVEskax0agNiZlhcol6Rw7myx666+s4OdQ3WY1CWOtepp50gOvWTobOHlRIEw7sjiFaU479jQKUULz/FNxX3x6HlVZ9JcBVSR5dQ31KNo+SQe+Jp63Yfy1ZnnVV5QqpMxqGFB+AO3rWm01gHyYLxvpaLQdmUKSFp8bDbMpk9Ww==', 0, '2025-03-14 11:19:37', 'Yo', NULL, 0, 0),
(46, 7, 9, 'Bt22Blz12ICepY8NuJbOOOK1ppedZZztuqeQp/KZZMl/SNxLQZGYNy6P5UlWTUDLdXypJF7hprdPMBOzSM7M4sZSV1UTuNyoBs1KzskiED+bKZ04K4PGhTZqHE+3FgphKWYIKtorvgas7PKI5Wsls9UWLopizuRAB0G3B4mOzOOpi+HtxI4m7b1XUBubGNmbpAD2Chx+CqymKUFsWjNk0U+3ksNuJPnr+O3QX0CURH/IYX2Yt3H9M6vsDpFm9jKNy6dwG+EOWGbovp+wQH36DVjLiZVFoXjf6Is01lwmT0BgtZ7LNw8eMpV302IHxPaFxcPcmid2KnOmxEF+P+Q0jA==', 0, '2025-03-21 12:10:10', 'hey', NULL, 0, 0),
(47, 9, 7, 'm2FW5oI9Y6fzGryKKu/TgR51KZrMxwgfIP7J/lMEDgPgnlox7Lv/pzeH0Imhm8YrpJ/9BKmhMNsNQpemtl84YX45uJtIjpnWFjAFAaBYnJXYY7LE3U5vzj3JHbUq2ex9MOXnMxb4ncQUgHvaP0814QJ05mHWbelfi6VzamSRegqndL4TVRyYg8JjMmkN9Ba73Bz1zVHHIR99/mnzdU2tzoN8CrCMZ+RCkgbAmH57OEaIdqpOhyTWnLUVNtmJ10D/nHRhoK3SakRTGVQEi2nFYmVdTX+gUYq1jXwdBmG/tYYvcssf8gA0WatNtSgNqoMd3HpZqJc5S0WF08rXuPIiSw==', 0, '2025-03-21 12:10:43', 'Hello', NULL, 0, 0),
(48, 1, 6, 'hUfyShatdDSH0PhOFeh2//xDhauzFzRtPGR+W/xow2bt9Co4Un3731/D/GUYH4E1GPVFgTA6OlrpW4/cE5mMvCz/FW+G/C3pdJtUN8biZi645Cw9GTKHoBgQzhY87hL+n7luKvNY83cDoARhjkM1RF9mTRgOG3uNMc9JAOs8PPbS2XVu2QmTm4qrqC1/U6YVd71bET90Ivxzwg5sThuLub5IhO01O1tiRuPiGY9uTu8rcVj/MqaGqj/rUhxgYpLK7SFHehZxk0aJZcxsuWD8PP1lAHHoz63Ww1MNb+MAIYBjFjJsI2hAuOnjsE1veMm4jf1Zp04Op09HcQwvYoWV7w==', 0, '2025-04-01 18:17:18', 'iconic', NULL, 0, 0),
(49, 13, 6, 'gpbKsf7/v14+Rjsoc0hzYeOkwVYsTT957QZswcB9pd9kolrslZAzvNkSoWSuQyCEAP1zBr5SGhNQViNWm7UXpmQmZi8oRj1CaE4hmB68ThjXfF1mVTC5ttYQmc8AQPfXxPSc1O0NcHqDq3/RvYVfMbxNvVuAgz+R9J7+f25mqQUhrpo8PVJCGjuS8/Vl4aVMFDrRIrFCPKBmwywZdDfYrH6Hl4T2d2qR11kwatfQ/64nk0zz69eMtYUfEJQoojBQKCijWiwoN+mwj+1Gypu/1EYbywkdHdrfovgAdJeo1tay5anV15PRLVj04kOgYKNZzkNlgAKxge/xHmiQfnLo/g==', 0, '2025-04-24 17:23:57', 'Hello Bro', NULL, 0, 0),
(50, 6, 13, 'ZdBaVE9al7Lx1rspSDmI4M1rpxw1rWrGnDR6bzJvxGI6GozfDyZ5W+g2yUt9YNhwDdws5LzUXFNS83WIN1+NkWJKSSQPzTNp53OLv+OjHPsU211wgV+qa4LImQxUYqPk3V6cf25m4GA8Kt4A7Q36fa1DsXxJZ42EGAgoCIjdDcE3kSwpQqrHqVLYngw+vp4cdWLBOxkFQA2m5xfX+20OXZvmR4Vusl960tcfzzkWfQ8Zr34ZPS22Uc+QbWA8UHaH6yfJDtLBZhXT+BwwI5TpIHCRjtUNF4J52mYcESPkbCdftZdmFr0ouEsI/fNkFwmYueNzGMdrDCO8NutBkNbJ/w==', 0, '2025-04-24 17:24:34', 'wassup bro', NULL, 0, 0),
(51, 13, 6, 'YDtCVnE5CRfmZAXmVsZR1aD7+PxP1knIAjLoRDandm1AAiInriO42mwzoewy9NSzagzAOsfyZfKdIIIACBgMoFZDiE0QqDdxrzAYsJYq8mu1MXa+FKFfsR9Wlj1mEk0LcEEk4WLYgcRbYjcuzZOCCvi0+JbLewntelr3nwT+SG9rJcq5pBSxJA3DGdQA+9Zo4MQFRX2Pl+ZjRblZ1x/ngjSC3OXsgSgugacuueEgDQmhjiR4IjWBYHSFwmyHXE1cbaS289vOB3lwP3tpgXFjkpbMPKl7qiYWK8AWj5P3Eun9q1Kjtaus4H0OfhDDyzVcbrgQnWAzXD9YYR7+3AK6zA==', 0, '2025-04-24 17:25:05', 'Good how about you', NULL, 0, 0),
(52, 6, 13, 'fg7DibA9Z2bhPg4fdS2cyGwsPoJ/hLA3Zr6EZbGOlGupsPBsCkRBvHDN0MrHBqrBrCDSFwj3j32dfqQeGqr/NdR/2Gn+/7o55QtSLgl8wKw9KNisK93K8U3C6U0jci1+FGObe87RhcelNc9eQqYpuyBkMdXR6Zb/LKHgwXyaU99m8SFdPA8ZK4kfCgXKVkxG+ZM87jcA5iPm+vhhd9mnBjDnPrzeA4ijprj4o+PUY44aSXH5lafGUv7++KMQIAYsOGPx0iqRuOphtBYDp0luK1ErXpt05/mKZonIaG4UUobeDdlTOt04GvedogZp7nxcilktBD2ow87De3r1K24Y0g==', 0, '2025-04-24 17:25:40', 'am laka????????', NULL, 0, 0),
(53, 13, 6, 'N+HZ8yhtsYeYaFs7Fb8m+s2Lw5aQtUrkbbIAnQ6Q2PXos7/DvEdDwG8UpW/JqnNu9kAD7BagVv+W5EL9K2M2aSdDy/NLMESQO6sCVPl/BDIy6zVEvt4VPO6eXzoaf8hYW91RokPJTrBcpiIryQ6kdgx3LQAahjScC76YaHqlvpfgdRj9Psb0ZgM05g7flMtI0KaDclB/SK2xqhQEXKsWjFIM4QaCeb9c7Q3CFU+6bbRjBblpTtyUEmqnirwlJMExOIAeyayaANc754tmQ2u+HjnF9lNzkCZs3UwdM1ZAjeklH7hwEROLMMDS9ohAxeI7Xl1Kjb2NVFMibPf5bDF76w==', 0, '2025-04-24 17:26:04', 'Alright ????????????', NULL, 0, 0),
(54, 1, 6, 'WBpaD3AbatEeP4JuTvV+WmegGAWKl2bAyWxebJvuUSYBZvIRr9KoCrYV/GcSkrhPHYFQM9SCr0xQ5KlvYx8zUOXMUQfauUMUECK1Dl7Cz9/kVRxnZisLVaJ1BJI4JNcOaHWNQxK4vOzdZdtnmjoCNbli3bfOGELJQpJem6ifykVXV845wiZZrxZBDmink3L/uiSomojRJLmvF7BFxNB4i2V9OyNHHt9Jnfe6HxG/yoyQ3QF3vBV1bw34lGN2r1Jrkd2W+6GdNDBCmvIHiZgCHPaKeGLwIOBlsd0/HLaNfswE3LiomEQxITO9m2IxgXhMwsasqWJtYJ6ZgNF+bVAd3Q==', 0, '2025-04-28 17:21:13', 'yo', NULL, 0, 0),
(55, 6, 14, '1UXzYtUNhHR0BIR8bkDJ1aLYlsrVa4W9Rlz3OxnEkSmJ75GJFAeeFacfNCY96yDM3eRbSbKMPF/LfjUYrb4bxEA1cvKT1cX+gE0SRmQv9Jgtu7C1jkLok09QJevzrx052KajNnBKAY9HlIW8kPQsB13ZYmGrq45tl8IlOsKvleUckVrlLG/SzQTwLaOs540dT7B4VoLS9bRRDhyY2TrcQ8tkygY7SncGWYj0RkdRkJg2Kh4e0uIyjflrXQfUdo76uabVUvBBpdFT31FUYZK8WQgUYdbbla4F5fxmTBU2ySciJpSOG+/vKDTACLtvICBp3pY/RN/d8+Up+JSAOXhg+w==', 0, '2025-04-28 17:58:52', 'Hey', NULL, 0, 0),
(56, 14, 6, 'kLBHeA/L328oSTwb0zWdW/PX+KPdgcEBX+LqqRKSaeIqE4pZB+vCwNL5PyM51Xev2sfqBAWVp/wA4LE+vK2mPCm2cxO9BepB3TZ+IvGNmniHiIclgsLOXP7qdzUDmVI+ViFEkAm113SNxgfdIZ6pKFUCYpfRYfnl/cMxRegNeAaeB1s1zswdtIOQpeC2BsdlqWkZveLP2V7UvO35i/H5HqRFQL3wvFGT+LEearDqrTglUdBLTwW0UTmoa6VCTqOcV9I1cRzKr7ErqxMr9TOJ1wBCz+xezqKoptaNWv0yhA9+nSfE3DYw5ZfkE31TrJ9pMWM/nXuu3VCaNoUDeHCuTw==', 0, '2025-04-28 17:59:38', 'wassp dawg', NULL, 0, 0),
(57, 14, 6, 'EULWTwQqxvF+Ropgol128VeSEKCZ9b0/8qXL0/xsU7Z3NQenVjDIPp+dtUCNaSm9o8uLnJHY1exbkOGgLEzSF3y+yk1I6StYEzfQY4LElGk5mayahRlyW7DeXVwscgrsWeq9lmhsdQZ74tt6HS7U9EDoHHijhgwxBx9Y/FEddHqDBWoq0d3D5FBGVs+x/ziefRjncmGDkxcLI3l/Hdo5phdS8VI40y85aP+O/mKpYM65DVHS7vHUo5vNKf4ZyIrqhrNOxp3BAOeLQioEXeP3a5MLtSBoEFCIndKW/muMaIYvAirOAf+QFnPBAb35dEO4Rq2PMV2TYJ2sb54OVk3HVg==', 0, '2025-04-28 17:59:49', '????????', NULL, 0, 0),
(58, 6, 14, 'zRFOJsm76b5Huk+O3cotLdEWamf3yaQUSX+VHAHvtplelqhkjMZTdI00XDBYaDEtimsw1FLYqUlXa+gs0b9GqnkEqaCc82pcynZEE2Yq/hMw/vTBKeeIrPTQMlVIEfBkqxjkvGjsG1lXbb560T7w7uBULFACd1qUKq/Sy/YW/trqli+CiKdkk+4/X9jskzr3bxewgGvaamdtGTyDWLqNPKJXN996eYrEzHlI1654QrI6+sIHfP4E9usiI1RjffyWuAP0uF5DdJatGWI5TVdycHOaaqPGBN+79Yk4zFbkrJf3FoE6PxtB0cDX/uDgY2JOXCeCSthpdSWf5rElqoAB7w==', 0, '2025-04-28 18:00:01', '????????????', NULL, 0, 0),
(59, 14, 6, 'Ou0a4EyltW8q+G0i2H24gr+ewbEmVq3UgmY+Uzc9rvVXVVzv4QA1z0Rw8TM/FkFQpIeKsv3SBO+WNn5qIUUPt06He7f7GjwT8av0wgwmtuCVezpdHi+ifkGmw79Xy4ym/WCHL0nF6DwQOP2r30pBcG63cqsXiEI1LEEC+rrykc4Kwql+OPKtvZBE3xh62mK/4w3fAS09xVFXEBKzrJod+5kbS6i31zcG1qXSdDrMsYMFPjL+jf3RIdRlIQeRg0Ri2RLnGcXzu9okyHPlmK3VZrTuCdBK997nhZ00D0aJuuHlcaTS5uFbSxuFfYj1g6zTS0DpkDBc9SoZwxks8cFpmQ==', 0, '2025-04-28 19:16:59', 'yo', NULL, 0, 0),
(60, 1, 6, 'UsB5dMDazpAydkN/zHw2vMTJ/uudkOLrHQW2ZnqcZJGcXAKf1PbzX53xc045lwsGMFif/WUPwjNYNz1UR6d+qfQP5FOLp9heF8JScHfVPaFeJhUJWV1jCsN3nQQb/HT3TOKp9WfPEwhQTGHh9YGrpucP0yZ87i588iAP7LYVQ9WLCY46mSl7ORZZINKV1SUQtu3vGrsCC9hzFMB9OkKPadbb6C4qrcjunZiagua9GKFLU9GC9mscc1HzWHd6SVZJc+tXXb7mHpalz1mIx3Et9k7MdAdcXuWAULJeCxIb2hUbFbXYK7MWpSyfxyFzF1XsWdL9toGntvTkmCXMEu2MDA==', 0, '2025-05-22 16:40:53', 'Dawg', NULL, 0, 0),
(61, 14, 16, 'n33AY294L4/VW3m8+okzgmmQhXAbEPTKe37IQnanGBbUmHwAkc2ihJxISqzGoD/Om5FHaggFSLDrbNvry33njFk8YUo4Jvs2JSSqGO6imr4S0ESMMY5rRY8Z8MratFzrAM7Zu/KMzEQB1SHQXTO14MS68zoN1iaq5dTH09Vjh1mjY0T7LVsnNRGu6XHYd9wDk2ugJpC2XC0yPnWfycwF19BBS4oIK039OdffZPsH8BwRe37sxhwIG9blFHpT997KUpTgCEfin9iWUE4nSHwROkPM7QzICFAgyW5CbMsvrDkqAy+llR9ZrwPBM1ajWgmZGvNHGG6Vaykj+e92ETDPpA==', 0, '2025-06-07 16:29:43', 'hi', NULL, 0, 0),
(62, 16, 14, 'lDHWUGRAcuiUveYelQYxW4++r7T0gIkNri+hoePilE0IcYq00h5VEMCjQoi/wGayP9HJyecU4ZYLI7yZnLUyj7cKV9GVSktKnZpG/bN60G/lxAb9R3NjSbF+piir2U7y681nRa1zwyisEzsO+JHZoDYDqEM0Fz0OV8a+SUouWeMNggcFOmGfylO01Yu0b+440Tq2hfLNOri14PwPm146mJ0ljp/W7cYF+XinSfaZXBcMK5jMeIdkybEMGbr3944+iIRB9F8BXt13LVGyejhBv1f6ZXgwaVSdOdWgiPc5gwrx8ud0+nV1iZR/N3Rdw1PEG9Ue47uLkD336lQIz8yhVg==', 0, '2025-06-07 16:30:38', 'Hy', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 5, 'Someone reacted to your post with a like.', 1, '2025-03-10 17:02:50'),
(2, 5, 'Someone reacted to your post with a like.', 1, '2025-03-10 17:04:35'),
(3, 5, 'Someone commented on your post: so what...', 1, '2025-03-10 17:05:18'),
(4, 7, 'Someone replied to your comment: get out...', 1, '2025-03-10 17:13:40'),
(5, 5, 'Someone reacted to your post with a like.', 1, '2025-03-10 17:19:45'),
(6, 1, 'mike reacted to your post with a like.', 1, '2025-03-12 10:27:30'),
(7, 2, 'mike reacted to your post with a like.', 0, '2025-03-12 16:15:56'),
(8, 6, 'mike reacted to your post with a like.', 1, '2025-03-12 19:45:51'),
(9, 6, 'mike reacted to your post with a like.', 1, '2025-03-13 16:04:02'),
(10, 6, 'mike commented on your post: Peter ...', 1, '2025-03-13 16:04:19'),
(11, 6, 'admin replied to your comment: today...', 1, '2025-03-15 10:27:31'),
(12, 6, 'admin reacted to your post with a like.', 1, '2025-03-15 10:27:38'),
(13, 4, 'admin reacted to your post with a like.', 1, '2025-03-15 10:28:50'),
(14, 4, 'mike reacted to your post with a like.', 1, '2025-03-15 10:29:20'),
(15, 4, 'admin reacted to your post with a like.', 1, '2025-03-15 10:35:51'),
(16, 4, 'admin reacted to your post with a like.', 1, '2025-03-15 10:35:56'),
(17, 4, 'admin reacted to your post with a like.', 1, '2025-03-15 10:36:01'),
(18, 6, 'mike reacted to your post with a like.', 1, '2025-03-15 10:37:50'),
(19, 4, 'admin reacted to your post with a like.', 1, '2025-03-15 10:57:31'),
(20, 4, 'admin commented on your post: man...', 1, '2025-03-15 10:57:48'),
(21, 6, 'username reacted to your post with a like.', 1, '2025-04-03 13:09:55'),
(22, 6, 'username commented on your post: wassup...', 1, '2025-04-03 16:16:49'),
(23, 6, 'username reposted your post', 1, '2025-04-03 16:17:14'),
(24, 6, 'username reposted your post', 1, '2025-04-03 16:43:04'),
(25, 6, 'username commented on your post: yes\r\n...', 1, '2025-04-03 16:43:35'),
(26, 10, 'username commented on your post: Yo bro\r\n...', 1, '2025-04-03 16:47:45'),
(27, 13, 'mike commented on your post: hey...', 1, '2025-04-24 17:31:36'),
(28, 6, 'Chansa replied to your comment: Thank Mr Mike????????...', 1, '2025-04-24 17:32:24'),
(29, 13, 'Joseph m reposted your post', 1, '2025-04-24 17:34:25'),
(30, 1, 'Peter Iconic reposted your post', 0, '2025-10-04 21:32:28'),
(31, 14, 'Peter Iconic reposted your post', 1, '2025-10-04 21:33:00'),
(32, 14, 'Peter Iconic reposted your post', 1, '2025-10-04 21:33:08'),
(33, 14, 'bwambabwenda commented on your post: bomboclat,,let me find u with my girl...', 0, '2025-10-15 14:26:32'),
(34, 14, 'bwambabwenda reposted your post', 0, '2025-10-15 14:27:36');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `original_post_id` int(11) DEFAULT NULL,
  `caption` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `content`, `created_at`, `image`, `original_post_id`, `caption`) VALUES
(1, 1, 'Am peter', '2025-03-05 15:44:21', NULL, NULL, NULL),
(2, 1, 'Today am mmood', '2025-03-07 09:50:12', 'assets/images/posts/67cac154ad6ea_IMG_20230524_161251_789.jpg', NULL, NULL),
(3, 1, 'Today am mmood', '2025-03-07 10:52:42', 'assets/images/posts/67cac154ad6ea_IMG_20230524_161251_789.jpg', 2, NULL),
(4, 1, 'Today am mmood', '2025-03-07 11:02:30', 'assets/images/posts/67cac154ad6ea_IMG_20230524_161251_789.jpg', 3, 'so what'),
(5, 2, 'am nancy', '2025-03-07 11:06:28', 'assets/images/posts/67cad3344f706_PXL_20231016_175137017.PORTRAIT.jpg', NULL, NULL),
(6, 1, 'Tonight', '2025-03-07 22:37:03', 'assets/images/posts/67cb750fa538e_IMG-20241201-WA0048.jpg', NULL, NULL),
(7, 5, 'This Friday new banga', '2025-03-10 17:02:39', NULL, NULL, NULL),
(8, 6, 'Career coming soon', '2025-03-12 19:42:51', 'assets/images/posts/67d1e3bb90119_IMG_20250220_110108.jpg', NULL, NULL),
(9, 6, 'Coming soon', '2025-03-13 12:07:23', 'assets/images/posts/67d2ca7be0269_IMG-20250311-WA0124.jpg', NULL, NULL),
(10, 4, 'cover', '2025-03-15 10:28:40', 'assets/images/posts/67d556586962b_career you-Cover.jpg', NULL, NULL),
(11, 6, 'What\'s going on', '2025-03-18 08:26:08', NULL, NULL, NULL),
(12, 10, 'Reposted', '2025-04-03 16:17:14', NULL, 9, NULL),
(13, 6, 'Again trying', '2025-04-03 16:24:11', NULL, NULL, NULL),
(14, 10, 'Reposted', '2025-04-03 16:43:04', NULL, 13, NULL),
(15, 10, 'Am Peter Iconic!!!!!!!!!!!!!!!', '2025-04-03 16:47:10', 'assets/images/posts/67eebb8eb42e9_copy.png', NULL, NULL),
(16, 13, 'Hi everyone', '2025-04-24 17:30:38', NULL, NULL, NULL),
(17, 12, 'Reposted', '2025-04-24 17:34:25', NULL, 16, NULL),
(18, 14, 'I use my app alone', '2025-10-02 17:11:20', 'assets/images/posts/68deb23880ff5_Screenshot_20251001-135313_WhatsApp.jpg', NULL, NULL),
(19, 14, 'Reposted', '2025-10-04 21:32:27', NULL, 2, NULL),
(20, 14, 'Reposted', '2025-10-04 21:33:00', NULL, 18, NULL),
(21, 14, 'Reposted', '2025-10-04 21:33:08', NULL, 18, NULL),
(22, 14, 'ksvsjdbdkdbfkdbfkjkjvhj', '2025-10-15 14:23:23', 'assets/images/posts/68efae5bb4037_20251015_140623.jpg', NULL, NULL),
(23, 17, 'Reposted', '2025-10-15 14:27:36', NULL, 22, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `post_tags`
--

CREATE TABLE `post_tags` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `tag` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pricing_plans`
--

CREATE TABLE `pricing_plans` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('impression','click','duration') NOT NULL,
  `rate` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pricing_plans`
--

INSERT INTO `pricing_plans` (`id`, `name`, `type`, `rate`, `created_at`) VALUES
(1, 'Pay-Per-Impression', 'impression', 0.01, '2025-03-06 16:20:55'),
(2, 'Pay-Per-Click', 'click', 0.10, '2025-03-06 16:20:55'),
(3, 'Flat Rate (7 Days)', 'duration', 5.00, '2025-03-06 16:20:55');

-- --------------------------------------------------------

--
-- Table structure for table `reactions`
--

CREATE TABLE `reactions` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` enum('like','dislike') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reactions`
--

INSERT INTO `reactions` (`id`, `post_id`, `user_id`, `type`, `created_at`) VALUES
(1, 1, 1, 'like', '2025-03-05 15:59:27'),
(3, 3, 1, 'like', '2025-03-07 11:14:44'),
(4, 5, 3, 'like', '2025-03-07 14:17:19'),
(5, 6, 1, 'like', '2025-03-07 22:37:18'),
(6, 2, 1, 'like', '2025-03-07 22:42:06'),
(7, 7, 5, 'like', '2025-03-10 17:02:50'),
(8, 7, 7, 'like', '2025-03-10 17:04:35'),
(9, 7, 6, 'like', '2025-03-10 17:19:45'),
(10, 4, 6, 'like', '2025-03-12 10:27:28'),
(11, 5, 6, 'like', '2025-03-12 16:15:55'),
(12, 8, 6, 'like', '2025-03-12 19:45:51'),
(15, 9, 4, 'like', '2025-03-15 10:27:38'),
(18, 10, 6, 'like', '2025-03-15 10:29:20'),
(24, 10, 4, 'like', '2025-03-15 10:57:31'),
(25, 11, 10, 'like', '2025-04-03 13:09:54'),
(28, 10, 10, 'dislike', '2025-04-03 16:17:59'),
(31, 9, 10, 'dislike', '2025-04-03 16:18:06'),
(33, 12, 6, 'like', '2025-04-03 16:18:38'),
(34, 13, 10, 'like', '2025-04-03 16:24:25'),
(35, 15, 6, 'like', '2025-04-04 13:46:20'),
(36, 16, 6, 'like', '2025-04-24 17:31:06'),
(37, 16, 12, 'like', '2025-04-24 17:34:22'),
(38, 2, 14, 'dislike', '2025-10-04 21:32:18'),
(41, 8, 14, 'dislike', '2025-10-15 14:21:09'),
(43, 22, 14, 'dislike', '2025-10-15 14:23:32');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + interval 24 hour)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stories`
--

INSERT INTO `stories` (`id`, `user_id`, `media_url`, `created_at`, `expires_at`) VALUES
(1, 1, 'assets/images/stories/67cb572f189bf_IMG_20230524_165032_995.jpg', '2025-03-07 20:29:35', '2025-03-08 20:29:35'),
(2, 1, 'assets/images/stories/67cb58996b3ff_IMG_20230524_161251_789.jpg', '2025-03-07 20:35:37', '2025-03-08 20:35:37'),
(3, 6, 'assets/images/stories/67cd890425812_IMG_20250306_153044.jpg', '2025-03-09 12:26:44', '2025-03-10 12:26:44'),
(4, 7, 'assets/images/stories/67cd8cdcb2799_IMG_20230524_164300_646.jpg', '2025-03-09 12:43:08', '2025-03-10 12:43:08'),
(5, 6, 'assets/images/stories/67d1e2678801f_IMG-20250311-WA0124.jpg', '2025-03-12 19:37:11', '2025-03-13 19:37:11'),
(6, 6, 'assets/images/stories/67d1e40b69dc0_IMG-20250311-WA0146.jpg', '2025-03-12 19:44:11', '2025-03-13 19:44:11'),
(7, 6, 'assets/images/stories/67d1e4383bc70_Screenshot_20250221-225111.png', '2025-03-12 19:44:56', '2025-03-13 19:44:56'),
(8, 6, 'assets/images/stories/67d2caa799ef0_Screenshot_20250313-091233.png', '2025-03-13 12:08:07', '2025-03-14 12:08:07'),
(9, 6, 'assets/images/stories/67d92e39dc41b_Screenshot_20250314-202413.jpg', '2025-03-18 08:26:34', '2025-03-19 08:26:34'),
(10, 9, 'assets/images/stories/67dd57c2bb523_1742412174841.jpg', '2025-03-21 12:12:50', '2025-03-22 12:12:50'),
(11, 10, 'assets/images/stories/67ee88994f378_copy.png', '2025-04-03 13:09:45', '2025-04-04 13:09:45'),
(12, 12, 'assets/images/stories/680a6ff2027bd_IMG_2406.jpeg', '2025-04-24 17:08:02', '2025-04-25 17:08:02'),
(13, 16, 'assets/images/stories/6844687aed7b8_IMG-20250605-WA0060.jpg', '2025-06-07 16:27:39', '2025-06-08 16:27:39'),
(14, 14, 'assets/images/stories/68deb2645c829_Screenshot_20251001-135313_WhatsApp.jpg', '2025-10-02 17:12:04', '2025-10-03 17:12:04'),
(15, 17, 'assets/images/stories/68efaef9e9d37_20251014_124355.jpg', '2025-10-15 14:26:01', '2025-10-16 14:26:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
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
  `private_key` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `profile_picture`, `is_admin`, `last_activity`, `full_name`, `bio`, `location`, `interests`, `education`, `work`, `website`, `phone`, `public_key`, `private_key`) VALUES
(1, 'peter', 'peter@gmail.com', '$2y$10$BK9q4.SeL.sODTLZv7QDnuPPkMCyGEvckaBFiAjc7GUBk0JHuV3M2', '2025-03-05 15:11:56', '1663947292319_plus.jpg', 0, '2025-10-02 14:06:42', 'Peter Iconic', 'Zambia is my mother, Congo is my father', 'Lusaka', 'Music', 'Y3,S2', 'Student', '', '0768894862', '', ''),
(2, 'nancy', 'nancy@gmail.com', '$2y$10$zmasnKGm0jaHSn8863ISKej6/zq/QSGoDJRxboPJ.GgU6pYZpBgH6', '2025-03-05 17:39:59', 'default_profile.jpg', 0, '2025-03-07 13:32:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(3, 'lion', 'lion@gmail.com', '$2y$10$..16scBL7N5uPa93xLnZ9uZ5IS9ov9AlrBJOBdW6WEaJJjlfGPUTG', '2025-03-06 17:17:25', 'default_profile.jpg', 0, '2025-04-30 09:35:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(4, 'admin', 'admin@gmail.com', '$2y$10$En8eSveJao9c7hnAhv10eumt7P2slCBtPRHSVUihDwbnqCHX54ihq', '2025-03-08 00:21:24', 'default_profile.jpg', 1, '2025-06-07 16:21:05', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(5, 'eazy', 'eazy@gmail.com', '$2y$10$1QqAb4RmlLrM1W6IpEO0jeROHpaj/hnoszAvjoga9GPruBguqzj1S', '2025-03-08 23:18:45', 'IMG_20230524_161352_960.jpg', 0, '2025-03-10 18:20:06', '', '', '', '', '', '', '', '', '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqp7eVgpsdyJTy0no1Wzb\r\nkxyE/jZYNEI+z42Ya3yOxwcM539lG8isETVwmoY3315IHC6KefeTXeLqsrAL//8/\r\nnoqGWh3qgcPCitG+cwvcS34lQCURDth2Oz2Zvh2F/JOvlLiTOGSDelPwAbOT93kj\r\nVy1hT8SMz0+jzfWX4K7k30Cf5Fr+Lrz1G45WIrzgVAf5We8OlAraCoaNZidd/vV1\r\nAQSF9IgAcfHRsKyGY/ojm1XxITykCsDI1dHOEsX2zB852b4dmYYVzTvTxAcYhOuL\r\n58IjAn+coNeADwlRizNpZoO2MKdUvqEgMQbyBl2UrPNP2tQUa3x/Vfsk9Ag/DwSl\r\nLQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCqnt5WCmx3IlPL\r\nSejVbNuTHIT+Nlg0Qj7PjZhrfI7HBwznf2UbyKwRNXCahjffXkgcLop595Nd4uqy\r\nsAv//z+eioZaHeqBw8KK0b5zC9xLfiVAJREO2HY7PZm+HYX8k6+UuJM4ZIN6U/AB\r\ns5P3eSNXLWFPxIzPT6PN9ZfgruTfQJ/kWv4uvPUbjlYivOBUB/lZ7w6UCtoKho1m\r\nJ13+9XUBBIX0iABx8dGwrIZj+iObVfEhPKQKwMjV0c4SxfbMHznZvh2ZhhXNO9PE\r\nBxiE64vnwiMCf5yg14APCVGLM2lmg7Ywp1S+oSAxBvIGXZSs80/a1BRrfH9V+yT0\r\nCD8PBKUtAgMBAAECggEADbHVNjI9fP12HNPImrLTV/Y6zX22rDEiAf5DPONhQWPV\r\nOxKMXsPHXdU8fcamnujeIFby+fGvdCJ1xJRhTjGiXVsQiBStIS/Bgmtt3iWWT7/n\r\nBQREn3yr1rrtx2buvXRsLCN5e5YDwJKSqcxMZNlmBwYHMMQdqjkh9HLRNzFKgQm5\r\nCNuyC02vkejFf0AV+TXUhOXzBuya6HG/eHSwTwGYOjVP9mNS6aD2zAoEuoTmJJJC\r\n6OM7M61BjBkulK2qFDhi+rGhFi9E2MYPhSSF7Rl/U01EHA0/kNXQmYwsw7jn+ce9\r\n2DcTf/tZy4ypKfTbPTm4YMWy9s1zepgfqNklF1zIQQKBgQDY/1KuILN9oeSX6jjv\r\nuz2xsS/t+QKuwx8tkp0uwEmbD0inB5el6SHOZe8On2eSHSHkS2tIjjjrcD5r8/0U\r\npPg0dP12SRA9xQ7Zo44z9pccpXCtz8ZyyDOJfoKD0iAN5I8QgwKdwhM8LaBwm2xd\r\nSkK5W08sgPqQfUbP2GhoLjEIwQKBgQDJSZ2gopTa1hO4QgzRFTE5OwEZVgcq2cYo\r\nQED9JRn/Wrf0/HlZNeb/2T7oov+TA+h4rH4lEbV9xf988UgJl0U7xl+ANRR/FDaM\r\n1eA+6YkI9Xf+fw1Tvo+TK0xaBb3zGI6TELiMWf1inOXeWodCTqc255CyNkk47W54\r\nkCeTAwurbQKBgQCw+62nr6w1b3FOJg7CGGk9IFMDOPFjMGmhdc8VbmeaPGD5OkwJ\r\nWZflC2Zq+sAyf+hAlvKtfrIV9Lo0ug9UYyi9QB3p97Vza+GsyKUW5KxjBNxeJvSo\r\ncXj3T2OLuDnEmwHEadYcbUna7yvILDu56vN40mxE0/2JE2RJ6SterS35AQKBgQCD\r\n3NMDaZ6kYbvXaIWm7wApIstMgrv9SV7z/WvVqlmGnDKIrmD8nUAv+Wyp0CYndFb1\r\nvuKAfEJuG6iMfDAaAFwdlY34mk1MFrzJtE7MSAc6tDwgn7DmXJ8H5USGcN6IA11b\r\nYIfVghppYKmB6cJUINyQLlDvPnrnbTuChcU3HLanXQKBgQCno0tOmUYBA0stnG1S\r\nIrxdN0c/SacrF0BCpGMQTTsEwGXRdxs1InnucSk897/avRYUEf1aCaNVCZMSSZiS\r\nu8J+9WNgInv8k36zQdCLHQxeuimxrRZZn1VHSqkw+j+RGtIzlnyWkaBd/h8gWleK\r\n5cZ6tZwfs4prHO/IGXuMlk1LGw==\r\n-----END PRIVATE KEY-----'),
(6, 'mike', 'mike@gmail.com', '$2y$10$DaU29aKxZkDEkZXTYbyJWu/Sc/D1doWlnEkrLGs4WgYWEXkBaHyW.', '2025-03-08 23:26:09', 'IMG_20230524_164414_876.jpg', 0, '2025-06-05 16:47:08', '', '', '', '', '', '', '', '', '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkV4BkS/aPCE+YKtc6y4y\r\nMLVBSjdQFvX97q4rurPR/PGwFB2KtIE0ULwW2yboHHV926caBXuvH/2bSCyNtzHy\r\nFMkhWK4zHcHZbBgThNpn/bqR/JaJ+/NHfIzFgfGRcDjXs/lUbSi0WGNrePE2Ozfc\r\nmlxBBRzolgCbYL5C8kRrLp40UO/wyEtKSpxHokJ+ZO7jdSsajNeLIWR9DxvQz28v\r\n3ReURyYimOZu+kQlcv4n2oiuyxuFcMa4zmi6AkZodhC3Etz2Z1cD9YyHhT5H9d8A\r\n0q8J3P3ZXrk8lKtyhVEpD5NySqvyR+/mOEQvlGEUyqYHjgt4H7AIiRQfjINzBpku\r\nLQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCRXgGRL9o8IT5g\r\nq1zrLjIwtUFKN1AW9f3uriu6s9H88bAUHYq0gTRQvBbbJugcdX3bpxoFe68f/ZtI\r\nLI23MfIUySFYrjMdwdlsGBOE2mf9upH8lon780d8jMWB8ZFwONez+VRtKLRYY2t4\r\n8TY7N9yaXEEFHOiWAJtgvkLyRGsunjRQ7/DIS0pKnEeiQn5k7uN1KxqM14shZH0P\r\nG9DPby/dF5RHJiKY5m76RCVy/ifaiK7LG4VwxrjOaLoCRmh2ELcS3PZnVwP1jIeF\r\nPkf13wDSrwnc/dleuTyUq3KFUSkPk3JKq/JH7+Y4RC+UYRTKpgeOC3gfsAiJFB+M\r\ng3MGmS4tAgMBAAECggEAAyi8F3vm/f4J9T9cIFdLa3AQ+GwtzyXu7BA4bpEIW/sj\r\npLhEoqoZKTUBOSeGDVJHVy3xPJPEyUjxZjbjqIrLFEUPtrXocfnbwAPg7rbxYhv7\r\nHrZlzsYpdE054JpPmxT9KRHe0hV7n90fQYxabZyH7InbNuF7M4FVXxubaFjwzF1G\r\n6DonqgzqFdsepfwWDb9kJkQC6vudlTxtCI3G55PzJWaUKKpwtzfDp/fkR3LubXxy\r\nbPYz6Vpkwx70llMX/AREWwbQMGJyEP7HZu5fpkG8Qj0/HoUOP9QdbwFKzJ49H33S\r\nl8+t+fEaIGY3eW9f1UXQv5GJ0m6WZER99VVAOd+rAQKBgQDKkZ6vVl0UV+J1QYZQ\r\nzbf398Yk3MJfkIIeryuynCF2yrmIozqXz9x8UmFBoLyqD+IP6SwA+1PAwFDJZbNC\r\n8HQQTXEWszgWHie7boNg0caB6RQzwMjY8RUpxAPBxncqELIo+cAEM7QU8aEpWQx5\r\n3m3PuK9xJcgFCqXxHuxT/qqhLQKBgQC3td6kwu+UT1jN6RLOwsRxDl+rUeQWm4hQ\r\nRL2pkqOOI86ZagZYYjqi2ku51bkWiy3NxGO+mIi9/sa0CFf0kR2Bd21c54xbXGMQ\r\nLIT3HWBBcdmAo5zM3NY4RSIhjQLBKtZOjK6s8e72+hga64GsMlaASoA4aTqXt/gj\r\nlcU3AR7hAQKBgBsucxagBhlmuZJ2WmmZUShK7SGhJcvg/jTT2I88+BiSl1bIYGJi\r\nl/lurHI7+VTwkKsF5Mu07cYdiDmeFfHThh9x5Mzg+5OsNDSoXaSuQW7JMdbH9at2\r\nnhpenQpxcSgJ2X46FRP7RBzTV4bO1ie8Owv2gkQyh6Z1iVLTjW6v64F5AoGAMRjn\r\nDuuWMfHezvEqeJ2u+HQZ92Rka/JXRPSKJ8ar9XH2ZiPi3D4sY5epw1muJKs/q42A\r\nBtEnQnfTzQupzg/2bcJoNPshFM2lIA513sE4F2WA9pNDdbDTg6heTc8s3ElBiy6o\r\nBEqITfNa+97TAh1V5uWCTRE6eo/NPl1pnqCrCwECgYBYKo0uC7XEmf1zu1Bj0wPw\r\ns/GRkG1p6WAXULNBSUW0PilwdNuJ7uj1BgkNkI/wLIYS36qnvbPP3Rm0sdM/9de6\r\nPDmwrOUCaUn8Rhy8hbcBuBQ/9EzQZSmnsy7P/pRlApiyLxoM6uKsyFreHQ1DUk2e\r\ntWb8lCxQpgJbSwmfQTZadw==\r\n-----END PRIVATE KEY-----'),
(7, 'Kalenga', 'kalenga@gmail.com', '$2y$10$Tv6VgUHhea0E9FvOZlxt8exDRk3Vl7sxRZJZ62sILkXDPBhhq85t2', '2025-03-09 09:59:50', 'default_profile.jpg', 0, '2025-05-20 16:08:17', '', 'wonderlqnd', '', '', '', '', '', '', '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAu5V7sedn9FwMEtDlVatG\r\nK/uNQJL8n4q0G5ewEg8aBQs39icPNFjA9h6+s08GGMo/s2VATXDgTq5DRyHLyVTQ\r\nvQne59ks+n/NcjhAcn1MTBLsCZFKVPM7xjXp1Xlzfl4yUAae6pgiq+I/77z35TEP\r\noTDNSwHURiUpY2Oci3rQpPt5nkuooTUDrzj9CozTFaSj+WwkIqxz+xw/a8mT1PS7\r\nrr6/R8JmlmO4v96HC6V4bMxQuSYw034deUFg8bWhqMDuupzy3N1RERz7/HtSzLVX\r\n7+Q3YAqWw+Izl2YB7ymy3iCxuzw9LpObvxND3OKDkIC7lpejzb9M6EXPm1+4y94B\r\n3QIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC7lXux52f0XAwS\r\n0OVVq0Yr+41AkvyfirQbl7ASDxoFCzf2Jw80WMD2Hr6zTwYYyj+zZUBNcOBOrkNH\r\nIcvJVNC9Cd7n2Sz6f81yOEByfUxMEuwJkUpU8zvGNenVeXN+XjJQBp7qmCKr4j/v\r\nvPflMQ+hMM1LAdRGJSljY5yLetCk+3meS6ihNQOvOP0KjNMVpKP5bCQirHP7HD9r\r\nyZPU9Luuvr9HwmaWY7i/3ocLpXhszFC5JjDTfh15QWDxtaGowO66nPLc3VERHPv8\r\ne1LMtVfv5DdgCpbD4jOXZgHvKbLeILG7PD0uk5u/E0Pc4oOQgLuWl6PNv0zoRc+b\r\nX7jL3gHdAgMBAAECggEACgWvEa7A7ZvbY0f48M5RVfk/L0OLLsT84XqFxDk9VSan\r\ny0WD+PKDAMNcwfzHYRyxMZcHy/traJjD7HGAT1XyPx9fYvjF/+5DHkamHtfV8zyR\r\nkuNJ2ucR+wGXaDnwc0B6JK9t3y/YrmFMtDTe88ZexOh0F31WB5dlsjM4wnUB38ae\r\nZfUPUhWF0J6znyKhe4mA58NiRjPBw4lSLV2obtB2PdQ+OjugBOfubEJdP0dDZa8+\r\nWD9289IVXcMZP2c08163YBGeaPrrSKG1gzfRmsQakeGkR+XJUU4LTBv+jXkc+GTw\r\netV+LskeiZxkZ4+VD7f/PqD0hLWRjpFjzAEpCyhUywKBgQDkDMP1XG9QZUTBafSI\r\nKzSAv0ucRlcKQQLrPjwticULvSGg4xz6/6rJuT2rOiT3tty6kocSb0E7k7VePz/P\r\nwNTzmzvdzMFcEeT38y7xHSFJCwMfqvxjW9pAUmrBYE3vC6K9m1btrG3N0GvcXoc/\r\nyvDc4AZJdYR5Sdt3hZLhraQlcwKBgQDSkxGIH1BmeYxlmvJk26unk3K965yW7Mur\r\nNBudwDV+3VzsNC8bfikZaqkVT2Y00yI20GaECELKcGzBMGXtytkG8h+mJUA800eB\r\nPsFWIMa8iPZt5yHHafqG3tLF6nR/wT6J36H5QMOOMt9Cphf05iD/wBgfjQvn+Gtq\r\nAVUByr3nbwKBgHQHCqyLmxcMby74+bFOSig3LAEWyLIu4Y1O3M9OiTKvx6xT4SrT\r\nadG4reewbZ6bKzLB2ndGo6nsPRr2k0Dgm3hWQt9WjgqKEDUXRYrnh0fiknRKSp9C\r\n3IhdZnN8zCoTgXl2z4Odd0CACmDUt3t9hY7bbFdzszMCoObuzwyDjECBAoGAbwfU\r\n6q14O1BD0x9MSBn7/LQmgDXHr1zUV0V2ektq6aXW5UTuwdRX32r6FJ51Cc158OUZ\r\n6OxiK1P0RDk8xZF7tcndHkHuCSRuQ5vPXZaSs76UEYcZrIgY7Rx4jpr9Ko++Zfxg\r\n74hSlJwGVKI3Z44gQDoNfjVk3b+DA7YIGJXKZEkCgYASNpAVXWtSgUY9A63x+lE9\r\n+V/vQD8AENO3dUJ1aLtTiJE5mgg4tdnY6Aod6Q4lhOFUZvmJZ4/UmM8a5xVIAgv8\r\nRQYxMvr9es+MSN94D1HIGhtxykK0UHUGrWCB5R8uwy/K37MN4zVDQL1mEY+VWX+A\r\nXcE2gOkdkHlHkSOCsM6Dsw==\r\n-----END PRIVATE KEY-----'),
(8, 'newton@gmail.com', 'newton@gmail.com', '$2y$10$AR45xd5CaZ.YMWRNA0IqDOyQBnkThQwUr70oe146/9er9mPZzZVPu', '2025-03-14 11:16:04', 'default_profile.jpg', 0, '2025-03-14 12:04:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnqy0L/j1iuifCMKeVBB9\r\nKVkpox1a3Jud0tUoPltmujsKVfqKCxNvxBy5HXAo4uUOYpRystzfMry3g5W+EIZ1\r\no+HHrnCivKuStyA/VvSsTjZfz5iOM7Q2w0cMvYQPb8rEhLtSEpuoHRKpR8gLTmYi\r\nqlp1bje158788ojBIq1zUcblnQAnwUOGOquVajfvKn/ZeUd8xs+unOTxOGnzTezw\r\n8EP68iDkoCZ38P1qopn+lN61iwXBL6o5LeAppZscHuDueTK65QJq6KWcne2K/5+5\r\nkUZ3omg6Ms9sIlJ7Ol6TUisR979wwaGWQhp6dAgHyZ5uXmh9CoJLNSC505+kK6qp\r\nTwIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCerLQv+PWK6J8I\r\nwp5UEH0pWSmjHVrcm53S1Sg+W2a6OwpV+ooLE2/EHLkdcCji5Q5ilHKy3N8yvLeD\r\nlb4QhnWj4ceucKK8q5K3ID9W9KxONl/PmI4ztDbDRwy9hA9vysSEu1ISm6gdEqlH\r\nyAtOZiKqWnVuN7XnzvzyiMEirXNRxuWdACfBQ4Y6q5VqN+8qf9l5R3zGz66c5PE4\r\nafNN7PDwQ/ryIOSgJnfw/Wqimf6U3rWLBcEvqjkt4Cmlmxwe4O55MrrlAmropZyd\r\n7Yr/n7mRRneiaDoyz2wiUns6XpNSKxH3v3DBoZZCGnp0CAfJnm5eaH0Kgks1ILnT\r\nn6QrqqlPAgMBAAECggEAAovbjmOGSTbhCnc/d2Wv8U05fyEckREioyLOW2w3306Z\r\n18LGZ7wY0I+N1u+ZI5Iw1psl2UH0upZo5k2T+CVR1164FINBN7OtjiiQOHLpUVrL\r\nbQQe0BL/YZRCNS5cpkFQp9KiINaEbm1n79UOSjFKAB+lTJUyfM66pDrwL00td+DD\r\nQyLI/hN+GG2/agWkKBPC8BpYTyhqQg7foDkhjWpV8JKeCc8DIJ+iT299xqbxJlyB\r\nwjsRQsX96lFhmQUINjGo3rysRELVBB3PPZ+tSm05BIRypDHnVeaq25nGqLc0YRSt\r\n/9uIMLeTiw1Ga7aJc/RPTBFLSCBeiDVuWodNrRe7VQKBgQDKeFDsuLnE1NUMMNTJ\r\n8t30r3qusaT2LKLwyDOOH9ORp3UY7q+gluFSLg4syYdIPiQ8sh6CbQ/EjRTmdB5j\r\nJj5F5iLN8H0z9d8OpbAPL/p9+IWONv/pY2wSytJ2x6qfG5Ub41uEz4FP81GCXbr0\r\nQ7vUtoN4iQmH+Tt08cT0iG1FtQKBgQDIoDTPYVC66vOQp9wtPtuBp2KfzwhxljkS\r\ngCRv50SjV1/0rVWILw68ZPOSU61USkzn6JjkTAwVq1vrovXQZ+IZ7xASHc3OguVI\r\ntbwa1WMDVWivCcM5UcK6qsOl4hlDw9NOhxq3CijHjfJnsiuXMEK8naYhoziVhqIS\r\nPMGKRjqVcwKBgFdDX+2G+FoDOQeWDEA18r/A2eltGyIWvutz/fRldzQxrmBej0dx\r\ngA+BVg6rlSjnz4pxYoDoLIGUJMvmhCpLk09hDuMfoXbBo6+Wbbk9/oJImJdg0Q9w\r\nEGZREECtcbY9lxh79zfYj207+4dqimc4wj29pBBRT9BQ9PVENpUGL8J5AoGATh4D\r\nMhRkSMOfHt4dXwiwk6VHQ45rbT7e7hMzHNdh5G/rDuxG94XLKRPtuzYVsVJU65+n\r\nmIz7z5wvaGrFZ+ZrJUnuaf9s2VIiUNNicNhRe0TQRAd7GmB5gBFpqLeGutoO9u6o\r\nOkkcY8cjcbjwPCgwdBy2STaym5YWmR9LBLofqX0CgYBe573eNMZzJUacnW0NndtD\r\nz8stIxyPFX6OHfW0SpVBb1OLH967wr4zisKJGfidP1ukDht48Pk1VdTgeViumyxF\r\n3MNFQ640jSfR1fHxYdiDp2JFAevCWEbow81Nsf74RfUiPhIOLk/33IxIWYTGvsIe\r\nftubRBtXByzPeRI/K/7E6A==\r\n-----END PRIVATE KEY-----'),
(9, 'michael', 'michaelcodes1256@gmail.com', '$2y$10$McCcnE7BfG9MX3yIF7mH9ugSLgYYxe6V/itoUBgCg1CynftFN08/e', '2025-03-21 12:01:52', 'default_profile.jpg', 0, '2025-03-21 12:12:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkaSxbkpcSXsfUR2hTKnZ\r\nEoeSao9IeUtRg/4oSjIEj73fqApPyf2lQpknNZaNJBvc+bWe5WbxbiZBBfLR2GhT\r\noVc15+U3bvLQsoS/7hI/G8NnCsriycDlPy/UjeAfH1+abojGHsdGdlS5Sx6D1jLu\r\n+UxwuyNenvM/PTCJX+y73uV/oe/IdUCimcRboU5dfZvfAEoRke8qGuC4IMGohX/G\r\ncWKKiBcW3ZhnNnxVwkLx2x8pIebVVM4tWyLhpLKKl0E2i6pzVa2JgIzY/9e/xMzj\r\nx+CkdKY+bq07G44BfRxyyBMuN0w0WT8M1GXBclBRVN7Vh6AN57t6fIjsO2lQXku4\r\nPQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCRpLFuSlxJex9R\r\nHaFMqdkSh5Jqj0h5S1GD/ihKMgSPvd+oCk/J/aVCmSc1lo0kG9z5tZ7lZvFuJkEF\r\n8tHYaFOhVzXn5Tdu8tCyhL/uEj8bw2cKyuLJwOU/L9SN4B8fX5puiMYex0Z2VLlL\r\nHoPWMu75THC7I16e8z89MIlf7Lve5X+h78h1QKKZxFuhTl19m98AShGR7yoa4Lgg\r\nwaiFf8ZxYoqIFxbdmGc2fFXCQvHbHykh5tVUzi1bIuGksoqXQTaLqnNVrYmAjNj/\r\n17/EzOPH4KR0pj5urTsbjgF9HHLIEy43TDRZPwzUZcFyUFFU3tWHoA3nu3p8iOw7\r\naVBeS7g9AgMBAAECggEAA6TfAp8uLXF88FrhLwmd4oqNVYi1u8EwbjDFAcw8FHuf\r\n9bSiV++2e5LY6gyVBfcDgaTzb5Jyjnq7DELN0NAVbZdAxEdWKnq9UYYkRjjjJtbu\r\nBFVctMcUhU71TsJxjOentcXKzTjXsLzAcRp3mnumQosQ1AHXy3rTFX0aJtIFxlcm\r\nf2vq5XXwKe78/rsPO5PzVgHKKkysKFtGnd6zAtsxqJfLcxgAWr6BJ4JhfrmdUFVz\r\nXk0yABG/s/+kQPbyy7Qi9pEc6qY1uYXjprCVrc5rjurQk4cZvgWArLqgD/aJnJKx\r\nHViwIlUgwptIbA1OfFPjfwyilrcjeoumlqCZldfwaQKBgQDCphLR4pB9fw5BWVwM\r\npetszPS0nQ1xXLNsa8jkI8qXYlYiRc5a9JmTOvbrqrnrx2PmwbyuSdVWAVCCE6H0\r\nEnVR4dLZk5nSyIbPenQQ4h8Se3t3MXCPGgVTaed0fqEVRr8mSJhERoSIIef5kkM4\r\nUxs8KckTCFniqA1aJwyrTgv8GQKBgQC/jHHac31Y1u5RR5Y1u+j5uHJB+QFXyXeT\r\nHBRhUGKh4VnqNVVtTcE3ChLwEQoDUGHjJCpC8oAJ70EQH5JwtrAT28AHZHZm8YlY\r\n234WkSsMTHQ+V+g+3X8+bBc5kowi+0AsnW75ZfB7mwcByHDucV8VnByWqn+nucQH\r\ncHuuAp+hxQKBgQCeNwtZGsXDnEDkEVRm236vIzFMldPVbzpQSJQ0DHuh7UaB+Sew\r\net1R9T27dTxGT/3+FT/ekxkbHVppQLgFgfNmqR4PyI/h6yjZAHnTN1l2VnSW/9K0\r\nHrxxfsWpxTv95VF+Nse9x2v2k0jRbXfCEpsynexY0hLtzxd7Tf5YR8oqEQKBgQCQ\r\n/TcE+txLjc0wuvYpUEZmF8Zsx5XLpFUdhmiqzJUMIa7UyHAUX4G8LqTtEIi6v3KI\r\n40wWBscCPhbKpItNRAt9zJ3LOrEg9P1YxDPp0xJ5qblno1TJmRGC54Cg9Jucsv/9\r\nhWGF6DKi7VLdd6J76lbl3ZgKN56PD98hHnSP366DXQKBgCheuP2cSDlRVzX5eUeF\r\nhDMql69IG4pPFMaPD51kO4K00g1/jLIiwy4rQSqI6OlMKZbDj20jfBTydLsMx7sF\r\nCDi2feB90scXDVd8Vue0avoRA5FJV+V0kQUhsmnxhKq6FeYAeQtPBaoaoFUwJLUX\r\n71rN5nsaIC24omVb3LePF30G\r\n-----END PRIVATE KEY-----'),
(10, 'username', 'username@gmail.com', '$2y$10$h8MLtCmppjYvBY7JBtg.FeF///URwLedoXvuBGEmHwMV/takBFq2K', '2025-04-03 13:09:08', 'default_profile.jpg', 0, '2025-04-03 17:52:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA5iYrEsyxaw+rINywl1KX\r\nXaVfhlFBhztiCYWQgXpdT6mR5DNXdqpa/DYQj+XO65zYO4O63Hq5468y4xJB+q98\r\nOWRtxUkP3g8l2NIWa3W8EiLNplDRUOCcr1kcb2EyoH2QX31hLP52OZf66DeIzv4F\r\nb+C3FN+0DK5lYKUsGfTKtwfgBVpndyK0YuZLp5ikT6lVQnEr9toEKMxB2hFqTTqT\r\nU5L8lCj5DVod53CC+uWirmZh1f9rNS5u8MGBYP8hgsvfQ+u1sGLCE78DJBnlYeQ+\r\nWG2wenTYdxjkT9NH6Drb8Ir4OH0l86tqr37wg87RPpJfxKaDmRGuOf0u/nUlCFVq\r\n9QIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDmJisSzLFrD6sg\r\n3LCXUpddpV+GUUGHO2IJhZCBel1PqZHkM1d2qlr8NhCP5c7rnNg7g7rcernjrzLj\r\nEkH6r3w5ZG3FSQ/eDyXY0hZrdbwSIs2mUNFQ4JyvWRxvYTKgfZBffWEs/nY5l/ro\r\nN4jO/gVv4LcU37QMrmVgpSwZ9Mq3B+AFWmd3IrRi5kunmKRPqVVCcSv22gQozEHa\r\nEWpNOpNTkvyUKPkNWh3ncIL65aKuZmHV/2s1Lm7wwYFg/yGCy99D67WwYsITvwMk\r\nGeVh5D5YbbB6dNh3GORP00foOtvwivg4fSXzq2qvfvCDztE+kl/EpoOZEa45/S7+\r\ndSUIVWr1AgMBAAECggEAMii65gHBIBt92S9n+E7qiOEYMNqoPKwjSX532FQ+HnTP\r\nREwxQX5Ozp2M3gPApWadVNk4okFIHriKD2WlBhj1ar+50c/C2stz3O2qr7hs6Qn3\r\nRopiGC2f0HKMKTUmlsZO7xTRF0CNLS+zoiUqVqyTrEauOCkqIUJn+1h8RFruzFVL\r\n/qcOcJdq3u1VwznRH8718IJYzrVoGGJw25+dLRzr6nUJuhjlDxeEJX1BRwZ/nKGD\r\nhulRo4r8ccEgzHyrQ+I0N5TT1AltvHci+Y8QJG2vYfuwdf9leB/4rwcMn2xDmsTS\r\nYMHU6f49ZyBuJHPRECjF5JE7JH9x/XLLRGWRMIJZgQKBgQDy8AuNLNTnvzqysGXX\r\nBEMMR2A+IxYQt24VgNfAC5qtNYZ+C9POHV6r/vdegnLdwLlXHYHJRVsWMV0FPAty\r\nrgTQtztu9v1cDz3LcvL4xFxRPMA1/b7fuE5A3qm6S4+0Va2I/xS2KvKEJiqmkuaz\r\nYnt4dEbckh45sVjlnesS46M1gQKBgQDyhhe3Er2Ko+0vBWj33BBzkQhPsNakzfZf\r\nLWGjHCQk12a6sGEw+k33seYvrkw7bdnGa+0SrhX+cTqGqcM4BunaS03pVp8uoNB3\r\nL37ntqqtkDHCxs60xGArdLdDjeScdVuVYY+WKj99yrPGFTzP2rimSrZTI3TC9m76\r\nv+OARl93dQKBgAP/7cDCRzMVk+rTOqoCmPP/zNbZDwjxbC+Qcnzn9AD8C53RpggT\r\ntbZROVNmBGwgOyzzAGsG6EwCPgzTA0E9GjkxexoFmQBGA/dwig2MdhSkUmnRJq5a\r\nQ3eP8u1tRw1qB7RktruVE28XjcY2TvQPIAdIqs4A3dyHTfZh1jf2ZX6BAoGBAJ1Y\r\nIBS3hN9o4R3rnaGJVecUhlblfSypL5mqYLkpLc31LgIbIsNa8bCs65GtvGmmKG8S\r\nYFoJNd3Bal4pz60O1zO4PZEQkO/4h6d72hLNMUivz4j14O/opBgtfiTG7zYO8d8l\r\nz7l2KYEcPKxb5PtZhQjmWgnwa/V9Wp02V4xGm/NtAoGBAI3SHBdAxBrkJdbR4B/D\r\nmBGXQj5bGx8XwV3/vQMbx9xumsKrpMJA/ISqu9gho+5Q2GORvSLWssaWurDm34GV\r\nfF1XqLq1+YaaMlBevZ6qiNAEMFyN8uS7dWaIbN34S14GRvWA8rb0wqzDrj+JQ2Tc\r\np70JvcYVzmMzH0S+0YX3R2w3\r\n-----END PRIVATE KEY-----'),
(11, 'Loans board', 'loansboard81@gmail.com', '$2y$10$u3AF1B2BP74ZxbMxoziime2uRm4Ug2EjppcmLNDRo8Uk4glWUJb4C', '2025-04-06 07:31:12', 'default_profile.jpg', 0, '2025-04-06 07:31:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAk9a5gMydaTmn9X4KHk0J\r\nih2VGATngVFrvWH/YrZTw54xjf5Bi5EfVTcqbv98yUIsNd8Drq0f/WIy+5e7fgaf\r\nnw42b5MfFP73F7vAUuRxyEvzE3sgH77s7ddxy/CDm4PiQctS9dl+tiXcbig3jTHN\r\nAz+titKLJtEMvAzRYybH/Zqf+YnnEGTcCIAZFP2OC3O0Pfn1sk5Wqlww9dbg5Ol9\r\nTQkba08RK0bV/QKtUGx0KGXkFcgV7xD1iQ4i3QZC78S1GFkl8vLJEhcTAaSTNbtf\r\nQKAel/m7/ESR+4vbZfn2zq3Vjm3D9b5JPEM4w+By08SM90LLv6a1w2iv0iXWsh6y\r\n4wIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCT1rmAzJ1pOaf1\r\nfgoeTQmKHZUYBOeBUWu9Yf9itlPDnjGN/kGLkR9VNypu/3zJQiw13wOurR/9YjL7\r\nl7t+Bp+fDjZvkx8U/vcXu8BS5HHIS/MTeyAfvuzt13HL8IObg+JBy1L12X62Jdxu\r\nKDeNMc0DP62K0osm0Qy8DNFjJsf9mp/5iecQZNwIgBkU/Y4Lc7Q9+fWyTlaqXDD1\r\n1uDk6X1NCRtrTxErRtX9Aq1QbHQoZeQVyBXvEPWJDiLdBkLvxLUYWSXy8skSFxMB\r\npJM1u19AoB6X+bv8RJH7i9tl+fbOrdWObcP1vkk8QzjD4HLTxIz3Qsu/prXDaK/S\r\nJdayHrLjAgMBAAECggEAORWU3FRlxZ8zKPopewjBEbkZfERMheUEBoTkAWAomOSo\r\n+sx9z5/SKuvZsTFqpCWuf4Ck4dpMe2DjEb3FckSpdtecklmLC2blzx66hOdbvf0B\r\nl3iX71ogDzh84sP0dWSnxUL2viJkqH2XD1vtE1Jy+Hmqj2t7upnG4ofKhBAR8eyf\r\nqurSMiJ3dKBO2QQ31y7Kw8hSuF13IM4LE5O7HppYB3vJn7kct7hfmazgwENzPL1S\r\nv/eKYyXvff47dnbecD6cFSi7sUKuZFS01HCqGVXGTxII1saSQ1U+PQNELSb/CATL\r\nE5LRx8r8BZR5JzgelGYgFn+rVvnSmkO7lASvGBwQ+QKBgQDO0ZXvHiRDIPZW51aV\r\nIi00QLZNG1aWkVp9H6XtvyfR2xniNfRFPCkcpkyPg0PHvBqUQ+FLqsySGputkbRW\r\nzAfLjx5Jgqad8Q3fk60OxcpkezjtdgXGtnXdkIHCCeAsmW+UthcMv8h5YVtrC3P9\r\n6MzhFlwACKgR6dfETSPsHKdniwKBgQC2/qSUQUEpswBIQey/FPtbJQvKc8+9fCPu\r\ntS2o9KWdm4uD7tS9cveNOH/Bva3FB3lH3ZKoogMpYRs8NzNCusZknoq8iBuSqj0C\r\nowRnu2fdpH8AccWnsO9Mi64NpAlb6LG+96E20pwQ/R5+1LiyHd76OoZl/4w2gEU+\r\n+3E8Xy4NCQKBgQC4k7IFIthoFK3lJzdNh5/iR3KrZB+l5vlkO++BPB2Um78A9PgZ\r\nJjTmvcAMQLEoO8dY1S/nsPo71oVjpWrWH+dBE2yLXI/I245vH8POMFWN0a8ftjo3\r\nezW71LEJdHjeNN2xUcVGeo63TV0iLqmJTNA0fhkDarZcrl8DcXkCyxvYOwKBgQCo\r\nN9to4beaMo9+3QNaAFqzxZFaMS31vOl3JRvtJcAc6wDMbj8oTEgejKJ8ofXcmms1\r\n0gxgclY/sqGLsB6yJ+n6UvesKd5W66GcC+kfxlnLmMyaxvtwlKjWD80XgwSLdZP3\r\nvcF4GREYWOawprGGIwl3s1ca6lilPTLqm8/TNJerqQKBgQDIMegHyzgJhqYpJjho\r\neKcaAAZ7UbVR3SxdpurW6yvldY/XHD2xQnsXWn0vnfzpRUuOROdFTxse+LVHpyZw\r\nH/yrtMOEsq18eOGkLOk9i8qBHYdJzib2wCuwAFFsfsDDSGSzf/rZgusz96R4jSSX\r\nXUPW8qx3oTTBOYu0WbKng098Eg==\r\n-----END PRIVATE KEY-----'),
(12, 'Joseph m', 'smartmba86@gmail.com', '$2y$10$NzLdqfC554Xq7utWkAULLu.a7WdWs4eZPfPu2rFE/d/1ioy5QBUUS', '2025-04-24 17:06:48', 'default_profile.jpg', 0, '2025-04-24 17:34:25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1qSZNxn3XZ10ZYU1m4As\r\n6BvqHazp0pacn0reSMeOs1IJWy70/prP0vl7WohxcprlAF7X/65R57KeypNRwzNe\r\ntu0806un6jo+tjdQBJitCz3514BRU8nOBiWUlhLYjYaUI4aiUmWoSicLDnziECph\r\nN/oZCbKa7s5QLViBO85h9hce6zvWsiY1JHo9SYxsTNUf7X8XJAkjVoY5Oq/KF6yD\r\nooPp96SgheVDNwICsW/+7AoO3MeOo7zRJCh7624Ri+0G92GGwcDeYdY52jSgm2Rp\r\neJnB5vbbsCVKHwx7BUN+nhEblwK6TLOu5cB5w0DMFoBx7C69aUQ74fQ3fHT1jefC\r\nzwIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQDWpJk3GfddnXRl\r\nhTWbgCzoG+odrOnSlpyfSt5Ix46zUglbLvT+ms/S+XtaiHFymuUAXtf/rlHnsp7K\r\nk1HDM1627TzTq6fqOj62N1AEmK0LPfnXgFFTyc4GJZSWEtiNhpQjhqJSZahKJwsO\r\nfOIQKmE3+hkJspruzlAtWIE7zmH2Fx7rO9ayJjUkej1JjGxM1R/tfxckCSNWhjk6\r\nr8oXrIOig+n3pKCF5UM3AgKxb/7sCg7cx46jvNEkKHvrbhGL7Qb3YYbBwN5h1jna\r\nNKCbZGl4mcHm9tuwJUofDHsFQ36eERuXArpMs67lwHnDQMwWgHHsLr1pRDvh9Dd8\r\ndPWN58LPAgMBAAECggEAAhyDDVG0vI0B9MWyA0zoJC2yHdyc6DS8ZwIQ3F0U1Zq8\r\nUNNOFvsQNEkT944EClaPuMiLY2PO68fhW/4a/cqRkfG/eW2PgfRUuEIkXVyLHI3d\r\ng/XVLE0ojlLsT+rw37jagtxI2HqmLySMgqCj204vaqI2gNaVmOfFTiI4EziH1NMy\r\nHMY6AHUqnXloOHEalaLzjP8WB8AN49U//3FSmHLRaxFm9PAKSKCITKpCSCQIFUX6\r\n+FTCwisogw5y4HH3UVxmW5Q9Av/6XzgrK6T8fj5JC1bhuNVMd54bqjZ6ukNelNOX\r\nC7FbTkOz0fqzKg+iS2M7GoV25vidzY0c9wE5zfzNQQKBgQD4S9EFfVjwZOHlwP4N\r\nBmS+zegU0vgLhVy+cqwFH8MoFvSVOcLayxIZdIwgeW1IiQPuLD1Dmd6kPuvx8eIL\r\ngcaOSp3FBdsuFslVIxH6+WmgRF3cq2VPAvcPLwGVGqf9BjkJbY90Fi3ovJZwW2MV\r\njqdN3IOXTREtNMQ+z20zNPEHcQKBgQDdTXqobgzooWgEM5RResa9zUTMPjynFYpw\r\ntgSMZ9z3yOrrgafW7/o9qzTpeQLjZzwJiKwC7QtwlStbjkJBm5pF2peTcWDGlcGN\r\nsPvw2+mYpD8uJfv8j5D/Ppyou3IjASQZ2/zOLFO0jMNk95DMXOJ57yUoDhhGu+ng\r\nRfjgkzrOPwKBgQDtp2PBZWCjd2vghQClZuE2386rX+Ka5GMIxqlvnh8lWBNyYX7n\r\n/EaupqcYziCTtNFWPnHFKpm3i6I6SdKaQPTTbQfldN19F8JNAuxK1D8Nb2KHvGWT\r\nYrsmhU92b7UqsbIWHA+ahUAb66XylQYZtBjepZouT02Jf1/23oMe6CVrgQKBgQDV\r\nyvERYGfmIXBMhsvlv1faq0ColAOiYwQAiUdxoTFuy9JvvC06T1IQW6LdXsO0jtoI\r\nV9ndZcWkeOePJqrJmRp8G4ZNsb2Ne8WmLfHnKXzNsvc6jQWYY9XvUDymIZhNSt/m\r\nf4kGYPpotYhatXRUifNdWvQpcxxu0UFLIi4iPvoTnwKBgF1/OU0c1NaK/F1M/ctq\r\neTNDLs+YyytZ7iabmkOZ/NwfUYtYqXH5xUrnP7Kqqwk8BxQdYdQ10Bkg9Hh9j9mM\r\nOPn3fmZVn41eq/1ln25QfXNi5SoXRkwBEEL1qxdIlQEidAhrfiQ8bfw5xhlJ5jSZ\r\nKkZ/xBY//nEGCx7uTpj430pK\r\n-----END PRIVATE KEY-----'),
(13, 'Chansa', 'chansa@gmail.com', '$2y$10$jcrf2WeWUQn9sMhrdD.3OeeBDTOEA8B1p97QekuWSpKxxfmqTxo06', '2025-04-24 17:13:27', 'default_profile.jpg', 0, '2025-04-24 17:40:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtOcigxVTV8kOqoVyh2ch\r\nM8Rfhh3gN8rkKPs9NnJ8YzMILz4ZBnruNjyRX3roCjlgWqOQVWYQD/1RINVLqMyg\r\nnDqWVqu2suyHhb1mqAA9eLVcJDUu+rItmTLOG0/HEbLrsfFJkXLe+AY16/4KThoz\r\nF0BfBYHjyQ1TtSuY0JMmWqoaMaBi1ChmxYNfkIxyUtMcrc3GCD0L868QEekzcqA/\r\nRzsXqTYZpko6nGuEVIAKwtQ1w70283aOUSjrSRXALFdIPYZR/OTMA1JhcQ2zm9oL\r\nICbzV+95FhrsSsjscfOAMh6ASwQkzfZXrFnYkcJdpM2gA4O4JXx1/o8eCVJds2mt\r\nJQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC05yKDFVNXyQ6q\r\nhXKHZyEzxF+GHeA3yuQo+z02cnxjMwgvPhkGeu42PJFfeugKOWBao5BVZhAP/VEg\r\n1UuozKCcOpZWq7ay7IeFvWaoAD14tVwkNS76si2ZMs4bT8cRsuux8UmRct74BjXr\r\n/gpOGjMXQF8FgePJDVO1K5jQkyZaqhoxoGLUKGbFg1+QjHJS0xytzcYIPQvzrxAR\r\n6TNyoD9HOxepNhmmSjqca4RUgArC1DXDvTbzdo5RKOtJFcAsV0g9hlH85MwDUmFx\r\nDbOb2gsgJvNX73kWGuxKyOxx84AyHoBLBCTN9lesWdiRwl2kzaADg7glfHX+jx4J\r\nUl2zaa0lAgMBAAECggEAB+DuI5vc6JyYzyvnMEb5TvYdO+vWrU9gjvlcMeAcEzzw\r\nfRA9Ckd10vZh1XD2pKVZkTEA8vsyNWQQsbegZy5cS1mQ3R724sWC5Hv6edQi2Tw6\r\n0lffnyr96edoyw83KgnrEnGiBNvmIpKwrAHBJKQm9/sFeGmvCD1UmTYYHnNeLwIZ\r\nJXcDwpMXSTlSjaeSE6noci7hUbFrTkAFuaCHtFv0iwXO+Xnny68ieOqZO9MuzVk2\r\nxaewKhCs9efEObDYE/XVfO1PP7mm9oy8I24LyI8LtixUKKgayODl1iXl6O9eLvYF\r\nLZzAmT8m1LRNsVo5cth/vXuSxnAf9c1yCU2jn5nPqQKBgQDzVecgwt5iDT4uknl0\r\nusjv6NRgIFwYaeVUwwq4faC48GATZKbnNMwFz0lGfcf/Hx3CQdajG2Z9xv3re+yx\r\n68d0qPMpqD/0EW+nFTjx6+IdB/kAEVLf5+f14dV/WR+dzOTrEVH6+7H+K9TGXWbP\r\nHKTZhUGmCbMQ2ITdVDq4F+dk+QKBgQC+UWfu5MseUhpLV1mrK2F05STaYplmjTBz\r\nyebnZzR+9tUY6n+FiKlyE/XxeVA9R9KxZ+TYQV/GZysOBW9k3cC8K4rpojaBx1q9\r\nZLdi2jSr3E6oXOYPdjRVXbIxfW+TtQJSJa2/5YcvSwHXG1GkL5gkJ4s2f+P/YOK8\r\nad7yS0OQjQKBgQCb2tGG8kltk/3X5olUcq5wuzgLua5DFNCGUcZ0FEL1MUDYKweb\r\n3v0uW24bcETA6zUsTu8i2VH0DZhlU6Ju8w11cyEL9W/A68oPwlAO436YCZs6p93k\r\n+6xemJ8eSf8uGyYkSZuwnbWLjpdh1kEbNsV/bRJ3Po8qowO2n1RcxTK4QQKBgQC1\r\npBJzIfBuZuPrplnRkVy5aX3L5LEN/JES4c88afbjeoeV+TFubCl01HI2XpdjdWo0\r\nobj7YSGcxZiFEFDpzu+FaHVzWLt3D6KeAkM42JPOtzxnWgrFFQcLtpo8u8BDFE47\r\nwvRaoyFr1MXT3KIF9trZHtiyUktz0K92LgF39LdufQKBgFl44QsBdBKvKltjxRKC\r\nrpnVbgsf7u6RT9wRs3FgqlPMd5HYrSJ0OfDyx/ONAnPxl+EXE17fLXP6+0mk8tcJ\r\n37W+gOH6zmz8hX7DDrEZsguog5jZFVmzpCImKPk0t5QU9IDsq4XeJnzFIsCjfN4X\r\n/tl3Smj7HNmpycGbglUrT03x\r\n-----END PRIVATE KEY-----'),
(14, 'Peter Iconic', 'petericonic@gmail.com', '$2y$10$nUpfq2AoutJD1JVOvklfHOyErXqNWHyxCAQGZ1ykT2YmA5j6ZwAuu', '2025-04-28 17:51:00', 'default_profile.jpg', 0, '2025-10-15 14:24:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA1fm0KC9ZXjkS7r7R1zuH\r\nchjKLm8hS+zBE7c0jTr37KIVW6J8PxR3gLMATfJEPR2nNuZw4mdfVRvXf+v5seMe\r\nxKsA+ioJoo0mZASBItrWGidFmqDRkYyySevsWuc4DVJlCLRBxBjiRRKmpLc04rWs\r\n8LZwHWWwU5SFbvXMp6qnrACUvzKh+Owd2e3u3R6ocXLf82SYJzQtOiLJTwaDfGbn\r\nfCNAH7cJ8AJIt7ggtkojcVrSBR2yvTt7e/BsDNVoArQBa4s8a9pqLQZKq9amCz2Q\r\nt4duqQEWIJ153JbB3+xHqeBBqRKVx7ZE2jKXrzPxq8jOY05Mgf2IuE28aVtoCXh7\r\nuwIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDV+bQoL1leORLu\r\nvtHXO4dyGMoubyFL7METtzSNOvfsohVbonw/FHeAswBN8kQ9Hac25nDiZ19VG9d/\r\n6/mx4x7EqwD6KgmijSZkBIEi2tYaJ0WaoNGRjLJJ6+xa5zgNUmUItEHEGOJFEqak\r\ntzTitazwtnAdZbBTlIVu9cynqqesAJS/MqH47B3Z7e7dHqhxct/zZJgnNC06IslP\r\nBoN8Zud8I0AftwnwAki3uCC2SiNxWtIFHbK9O3t78GwM1WgCtAFrizxr2motBkqr\r\n1qYLPZC3h26pARYgnXnclsHf7Eep4EGpEpXHtkTaMpevM/GryM5jTkyB/Yi4Tbxp\r\nW2gJeHu7AgMBAAECggEATUFgyHl//MMCkOyYeutlVc6ZgPZiwTTz6Rhmgu9dgDRt\r\nHa6myWesVe1LGNl1xdMlAm9lJnlINERfJDKg7pcgDsXnDmuLGwN3bvkpXtpAHyU6\r\nvj9+KtzvqjziE1gUJssu61uZuyF44JOBU0tKbuhFTouwIw0KAsNHAe8BOzAkZbcO\r\n+IX4qjwr1zhXCnyMmEO00ge8Pr3ehkseko1pWDSwWiwSUL0Vctr8Gt5gSalzXD37\r\nA891z9XlOu1bKxmwlDO88x5wXVQozynFSgtY9uhMzqWEnD3xdPqYT79pz6fZBTKA\r\nc/2pPe/PWi+ZcTnm89sr0y4QGHeEiKdEdVJlq514UQKBgQDs/VtK19l+JLaKPxok\r\nu5zjAf2pXN6NOFipPX7ePNefoqSotCHaY+PCyY/u72eUAdatUA5zsbAStaTKSTTu\r\n48HXpbKGiyEvVQRRnPUW3BEbzeZY3wmf7ymO1o85No9LLX7xVHXjbFbpZFH/IkMP\r\nnR3yIlvhlNhgT2aBi7uktMB/jwKBgQDnI75FRop0ZSHGFnswBu6fQMO2vhuT+XZ2\r\njzQ10P0hkAHMqas7nz4atxrELf5EgZzcsrIkSVMNXdIL6mSao7Er7WhT8km6Nnkm\r\nhY8be5XseW6tK+UB9xKejQhGOJxmb6mAjnMNPb0UVg8fLHbBfp8Xu6LRrgf1D4/S\r\nXn28uy8rFQKBgCDF3KUZ+nrGSvQNS+k+hLCj6tdL/37aBvDIj03ebhcmX8zwtCnG\r\nXI3oX94z/0fphS2Mf6MiNG4x7msG+qn6lyjiQjD29ozcHe/HW+FgZ4FH1Q3/Mg6K\r\n/V1CcKYB1IkZ7o8jFfZwWgiGlgxJSarUGHsOo1QryExxToSltTpOwLwjAoGAbXPV\r\nlAR1Z+zD84ONzg5aTPtkMlMPyCCnYjkp5hoS5CPHVl06Ar/Dru7qM9/7ugEOgMv1\r\n44z9USUOZCoYfcnqCf0gHDBtjUpiUEWKoN8C/bn7GPHiPe372Sy7sFT6at+Ripjq\r\nDmkf8lNWUdLCYZpOR/TIgzY/+mHmHx4zuH2DNokCgYEAnZ47/oEC1QGB6bkvbJ9E\r\npYzCGOeb5vOVYBgLxjk9sFFg7mX8KPt5erVMgh3uCiGJ5j1oV/7d+D1opNMGuP92\r\nOUInAAqfV3sKc3sjdNN3zypVIOk/E/cKNVd1PXE3sytC2xDw21JAcvahw0CinNXO\r\noxRtIQpluERNaT6eEF+FiD0=\r\n-----END PRIVATE KEY-----'),
(15, 'Iconic', 'iconic@gmail.com', '$2y$10$CT7tkW3X0gDWhJ/7TKVYGeIIXmQgM1sOb/pbKXRv10YQdMMozuYkm', '2025-05-22 16:52:55', 'default_profile.jpg', 0, '2025-05-22 16:53:06', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAjMZh+C1N6GKQekUYYjXe\r\n86yWJRkgbT1UhrWcullvl5orzUzybuWHvA5B3gCpuRUIhnG2C7O9BlP9/9G0pGYl\r\nVGURCsl4HVRtAzA3Wc2BTjQZbM3X62pPGYDL98VstJYxrM7AeQAWV1JfReuXEaX1\r\n5VEAGiLEOkrj7z59KxQ+DJzrLKGKJRbq+JlDhgdJpbi0JfxPhB4k7UcxbgX2OcZ6\r\ncBnxzAqYCFKmFkSzd9FHMu7rMunv2qh9g46fxVdBmEgMFfucr3N4hr4VfJ6sd+3t\r\nnVfaJ6Jf9OgIFLhhu8MYx1u5L52GTu4Pst4mR3zU7CYUwUnPTg+9itqJ7gk6FkVa\r\ntQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCMxmH4LU3oYpB6\r\nRRhiNd7zrJYlGSBtPVSGtZy6WW+XmivNTPJu5Ye8DkHeAKm5FQiGcbYLs70GU/3/\r\n0bSkZiVUZREKyXgdVG0DMDdZzYFONBlszdfrak8ZgMv3xWy0ljGszsB5ABZXUl9F\r\n65cRpfXlUQAaIsQ6SuPvPn0rFD4MnOssoYolFur4mUOGB0mluLQl/E+EHiTtRzFu\r\nBfY5xnpwGfHMCpgIUqYWRLN30Ucy7usy6e/aqH2Djp/FV0GYSAwV+5yvc3iGvhV8\r\nnqx37e2dV9onol/06AgUuGG7wxjHW7kvnYZO7g+y3iZHfNTsJhTBSc9OD72K2onu\r\nCToWRVq1AgMBAAECggEAHg4czIefooWoovItiopF13B5feIncist6LTNiVue35ci\r\n0uoiuFp46EWC+orZsZI0B8AvRcBSEw/Lotp63r6QKbyKLutkoZ5sx8l7h8jAWpn0\r\n863e83v291LwOS4FT2jhoitiHHNQUcRcYukxj6sHHLZ2dx4FFwz2LRYCUzg3Jyno\r\nW9O6G3OaxejF0p1HcBMdwpNomJY5KfUX0U6TJJuIm9bWXBpbFz6M+C+SOxxZAK3B\r\nYK6J9V3hDTOoQfFKd/j9vNfqWTGHCjX8/4Ze92YrCnqhW3fSylPSJw9FcpViazfK\r\nK1QZiasAGy1tfWxKMLEDsYpc2nVAB0FTV1ujLWxb4QKBgQC++b2qAupBFSFP6UCF\r\nWu9rfXO012dMZRZBy0vheyTdMbV0MydhU6nbrGaZbqXzg3yKcszRrcoT2JHIxK59\r\npssmVq/VE8bBTKn/mTKuIZ3jbaF85llQLTbo3BgIuQB4DaJc8TIMSNz4Pj7nL9Ea\r\n5Hp6leF6SLjKt/EnBYqr0aD9EwKBgQC8tPAP3Ww4bLK5S6GNKMgnCR2yUDr8Pij/\r\n6XPjgZ6XZoIEWX8cn2ffOJq6Qt1iFX0r8JBDIqA8Fzb+tmvxAIAo0vPTlxD5drrQ\r\njgbhQB3v0/s76ufnfTcdRARhwk5RrM2baxgafEALMaFIjeeZTGfAw6rQLPed2ONW\r\ndEu4HECqFwKBgA35Acp9mhwW6rAJFeJr69aGgD+7/t/VCZLYx/2AYAsbBvawg3IY\r\nh/X9oCgbs2KkvSj4C0pQF9fp4Yi58zZYTMbKUuUZJFKiRFHiKJYa+Y1ZSRZ1WBWI\r\nqVrSN6PzvTgLb1SzhQDnF9vF2h5aHkeEbf2oF641wed7G9bKDgJSPSAnAoGACOLE\r\nQxr8E5QavlrvWoRHGH93ZIQeou4SZCqMCHR8EUkEakwEjkZ7T6mMr6SiZBH6+Oy0\r\n/lRNE0dMkaXpY0nQvxtf2+DLLdQHa/akIEsyna5vsByZmu7sf3ZKdof9xB7M97qN\r\nQgIDygMmQxJbi35rjEoqau8TId2qptGHFz5NVTUCgYBfTZSQaehxBsQ7wK/h40+f\r\nBpuxe4iBrEJ9aXWuH3GT7up4GX2dKA1JUg2lJ+K+g53CWm0lUG8UaN6Vk2pFDTc7\r\nBz7HNWYvFkGZfDGPUmIYn5mgN+AlDuT9caqLTG4dY4K0qqNhqndmLaFGmjsEhEBE\r\nPEZxSmouq/fhRsM0GGiSZg==\r\n-----END PRIVATE KEY-----'),
(16, 'Deo', 'deo@gmail.com', '$2y$10$cd/NI9epdruDfP932GGmEu4YL3gFpSXH4GLFDzw8R90T31O6YT/TO', '2025-06-07 16:06:36', 'default_profile.jpg', 0, '2025-06-15 11:08:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArqK2kWMmPrVNA6nYDWQm\r\n1mSk2u2uDdS0CfgGb0vkisr7CLYVKIM1oAW69TozY2mWxo10V+qgtYjn4i2ftZcH\r\nSgWZMiB8zE3iAim2f1jMsiHHgJFTDt6243Jzl8kwYw+EzBchZ91DrIaGkSD+2UR5\r\ne02AFMYl2yO//ImslTfOgzRvW0FoOqL4geDD/Lg5CFbH/EWboWRu8fMkS8mQt46F\r\n5zEMM4pdv06+Sqfamghn6YSxE61MYEXazJqbw5B+XUruaoMj5IaZq14hJTMjymrU\r\nIe4INEshxdqr8Y52V0WiZmHxGtdlwhoWfk21iXC31PoP11AfCS1EZqcRKdZfLuOl\r\nlQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCuoraRYyY+tU0D\r\nqdgNZCbWZKTa7a4N1LQJ+AZvS+SKyvsIthUogzWgBbr1OjNjaZbGjXRX6qC1iOfi\r\nLZ+1lwdKBZkyIHzMTeICKbZ/WMyyIceAkVMO3rbjcnOXyTBjD4TMFyFn3UOshoaR\r\nIP7ZRHl7TYAUxiXbI7/8iayVN86DNG9bQWg6oviB4MP8uDkIVsf8RZuhZG7x8yRL\r\nyZC3joXnMQwzil2/Tr5Kp9qaCGfphLETrUxgRdrMmpvDkH5dSu5qgyPkhpmrXiEl\r\nMyPKatQh7gg0SyHF2qvxjnZXRaJmYfEa12XCGhZ+TbWJcLfU+g/XUB8JLURmpxEp\r\n1l8u46WVAgMBAAECggEAI40JSIicKQjkgDnruzInu95qNw1KsRMbgaoFuVOEV4Lh\r\n3tCedwxa9tQjzrdF7dd5c7spM5qWk84mkoNCL4fq2nVQTgWSEk9KkrEmLz+HED5T\r\nLEepVm4dYikTAhPZb1kh6iBNUyUWmUEblODYMKfBaUoKKHB8FWPuOf1W7luZhVjp\r\nkIsZ2yaiB1gE3TiJguwnnTud1y572UqGNfGd/ezCoys4HXU0WIxIAUzKvxnC7o0t\r\n3ynXp/U62YlBo6Mjscu2s+FGJqzFdHlLC6yXV3D8hXmY9E7NIfJW9CIkOjN66ymg\r\npPbpE3dC5dnZd3EgrVLdXZxN2VGST0lCVMTtP9oCQwKBgQDg0DkFc4+VEV4QtljG\r\nuaqJvyZhqcMCXPnPHGe2Gm5MJalheaLSB3hqnl+5+IGlIoqzSebj879kg6/DpJkq\r\nfOjv1R91yeEERqAofnHYNLRymxS/wWLYy5L5IfH7PYQN3UJOkBhwRY0rBdUYHEKu\r\nah6TX0Qqshmb+/xbkmZn5lVRcwKBgQDG3Iga0R75hVQxjsgVzTJqjmE5FqlM4yPE\r\nfLRfzuU2AYtxCIgiz6W7Sbcynzu3CaJmAx1wjdGskDMdByrXevcO8wdJwCMol2nZ\r\ngm3YYzZWqEgxQv1JqeaqWXem1WnKrCuGl6/2aPF1kPXP6i4LSgj6WCSaP7xeOmDX\r\niPboLUtK1wKBgB5JTvVCDTza2x5LQoh7KNNn0gbkNOZTmj/hpsMsqmFNzZTZKys6\r\nYGmUrnbCWMzja2Yd9aIOC2HCL+KegRftPgBZaOSYbt0Bmr/50OJ8rzalV9VBe0yT\r\nmFhBz3S2Y9zuSumElhZB+HOsVHnsDLushjP6aJeL6NFP0D6R6YPjzuirAoGBAImP\r\nB381ZS3JcuINGI8sMFHRR6OL73TGMnm6obMclSV0kBaShkk+RjB134ne6BnHSlUy\r\nlmzDTWUHIrRTvujroHbw2fvEMw3jH09cj8t9ZZswMTASXM9V/b+cv83iFpoh2sHx\r\nr8DN+ykOK1u1bEBW6Dr0Oe2RZSXxUAgIMHOO0WRlAoGAffjsjIxIiOUK32oDDYp0\r\ne1LuvCUfWBt2TOh1Swnl58thUPAQEa9mUl8Wl6UO21OCQTzlFtTe1c4w98MAoziP\r\nHBN4AH4mKnOl4rwbJkBqCp80z9Bhg3GIlug2nMevvZg6LVNiB4NzsgaRYBEAEzWv\r\nwL+BZKacdnWjaghgVww1oHA=\r\n-----END PRIVATE KEY-----'),
(17, 'bwambabwenda', 'peterkzj@gmail.com', '$2y$10$826OOtjdFAzr79EFb5NLNO3x4W8rtWz/4djRjTYpbUV7zCsmz3o72', '2025-10-15 14:25:39', 'default_profile.jpg', 0, '2025-10-15 14:28:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAzC4j6cHvJZcQl97DJeKN\r\nt3FBb3VtsZwW0QGqjPvaxkHF6UpARg0SbRMWpKGF/P7Yw768SCfnCPPO7a+XjztR\r\nHKgErfgdI4MyZXbvU0VjfI4F9Nz3X3TsEJN84vXjccxvht1/UoPSkBBdT8AYEHlG\r\nz82gGSMUGtwsBVur9rTJU9SDPttzs8w77/GOFKlHQ133j8x2cx7uWml15dxRRs7R\r\nR6Cv2Llo6f0POq8aAW82+39StwueMltUrnlvrO0ba3ERitDocC1ctAPO5wlxPOVv\r\nRAPn9dhjBtFgQQ6AenCblMeTNrGl2dD/feTxtkKUMnddvJ+RITjFsbWJ8PyE6UmL\r\nNwIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQDMLiPpwe8llxCX\r\n3sMl4o23cUFvdW2xnBbRAaqM+9rGQcXpSkBGDRJtExakoYX8/tjDvrxIJ+cI887t\r\nr5ePO1EcqASt+B0jgzJldu9TRWN8jgX03PdfdOwQk3zi9eNxzG+G3X9Sg9KQEF1P\r\nwBgQeUbPzaAZIxQa3CwFW6v2tMlT1IM+23OzzDvv8Y4UqUdDXfePzHZzHu5aaXXl\r\n3FFGztFHoK/YuWjp/Q86rxoBbzb7f1K3C54yW1SueW+s7RtrcRGK0OhwLVy0A87n\r\nCXE85W9EA+f12GMG0WBBDoB6cJuUx5M2saXZ0P995PG2QpQyd128n5EhOMWxtYnw\r\n/ITpSYs3AgMBAAECggEAUy/VR+7QjWMfypBit7O3A28sNsoEGCG9Fgh0wR33g2DW\r\nznaG+0NmJ3RofEimu23lSNMUCN3g/j+/Jg2tVRjYsjEuPubgkFBqnvY3CZkysFN4\r\nz8ubKZQMfbBpaFrAAORG6A6kSi0VA8b3DX/5DfUrSYzAVp15Gnxrnv87b7c2c2Vd\r\n5B+2fEgU734NjS4cYP9LiOq5fPi4TMslRwSiHVXMRNNsbgThVHy94N1pmqeOBSGD\r\nzrntfhS0u631kHZKz0xrztzARfUCwVp0I9RijBceEHHtGLWFaQ8neTu0MjIATr/H\r\n5D8m21yBuCC94mKvsKJGWBhT5X4vDMWnrhS3l6Nw8QKBgQD4nfCSRfhzVfganhqU\r\nKK1UX/VW/28Jl6SsMUsK4yC0ziMp5FuFQJT5IDcIqWt+aLgA4l1FstlxfC+z0rJ1\r\nL4Zg7H4hjGm8AVRF0ul4mCFSUugqXQBo52ANYtqqKou0xY3NQJOmXHIP1mNk8BTS\r\nhw5S1veV/c4cGSxk8081ge5VKQKBgQDSPmEeinDk+bUP1eZ6lYBbxqbTcaIh+Rt1\r\nqv/2koug04CPYnMB76A6Z/Guw0yDetyiM6itYxjlFN3QFBAu2jxKGE0+WFjrfOC0\r\nsK07b66zzFb8tNzJ9V3bDB223ZQhX46x0UV6nnut3glFy6XXWBz0hBXzlcIdqhfy\r\nw4Oz772JXwKBgCcYnzZBbpqkkEmPR1q5Mtir5mbx8EIv6KNzdPuXUBNev0TdNk80\r\nIrkyibUA/3h3e0gYUNafE3a3MsEyhwHKoXUoe4VHEXGRO/FnA3QFaGgLxZqz2Val\r\n3AL+4qgT3LhmwK/gUde9fepjqmm7H2sj9eqtB2485WahxstxP6mIbzRRAoGBAI2W\r\nxQE0auNJ585KV3WmyVMFbcRoerA2e2+7QbZk6vPcfHAT9TAQmO+8oN75V8YUMBJs\r\nh+R9IH7mlptTZ7Kl9oRP6XVbOkcdSpdlhMTKafVcYBjAFRFN1W93sWs9vkzbddX7\r\nAbeak+B77/K5O27TQGzLp5zr9lbT/lzXnxLHGfYxAoGAaE/TqNGnJ2mJW5RKJG11\r\naP3RqDsgQmBZvnSNYMagdocVfHiHUR4QVteskICgKXw0R8BkP9BUebH6DRpJiqYH\r\nnzHFQ4YsIF3yeNp5ZOlEqDd+vHP+L51W8mj0LijQoXu1bh8Dbu6kdd4ajv1klZtw\r\nIsb77YhaR72jbV24qP/Yl/8=\r\n-----END PRIVATE KEY-----');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `shares` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `user_id`, `title`, `description`, `video_url`, `created_at`, `shares`) VALUES
(1, 6, 'Down below ', 'Life ', 'assets/videos/67d2b02fb6b4a.mp4', '2025-03-13 10:15:12', 0),
(2, 6, 'Dollar ', 'Peter iconic ', 'assets/videos/67d2c92185edb.mp4', '2025-03-13 12:01:37', 0),
(3, 6, 'Peter iconic official audio ', 'Yes', 'assets/videos/67d2d5de9af5f.mp4', '2025-03-13 12:55:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `views`
--

INSERT INTO `views` (`id`, `user_id`, `video_id`, `created_at`) VALUES
(1, 1, 2, '2025-03-13 12:51:45'),
(2, 1, 1, '2025-03-13 12:51:45'),
(3, 1, 2, '2025-03-13 12:54:05'),
(4, 1, 1, '2025-03-13 12:54:05'),
(5, 1, 2, '2025-03-13 12:54:43'),
(6, 1, 1, '2025-03-13 12:54:44'),
(7, 1, 3, '2025-03-13 12:56:03'),
(8, 1, 2, '2025-03-13 12:56:03'),
(9, 1, 1, '2025-03-13 12:56:04'),
(10, 1, 3, '2025-03-13 12:56:18'),
(11, 1, 2, '2025-03-13 12:56:18'),
(12, 1, 1, '2025-03-13 12:56:18'),
(13, 1, 3, '2025-03-13 12:56:46'),
(14, 1, 2, '2025-03-13 12:56:46'),
(15, 1, 1, '2025-03-13 12:56:46'),
(16, 1, 3, '2025-03-13 13:13:45'),
(17, 1, 2, '2025-03-13 13:13:45'),
(18, 1, 1, '2025-03-13 13:13:45'),
(19, 1, 3, '2025-03-13 13:14:34'),
(20, 1, 2, '2025-03-13 13:14:35'),
(21, 1, 1, '2025-03-13 13:14:35'),
(22, 1, 3, '2025-03-13 13:16:23'),
(23, 1, 2, '2025-03-13 13:16:23'),
(24, 1, 1, '2025-03-13 13:16:23'),
(25, 1, 3, '2025-03-13 13:18:30'),
(26, 1, 2, '2025-03-13 13:18:30'),
(27, 1, 1, '2025-03-13 13:18:30'),
(28, 1, 3, '2025-03-13 15:24:05'),
(29, 1, 2, '2025-03-13 15:24:05'),
(30, 1, 1, '2025-03-13 15:24:05'),
(31, 1, 3, '2025-03-13 15:29:11'),
(32, 1, 2, '2025-03-13 15:29:11'),
(33, 1, 1, '2025-03-13 15:29:12'),
(34, 6, 3, '2025-03-13 15:44:03'),
(35, 6, 2, '2025-03-13 15:44:03'),
(36, 6, 1, '2025-03-13 15:44:04'),
(37, 6, 3, '2025-03-13 15:44:32'),
(38, 6, 2, '2025-03-13 15:44:32'),
(39, 6, 1, '2025-03-13 15:44:32'),
(40, 6, 3, '2025-03-13 15:44:59'),
(41, 6, 2, '2025-03-13 15:44:59'),
(42, 6, 1, '2025-03-13 15:44:59'),
(43, 1, 3, '2025-03-14 10:40:40'),
(44, 1, 2, '2025-03-14 10:40:40'),
(45, 1, 1, '2025-03-14 10:40:40'),
(46, 6, 3, '2025-03-14 10:43:37'),
(47, 6, 2, '2025-03-14 10:43:37'),
(48, 6, 1, '2025-03-14 10:43:37'),
(49, 6, 3, '2025-03-14 10:44:07'),
(50, 6, 2, '2025-03-14 10:44:07'),
(51, 6, 1, '2025-03-14 10:44:07'),
(52, 6, 3, '2025-03-14 11:37:37'),
(53, 6, 2, '2025-03-14 11:37:37'),
(54, 6, 1, '2025-03-14 11:37:38'),
(55, 8, 3, '2025-03-14 11:39:02'),
(56, 8, 2, '2025-03-14 11:39:02'),
(57, 8, 1, '2025-03-14 11:39:03'),
(58, 8, 3, '2025-03-14 11:45:04'),
(59, 8, 2, '2025-03-14 11:45:04'),
(60, 8, 1, '2025-03-14 11:45:04'),
(61, 6, 3, '2025-03-14 12:00:16'),
(62, 6, 2, '2025-03-14 12:00:16'),
(63, 6, 1, '2025-03-14 12:00:17'),
(64, 6, 3, '2025-03-15 09:30:34'),
(65, 6, 2, '2025-03-15 09:30:35'),
(66, 6, 1, '2025-03-15 09:30:35'),
(67, 6, 3, '2025-03-15 09:33:31'),
(68, 6, 2, '2025-03-15 09:33:31'),
(69, 6, 1, '2025-03-15 09:33:32'),
(70, 4, 3, '2025-03-15 09:35:45'),
(71, 4, 2, '2025-03-15 09:35:45'),
(72, 4, 1, '2025-03-15 09:35:45'),
(73, 4, 3, '2025-03-15 09:50:07'),
(74, 4, 2, '2025-03-15 09:50:08'),
(75, 4, 1, '2025-03-15 09:50:08'),
(76, 4, 3, '2025-03-15 10:00:18'),
(77, 4, 2, '2025-03-15 10:00:18'),
(78, 4, 1, '2025-03-15 10:00:18'),
(79, 4, 3, '2025-03-15 10:00:24'),
(80, 4, 2, '2025-03-15 10:00:24'),
(81, 4, 1, '2025-03-15 10:00:25'),
(82, 4, 3, '2025-03-15 10:05:43'),
(83, 4, 2, '2025-03-15 10:05:43'),
(84, 4, 1, '2025-03-15 10:05:43'),
(85, 6, 3, '2025-03-15 10:06:07'),
(86, 6, 2, '2025-03-15 10:06:08'),
(87, 6, 1, '2025-03-15 10:06:08'),
(88, 4, 3, '2025-03-15 10:11:40'),
(89, 4, 2, '2025-03-15 10:11:40'),
(90, 4, 1, '2025-03-15 10:11:40'),
(91, 4, 3, '2025-03-15 10:16:15'),
(92, 4, 2, '2025-03-15 10:16:15'),
(93, 4, 1, '2025-03-15 10:16:15'),
(94, 6, 3, '2025-03-15 10:25:51'),
(95, 6, 2, '2025-03-15 10:25:52'),
(96, 6, 1, '2025-03-15 10:25:52'),
(97, 4, 3, '2025-03-15 11:16:11'),
(98, 4, 2, '2025-03-15 11:16:11'),
(99, 4, 1, '2025-03-15 11:16:11'),
(100, 4, 3, '2025-03-15 11:17:10'),
(101, 4, 2, '2025-03-15 11:17:10'),
(102, 4, 1, '2025-03-15 11:17:10'),
(103, 4, 3, '2025-03-15 11:17:17'),
(104, 4, 2, '2025-03-15 11:17:17'),
(105, 4, 1, '2025-03-15 11:17:17'),
(106, 6, 3, '2025-03-15 11:55:20'),
(107, 6, 2, '2025-03-15 11:55:20'),
(108, 6, 1, '2025-03-15 11:55:20'),
(109, 6, 3, '2025-03-15 12:16:08'),
(110, 6, 2, '2025-03-15 12:16:08'),
(111, 6, 1, '2025-03-15 12:16:08'),
(112, 6, 3, '2025-03-15 12:35:18'),
(113, 6, 2, '2025-03-15 12:35:18'),
(114, 6, 1, '2025-03-15 12:35:18'),
(115, 6, 3, '2025-03-18 08:19:56'),
(116, 6, 2, '2025-03-18 08:19:56'),
(117, 6, 1, '2025-03-18 08:19:56'),
(118, 6, 3, '2025-03-18 08:25:27'),
(119, 6, 2, '2025-03-18 08:25:27'),
(120, 6, 1, '2025-03-18 08:25:27'),
(121, 6, 3, '2025-03-20 16:58:11'),
(122, 6, 2, '2025-03-20 16:58:11'),
(123, 6, 1, '2025-03-20 16:58:11'),
(124, 6, 3, '2025-03-20 17:42:54'),
(125, 6, 2, '2025-03-20 17:42:54'),
(126, 6, 1, '2025-03-20 17:42:54'),
(127, 6, 3, '2025-03-20 22:11:26'),
(128, 6, 2, '2025-03-20 22:11:26'),
(129, 6, 1, '2025-03-20 22:11:26'),
(130, 6, 3, '2025-03-20 22:35:10'),
(131, 6, 2, '2025-03-20 22:35:10'),
(132, 6, 1, '2025-03-20 22:35:10'),
(133, 6, 3, '2025-03-20 22:53:20'),
(134, 6, 2, '2025-03-20 22:53:20'),
(135, 6, 1, '2025-03-20 22:53:20'),
(136, 6, 3, '2025-03-20 23:45:18'),
(137, 6, 2, '2025-03-20 23:45:19'),
(138, 6, 1, '2025-03-20 23:45:19'),
(139, 7, 3, '2025-03-21 12:19:00'),
(140, 7, 2, '2025-03-21 12:19:00'),
(141, 7, 1, '2025-03-21 12:19:00'),
(142, 7, 3, '2025-03-21 17:18:53'),
(143, 7, 2, '2025-03-21 17:18:53'),
(144, 7, 1, '2025-03-21 17:18:53'),
(145, 6, 3, '2025-04-01 18:13:22'),
(146, 6, 2, '2025-04-01 18:13:22'),
(147, 6, 1, '2025-04-01 18:13:23'),
(148, 10, 3, '2025-04-03 13:10:47'),
(149, 10, 2, '2025-04-03 13:10:48'),
(150, 10, 1, '2025-04-03 13:10:48'),
(151, 10, 3, '2025-04-03 13:11:41'),
(152, 10, 2, '2025-04-03 13:11:41'),
(153, 10, 1, '2025-04-03 13:11:41'),
(154, 4, 3, '2025-04-03 17:59:26'),
(155, 4, 2, '2025-04-03 17:59:26'),
(156, 4, 1, '2025-04-03 17:59:26'),
(157, 4, 3, '2025-04-03 18:06:34'),
(158, 4, 2, '2025-04-03 18:06:34'),
(159, 4, 1, '2025-04-03 18:06:34'),
(160, 13, 3, '2025-04-24 17:15:26'),
(161, 13, 2, '2025-04-24 17:15:26'),
(162, 13, 1, '2025-04-24 17:15:26'),
(163, 6, 3, '2025-04-24 17:29:23'),
(164, 6, 2, '2025-04-24 17:29:23'),
(165, 6, 1, '2025-04-24 17:29:23'),
(166, 12, 3, '2025-04-24 17:31:19'),
(167, 12, 2, '2025-04-24 17:31:19'),
(168, 12, 1, '2025-04-24 17:31:19'),
(169, 13, 3, '2025-04-24 17:40:07'),
(170, 13, 2, '2025-04-24 17:40:07'),
(171, 13, 1, '2025-04-24 17:40:07'),
(172, 13, 3, '2025-04-24 17:40:16'),
(173, 13, 2, '2025-04-24 17:40:16'),
(174, 13, 1, '2025-04-24 17:40:17'),
(175, 1, 3, '2025-04-28 15:11:47'),
(176, 1, 2, '2025-04-28 15:11:47'),
(177, 1, 1, '2025-04-28 15:11:47'),
(178, 1, 3, '2025-05-22 16:24:30'),
(179, 1, 2, '2025-05-22 16:24:31'),
(180, 1, 1, '2025-05-22 16:24:31'),
(181, 6, 3, '2025-06-05 16:46:29'),
(182, 6, 2, '2025-06-05 16:46:32'),
(183, 6, 1, '2025-06-05 16:46:33'),
(184, 6, 3, '2025-06-05 16:51:20'),
(185, 6, 2, '2025-06-05 16:51:20'),
(186, 6, 1, '2025-06-05 16:51:21'),
(187, 16, 3, '2025-06-07 16:31:17'),
(188, 16, 2, '2025-06-07 16:31:17'),
(189, 16, 1, '2025-06-07 16:31:17'),
(190, 16, 3, '2025-06-15 11:08:00'),
(191, 16, 2, '2025-06-15 11:08:00'),
(192, 16, 1, '2025-06-15 11:08:01'),
(193, 14, 3, '2025-10-02 17:12:50'),
(194, 14, 2, '2025-10-02 17:12:50'),
(195, 14, 1, '2025-10-02 17:12:51'),
(196, 14, 3, '2025-10-04 21:36:07'),
(197, 14, 2, '2025-10-04 21:36:07'),
(198, 14, 1, '2025-10-04 21:36:07'),
(199, 17, 3, '2025-10-15 14:28:09'),
(200, 17, 2, '2025-10-15 14:28:09'),
(201, 17, 1, '2025-10-15 14:28:09');

-- --------------------------------------------------------

--
-- Table structure for table `webrtc_signaling_logs`
--

CREATE TABLE `webrtc_signaling_logs` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `type` enum('offer','answer','candidate') NOT NULL,
  `data` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ads`
--
ALTER TABLE `ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `advanced_analytics`
--
ALTER TABLE `advanced_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_id` (`ad_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ad_analytics`
--
ALTER TABLE `ad_analytics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_id` (`ad_id`);

--
-- Indexes for table `ad_billing`
--
ALTER TABLE `ad_billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_id` (`ad_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `calls`
--
ALTER TABLE `calls`
  ADD PRIMARY KEY (`id`),
  ADD KEY `caller_id` (`caller_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `follower_id` (`follower_id`),
  ADD KEY `following_id` (`following_id`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_friendship` (`user_id`,`friend_id`),
  ADD KEY `friend_id` (`friend_id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `pricing_plans`
--
ALTER TABLE `pricing_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reactions`
--
ALTER TABLE `reactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reaction` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `video_id` (`video_id`);

--
-- Indexes for table `webrtc_signaling_logs`
--
ALTER TABLE `webrtc_signaling_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ads`
--
ALTER TABLE `ads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `advanced_analytics`
--
ALTER TABLE `advanced_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ad_analytics`
--
ALTER TABLE `ad_analytics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ad_billing`
--
ALTER TABLE `ad_billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calls`
--
ALTER TABLE `calls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `post_tags`
--
ALTER TABLE `post_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pricing_plans`
--
ALTER TABLE `pricing_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `views`
--
ALTER TABLE `views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `webrtc_signaling_logs`
--
ALTER TABLE `webrtc_signaling_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ads`
--
ALTER TABLE `ads`
  ADD CONSTRAINT `ads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `advanced_analytics`
--
ALTER TABLE `advanced_analytics`
  ADD CONSTRAINT `advanced_analytics_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `advanced_analytics_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_analytics`
--
ALTER TABLE `ad_analytics`
  ADD CONSTRAINT `ad_analytics_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ad_billing`
--
ALTER TABLE `ad_billing`
  ADD CONSTRAINT `ad_billing_ibfk_1` FOREIGN KEY (`ad_id`) REFERENCES `ads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ad_billing_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `calls`
--
ALTER TABLE `calls`
  ADD CONSTRAINT `calls_ibfk_1` FOREIGN KEY (`caller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calls_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `followers`
--
ALTER TABLE `followers`
  ADD CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`following_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `friends`
--
ALTER TABLE `friends`
  ADD CONSTRAINT `friends_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friends_ibfk_2` FOREIGN KEY (`friend_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD CONSTRAINT `friend_requests_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `friend_requests_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `post_tags`
--
ALTER TABLE `post_tags`
  ADD CONSTRAINT `post_tags_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Constraints for table `reactions`
--
ALTER TABLE `reactions`
  ADD CONSTRAINT `reactions_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `stories`
--
ALTER TABLE `stories`
  ADD CONSTRAINT `stories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `views`
--
ALTER TABLE `views`
  ADD CONSTRAINT `views_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `views_ibfk_2` FOREIGN KEY (`video_id`) REFERENCES `videos` (`id`);

--
-- Constraints for table `webrtc_signaling_logs`
--
ALTER TABLE `webrtc_signaling_logs`
  ADD CONSTRAINT `webrtc_signaling_logs_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `webrtc_signaling_logs_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
