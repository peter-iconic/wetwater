-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 13, 2025 at 11:42 AM
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
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `parent_comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `post_id`, `user_id`, `content`, `created_at`, `parent_comment_id`) VALUES
(1, 1, 1, 'so what', '2025-03-05 15:59:40', NULL),
(2, 1, 1, 'who', '2025-03-05 16:04:41', 1),
(3, 1, 1, 'what', '2025-03-05 16:07:02', 2),
(4, 2, 1, 'yo', '2025-03-07 10:20:04', NULL),
(5, 2, 1, 'whats yo bro', '2025-03-07 10:25:02', 4),
(6, 7, 7, 'so what', '2025-03-10 17:05:18', NULL),
(7, 7, 7, 'get out', '2025-03-10 17:13:40', 6);

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
(42, 6, 7, 'b4i2GxWAX+gbuJsjrv5VPW4Ifa8Vka90iP3smtfJxUpOJ7ZYfFwq0uWMcFZUbZ8gp5sNqJtxZFI800Bebl36G2a6X0/ZfR+sjsifi5v/B/b/7AHM9Y1JMambwQmapjKl349L49CqkwqEg0cQEF8S1RyCld+xJm72fbGlI3r3s4O0hylWyAzHOGi9oB67qdQVB32R9tBLb0JIEXm2hQq25wPiRlePxQVuidb8t+lDASyS+ha7ol+aVfk5SHMh5ZMZdxSgITG9m5M1V4uuflvjv+5nyKPBYKKXVBPgq2oiwkA3c8KkwPCIRVH1V0UF+bFYnMySl84zLxnISguGBqWpFA==', 0, '2025-03-10 18:05:32', 'yo man', NULL, 0, 0);

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
(8, 6, 'mike reacted to your post with a like.', 1, '2025-03-12 19:45:51');

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
(8, 6, 'Career coming soon', '2025-03-12 19:42:51', 'assets/images/posts/67d1e3bb90119_IMG_20250220_110108.jpg', NULL, NULL);

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
(3, 3, 1, 'dislike', '2025-03-07 11:14:44'),
(4, 5, 3, 'like', '2025-03-07 14:17:19'),
(5, 6, 1, 'like', '2025-03-07 22:37:18'),
(6, 2, 1, 'like', '2025-03-07 22:42:06'),
(7, 7, 5, 'like', '2025-03-10 17:02:50'),
(8, 7, 7, 'like', '2025-03-10 17:04:35'),
(9, 7, 6, 'like', '2025-03-10 17:19:45'),
(10, 4, 6, 'like', '2025-03-12 10:27:28'),
(11, 5, 6, 'like', '2025-03-12 16:15:55'),
(12, 8, 6, 'like', '2025-03-12 19:45:51');

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
(7, 6, 'assets/images/stories/67d1e4383bc70_Screenshot_20250221-225111.png', '2025-03-12 19:44:56', '2025-03-13 19:44:56');

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
(1, 'peter', 'peter@gmail.com', '$2y$10$BK9q4.SeL.sODTLZv7QDnuPPkMCyGEvckaBFiAjc7GUBk0JHuV3M2', '2025-03-05 15:11:56', 'Screenshot (1).png', 0, '2025-03-13 10:34:46', 'Peter Iconic', 'Zambia is my mother, Congo is my father', 'Lusaka', 'Music', 'Y3,S2', 'Student', '', '0768894862', '', ''),
(2, 'nancy', 'nancy@gmail.com', '$2y$10$zmasnKGm0jaHSn8863ISKej6/zq/QSGoDJRxboPJ.GgU6pYZpBgH6', '2025-03-05 17:39:59', 'default_profile.jpg', 0, '2025-03-07 13:32:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(3, 'lion', 'lion@gmail.com', '$2y$10$..16scBL7N5uPa93xLnZ9uZ5IS9ov9AlrBJOBdW6WEaJJjlfGPUTG', '2025-03-06 17:17:25', 'default_profile.jpg', 0, '2025-03-07 13:32:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(4, 'admin', 'admin@gmail.com', '$2y$10$En8eSveJao9c7hnAhv10eumt7P2slCBtPRHSVUihDwbnqCHX54ihq', '2025-03-08 00:21:24', 'default_profile.jpg', 1, '2025-03-10 13:23:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', ''),
(5, 'eazy', 'eazy@gmail.com', '$2y$10$1QqAb4RmlLrM1W6IpEO0jeROHpaj/hnoszAvjoga9GPruBguqzj1S', '2025-03-08 23:18:45', 'IMG_20230524_161352_960.jpg', 0, '2025-03-10 18:20:06', '', '', '', '', '', '', '', '', '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAqp7eVgpsdyJTy0no1Wzb\r\nkxyE/jZYNEI+z42Ya3yOxwcM539lG8isETVwmoY3315IHC6KefeTXeLqsrAL//8/\r\nnoqGWh3qgcPCitG+cwvcS34lQCURDth2Oz2Zvh2F/JOvlLiTOGSDelPwAbOT93kj\r\nVy1hT8SMz0+jzfWX4K7k30Cf5Fr+Lrz1G45WIrzgVAf5We8OlAraCoaNZidd/vV1\r\nAQSF9IgAcfHRsKyGY/ojm1XxITykCsDI1dHOEsX2zB852b4dmYYVzTvTxAcYhOuL\r\n58IjAn+coNeADwlRizNpZoO2MKdUvqEgMQbyBl2UrPNP2tQUa3x/Vfsk9Ag/DwSl\r\nLQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvwIBADANBgkqhkiG9w0BAQEFAASCBKkwggSlAgEAAoIBAQCqnt5WCmx3IlPL\r\nSejVbNuTHIT+Nlg0Qj7PjZhrfI7HBwznf2UbyKwRNXCahjffXkgcLop595Nd4uqy\r\nsAv//z+eioZaHeqBw8KK0b5zC9xLfiVAJREO2HY7PZm+HYX8k6+UuJM4ZIN6U/AB\r\ns5P3eSNXLWFPxIzPT6PN9ZfgruTfQJ/kWv4uvPUbjlYivOBUB/lZ7w6UCtoKho1m\r\nJ13+9XUBBIX0iABx8dGwrIZj+iObVfEhPKQKwMjV0c4SxfbMHznZvh2ZhhXNO9PE\r\nBxiE64vnwiMCf5yg14APCVGLM2lmg7Ywp1S+oSAxBvIGXZSs80/a1BRrfH9V+yT0\r\nCD8PBKUtAgMBAAECggEADbHVNjI9fP12HNPImrLTV/Y6zX22rDEiAf5DPONhQWPV\r\nOxKMXsPHXdU8fcamnujeIFby+fGvdCJ1xJRhTjGiXVsQiBStIS/Bgmtt3iWWT7/n\r\nBQREn3yr1rrtx2buvXRsLCN5e5YDwJKSqcxMZNlmBwYHMMQdqjkh9HLRNzFKgQm5\r\nCNuyC02vkejFf0AV+TXUhOXzBuya6HG/eHSwTwGYOjVP9mNS6aD2zAoEuoTmJJJC\r\n6OM7M61BjBkulK2qFDhi+rGhFi9E2MYPhSSF7Rl/U01EHA0/kNXQmYwsw7jn+ce9\r\n2DcTf/tZy4ypKfTbPTm4YMWy9s1zepgfqNklF1zIQQKBgQDY/1KuILN9oeSX6jjv\r\nuz2xsS/t+QKuwx8tkp0uwEmbD0inB5el6SHOZe8On2eSHSHkS2tIjjjrcD5r8/0U\r\npPg0dP12SRA9xQ7Zo44z9pccpXCtz8ZyyDOJfoKD0iAN5I8QgwKdwhM8LaBwm2xd\r\nSkK5W08sgPqQfUbP2GhoLjEIwQKBgQDJSZ2gopTa1hO4QgzRFTE5OwEZVgcq2cYo\r\nQED9JRn/Wrf0/HlZNeb/2T7oov+TA+h4rH4lEbV9xf988UgJl0U7xl+ANRR/FDaM\r\n1eA+6YkI9Xf+fw1Tvo+TK0xaBb3zGI6TELiMWf1inOXeWodCTqc255CyNkk47W54\r\nkCeTAwurbQKBgQCw+62nr6w1b3FOJg7CGGk9IFMDOPFjMGmhdc8VbmeaPGD5OkwJ\r\nWZflC2Zq+sAyf+hAlvKtfrIV9Lo0ug9UYyi9QB3p97Vza+GsyKUW5KxjBNxeJvSo\r\ncXj3T2OLuDnEmwHEadYcbUna7yvILDu56vN40mxE0/2JE2RJ6SterS35AQKBgQCD\r\n3NMDaZ6kYbvXaIWm7wApIstMgrv9SV7z/WvVqlmGnDKIrmD8nUAv+Wyp0CYndFb1\r\nvuKAfEJuG6iMfDAaAFwdlY34mk1MFrzJtE7MSAc6tDwgn7DmXJ8H5USGcN6IA11b\r\nYIfVghppYKmB6cJUINyQLlDvPnrnbTuChcU3HLanXQKBgQCno0tOmUYBA0stnG1S\r\nIrxdN0c/SacrF0BCpGMQTTsEwGXRdxs1InnucSk897/avRYUEf1aCaNVCZMSSZiS\r\nu8J+9WNgInv8k36zQdCLHQxeuimxrRZZn1VHSqkw+j+RGtIzlnyWkaBd/h8gWleK\r\n5cZ6tZwfs4prHO/IGXuMlk1LGw==\r\n-----END PRIVATE KEY-----'),
(6, 'mike', 'mike@gmail.com', '$2y$10$DaU29aKxZkDEkZXTYbyJWu/Sc/D1doWlnEkrLGs4WgYWEXkBaHyW.', '2025-03-08 23:26:09', 'IMG_20230524_164414_876.jpg', 0, '2025-03-13 10:38:45', '', '', '', '', '', '', '', '', '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAkV4BkS/aPCE+YKtc6y4y\r\nMLVBSjdQFvX97q4rurPR/PGwFB2KtIE0ULwW2yboHHV926caBXuvH/2bSCyNtzHy\r\nFMkhWK4zHcHZbBgThNpn/bqR/JaJ+/NHfIzFgfGRcDjXs/lUbSi0WGNrePE2Ozfc\r\nmlxBBRzolgCbYL5C8kRrLp40UO/wyEtKSpxHokJ+ZO7jdSsajNeLIWR9DxvQz28v\r\n3ReURyYimOZu+kQlcv4n2oiuyxuFcMa4zmi6AkZodhC3Etz2Z1cD9YyHhT5H9d8A\r\n0q8J3P3ZXrk8lKtyhVEpD5NySqvyR+/mOEQvlGEUyqYHjgt4H7AIiRQfjINzBpku\r\nLQIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQCRXgGRL9o8IT5g\r\nq1zrLjIwtUFKN1AW9f3uriu6s9H88bAUHYq0gTRQvBbbJugcdX3bpxoFe68f/ZtI\r\nLI23MfIUySFYrjMdwdlsGBOE2mf9upH8lon780d8jMWB8ZFwONez+VRtKLRYY2t4\r\n8TY7N9yaXEEFHOiWAJtgvkLyRGsunjRQ7/DIS0pKnEeiQn5k7uN1KxqM14shZH0P\r\nG9DPby/dF5RHJiKY5m76RCVy/ifaiK7LG4VwxrjOaLoCRmh2ELcS3PZnVwP1jIeF\r\nPkf13wDSrwnc/dleuTyUq3KFUSkPk3JKq/JH7+Y4RC+UYRTKpgeOC3gfsAiJFB+M\r\ng3MGmS4tAgMBAAECggEAAyi8F3vm/f4J9T9cIFdLa3AQ+GwtzyXu7BA4bpEIW/sj\r\npLhEoqoZKTUBOSeGDVJHVy3xPJPEyUjxZjbjqIrLFEUPtrXocfnbwAPg7rbxYhv7\r\nHrZlzsYpdE054JpPmxT9KRHe0hV7n90fQYxabZyH7InbNuF7M4FVXxubaFjwzF1G\r\n6DonqgzqFdsepfwWDb9kJkQC6vudlTxtCI3G55PzJWaUKKpwtzfDp/fkR3LubXxy\r\nbPYz6Vpkwx70llMX/AREWwbQMGJyEP7HZu5fpkG8Qj0/HoUOP9QdbwFKzJ49H33S\r\nl8+t+fEaIGY3eW9f1UXQv5GJ0m6WZER99VVAOd+rAQKBgQDKkZ6vVl0UV+J1QYZQ\r\nzbf398Yk3MJfkIIeryuynCF2yrmIozqXz9x8UmFBoLyqD+IP6SwA+1PAwFDJZbNC\r\n8HQQTXEWszgWHie7boNg0caB6RQzwMjY8RUpxAPBxncqELIo+cAEM7QU8aEpWQx5\r\n3m3PuK9xJcgFCqXxHuxT/qqhLQKBgQC3td6kwu+UT1jN6RLOwsRxDl+rUeQWm4hQ\r\nRL2pkqOOI86ZagZYYjqi2ku51bkWiy3NxGO+mIi9/sa0CFf0kR2Bd21c54xbXGMQ\r\nLIT3HWBBcdmAo5zM3NY4RSIhjQLBKtZOjK6s8e72+hga64GsMlaASoA4aTqXt/gj\r\nlcU3AR7hAQKBgBsucxagBhlmuZJ2WmmZUShK7SGhJcvg/jTT2I88+BiSl1bIYGJi\r\nl/lurHI7+VTwkKsF5Mu07cYdiDmeFfHThh9x5Mzg+5OsNDSoXaSuQW7JMdbH9at2\r\nnhpenQpxcSgJ2X46FRP7RBzTV4bO1ie8Owv2gkQyh6Z1iVLTjW6v64F5AoGAMRjn\r\nDuuWMfHezvEqeJ2u+HQZ92Rka/JXRPSKJ8ar9XH2ZiPi3D4sY5epw1muJKs/q42A\r\nBtEnQnfTzQupzg/2bcJoNPshFM2lIA513sE4F2WA9pNDdbDTg6heTc8s3ElBiy6o\r\nBEqITfNa+97TAh1V5uWCTRE6eo/NPl1pnqCrCwECgYBYKo0uC7XEmf1zu1Bj0wPw\r\ns/GRkG1p6WAXULNBSUW0PilwdNuJ7uj1BgkNkI/wLIYS36qnvbPP3Rm0sdM/9de6\r\nPDmwrOUCaUn8Rhy8hbcBuBQ/9EzQZSmnsy7P/pRlApiyLxoM6uKsyFreHQ1DUk2e\r\ntWb8lCxQpgJbSwmfQTZadw==\r\n-----END PRIVATE KEY-----'),
(7, 'Kalenga', 'kalenga@gmail.com', '$2y$10$Tv6VgUHhea0E9FvOZlxt8exDRk3Vl7sxRZJZ62sILkXDPBhhq85t2', '2025-03-09 09:59:50', 'default_profile.jpg', 0, '2025-03-10 18:05:59', '', 'wonderlqnd', '', '', '', '', '', '', '-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAu5V7sedn9FwMEtDlVatG\r\nK/uNQJL8n4q0G5ewEg8aBQs39icPNFjA9h6+s08GGMo/s2VATXDgTq5DRyHLyVTQ\r\nvQne59ks+n/NcjhAcn1MTBLsCZFKVPM7xjXp1Xlzfl4yUAae6pgiq+I/77z35TEP\r\noTDNSwHURiUpY2Oci3rQpPt5nkuooTUDrzj9CozTFaSj+WwkIqxz+xw/a8mT1PS7\r\nrr6/R8JmlmO4v96HC6V4bMxQuSYw034deUFg8bWhqMDuupzy3N1RERz7/HtSzLVX\r\n7+Q3YAqWw+Izl2YB7ymy3iCxuzw9LpObvxND3OKDkIC7lpejzb9M6EXPm1+4y94B\r\n3QIDAQAB\r\n-----END PUBLIC KEY-----', '-----BEGIN PRIVATE KEY-----\r\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC7lXux52f0XAwS\r\n0OVVq0Yr+41AkvyfirQbl7ASDxoFCzf2Jw80WMD2Hr6zTwYYyj+zZUBNcOBOrkNH\r\nIcvJVNC9Cd7n2Sz6f81yOEByfUxMEuwJkUpU8zvGNenVeXN+XjJQBp7qmCKr4j/v\r\nvPflMQ+hMM1LAdRGJSljY5yLetCk+3meS6ihNQOvOP0KjNMVpKP5bCQirHP7HD9r\r\nyZPU9Luuvr9HwmaWY7i/3ocLpXhszFC5JjDTfh15QWDxtaGowO66nPLc3VERHPv8\r\ne1LMtVfv5DdgCpbD4jOXZgHvKbLeILG7PD0uk5u/E0Pc4oOQgLuWl6PNv0zoRc+b\r\nX7jL3gHdAgMBAAECggEACgWvEa7A7ZvbY0f48M5RVfk/L0OLLsT84XqFxDk9VSan\r\ny0WD+PKDAMNcwfzHYRyxMZcHy/traJjD7HGAT1XyPx9fYvjF/+5DHkamHtfV8zyR\r\nkuNJ2ucR+wGXaDnwc0B6JK9t3y/YrmFMtDTe88ZexOh0F31WB5dlsjM4wnUB38ae\r\nZfUPUhWF0J6znyKhe4mA58NiRjPBw4lSLV2obtB2PdQ+OjugBOfubEJdP0dDZa8+\r\nWD9289IVXcMZP2c08163YBGeaPrrSKG1gzfRmsQakeGkR+XJUU4LTBv+jXkc+GTw\r\netV+LskeiZxkZ4+VD7f/PqD0hLWRjpFjzAEpCyhUywKBgQDkDMP1XG9QZUTBafSI\r\nKzSAv0ucRlcKQQLrPjwticULvSGg4xz6/6rJuT2rOiT3tty6kocSb0E7k7VePz/P\r\nwNTzmzvdzMFcEeT38y7xHSFJCwMfqvxjW9pAUmrBYE3vC6K9m1btrG3N0GvcXoc/\r\nyvDc4AZJdYR5Sdt3hZLhraQlcwKBgQDSkxGIH1BmeYxlmvJk26unk3K965yW7Mur\r\nNBudwDV+3VzsNC8bfikZaqkVT2Y00yI20GaECELKcGzBMGXtytkG8h+mJUA800eB\r\nPsFWIMa8iPZt5yHHafqG3tLF6nR/wT6J36H5QMOOMt9Cphf05iD/wBgfjQvn+Gtq\r\nAVUByr3nbwKBgHQHCqyLmxcMby74+bFOSig3LAEWyLIu4Y1O3M9OiTKvx6xT4SrT\r\nadG4reewbZ6bKzLB2ndGo6nsPRr2k0Dgm3hWQt9WjgqKEDUXRYrnh0fiknRKSp9C\r\n3IhdZnN8zCoTgXl2z4Odd0CACmDUt3t9hY7bbFdzszMCoObuzwyDjECBAoGAbwfU\r\n6q14O1BD0x9MSBn7/LQmgDXHr1zUV0V2ektq6aXW5UTuwdRX32r6FJ51Cc158OUZ\r\n6OxiK1P0RDk8xZF7tcndHkHuCSRuQ5vPXZaSs76UEYcZrIgY7Rx4jpr9Ko++Zfxg\r\n74hSlJwGVKI3Z44gQDoNfjVk3b+DA7YIGJXKZEkCgYASNpAVXWtSgUY9A63x+lE9\r\n+V/vQD8AENO3dUJ1aLtTiJE5mgg4tdnY6Aod6Q4lhOFUZvmJZ4/UmM8a5xVIAgv8\r\nRQYxMvr9es+MSN94D1HIGhtxykK0UHUGrWCB5R8uwy/K37MN4zVDQL1mEY+VWX+A\r\nXcE2gOkdkHlHkSOCsM6Dsw==\r\n-----END PRIVATE KEY-----');

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
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

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
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pricing_plans`
--
ALTER TABLE `pricing_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reactions`
--
ALTER TABLE `reactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
