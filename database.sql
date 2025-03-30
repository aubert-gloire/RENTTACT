-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 09:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `renttact_db`
--
CREATE DATABASE IF NOT EXISTS `renttact_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `renttact_db`;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE IF NOT EXISTS `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favorite` (`user_id`,`property_id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `property_id`, `created_at`) VALUES
(1, 1, 3, '2025-03-30 16:14:45'),
(2, 1, 2, '2025-03-30 16:15:13'),
(3, 1, 1, '2025-03-30 16:15:15'),
(4, 5, 2, '2025-03-30 16:56:58'),
(5, 5, 3, '2025-03-30 16:56:59'),
(6, 7, 1, '2025-03-30 17:32:04');

-- --------------------------------------------------------

--
-- Table structure for table `properties`
--

DROP TABLE IF EXISTS `properties`;
CREATE TABLE IF NOT EXISTS `properties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `landlord_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `location` varchar(255) NOT NULL,
  `amenities` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `landlord_id` (`landlord_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `properties`
--

INSERT INTO `properties` (`id`, `landlord_id`, `title`, `description`, `price`, `location`, `amenities`, `image_path`, `contact_phone`, `contact_email`, `created_at`) VALUES
(1, 2, 'home', 'kanombe', 150.00, 'Kanombe', 'parking', 'uploads/properties/67e9690c2e6fd.jpg', '0789487154', 'lmurayire@gmail.com', '2025-03-30 15:26:57'),
(2, 2, 'Kabeza House for Rent', 'An affordable  beautiful house for rent ', 200.00, 'Kabeza, Kigali, Rwanda', 'WiFi, Parking, Security, Backyard', 'uploads/properties/67e969cae336c.jfif', '0789487154', 'lmurayire@gmail.com', '2025-03-30 15:56:58'),
(3, 2, 'Kibagabaga House for Rent', 'A beauitful house for rent it\'s fully furnished ', 800.00, 'Kibagabaga', 'WiFi, Parking,Swimming Pool, 5 bedrooms, 6 Bathrooms', 'uploads/properties/67e96a6980047.jfif', '0789487154', 'lmurayire@gmail.com', '2025-03-30 15:59:37'),
(5, 2, 'Kagarama', 'a beautiful unfrunished house for rent', 499.00, 'Kagarama, Kigali, Rwanda', 'wifi, parking, security, 4 bedrooms, 5 bathrooms', 'uploads/properties/67e997de9c2e7.jfif', '0789487154', 'lmurayire@gmail.com', '2025-03-30 19:13:34');

-- --------------------------------------------------------

--
-- Table structure for table `property_images`
--

DROP TABLE IF EXISTS `property_images`;
CREATE TABLE IF NOT EXISTS `property_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `property_id` (`property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `property_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_review` (`property_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `property_id`, `user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 3, 'Beautiful house fosho', '2025-03-30 16:19:57'),
(2, 2, 5, 5, 'Very Nice Place!', '2025-03-30 16:57:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('tenant','landlord') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone_number` varchar(20) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `role`, `phone`, `created_at`, `phone_number`, `name`, `profile_image`, `bio`, `address`) VALUES
(1, 'aubertgloire@gmail.com', '$2y$10$TeOCIQEKWewQwbFnJ1hx7OTqflRDxeczutE4PpOSBZULzNAF3aMlW', 'tenant', '0788268061', '2025-03-30 15:04:47', '0788268061', 'Aubert Gloire', 'uploads/profiles/profile_67e98af014e4d.JPG', 'Diving into the Digital world', 'Kanombe, Kigali, Rwanda'),
(2, 'lmurayire@gmail.com', '$2y$10$tyrLVdkfUuhDd.KLpvS.PeydI3nwaDf./qjvfH8jQok0MynOqaj9y', 'landlord', '0789487154', '2025-03-30 15:06:06', '0789487154', 'Lievin Murayire', 'uploads/profiles/profile_67e98ee6cb827.jfif', 'Real Estate', 'Ndera, Kigali, Rwanda'),
(3, 'Kalisa@gmail.com', '$2y$10$VgknjIZM3ttHSBZPd1wDu.woDzSHCE4I1/1m/RKV5xS6nfT.XGjXK', 'tenant', NULL, '2025-03-30 16:44:35', NULL, 'Kalisa Ivan', NULL, NULL, NULL),
(4, 'ivan@gmail.com', '$2y$10$ed4PVTaGHOrFV.WTGR4uQ.0/Lx1lsy0wXWgKM.2Kijt8CCClyrPgS', 'landlord', NULL, '2025-03-30 16:50:48', NULL, 'Ivan', NULL, NULL, NULL),
(5, 'ladigneagahozo@gmail.com', '$2y$10$6IJk6QiNS.3hbX6m331jYewmhM6RHpFLkQSQCh7hucQtuVLas.vbS', 'tenant', '0788728451', '2025-03-30 16:56:53', NULL, 'Ladigne Agahozo', NULL, NULL, NULL),
(6, 'uwizeyegloriose@gmail.com', '$2y$10$LJ8Iej.zipB/i6cfUcfmhOLzD6oW1S9SdJDV42Dhsu3JLD/KWi27a', 'landlord', '0788445811', '2025-03-30 17:21:04', NULL, 'Uwizeye Gloriose', NULL, NULL, NULL),
(7, 'Gashugi@gmail.com', '$2y$10$hBpYL.zRF4BHswqLAiq8WebxBnb6VXJl6HcHNbW65XgfRLrqdy3uK', 'tenant', '0788301127', '2025-03-30 17:31:50', NULL, 'Gashugi', 'uploads/profiles/profile_67e98a0d08fb7.jfif', 'still looking for better ways', 'Kimironko, Kigali, Rwanda');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`);

--
-- Constraints for table `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`landlord_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `property_images`
--
ALTER TABLE `property_images`
  ADD CONSTRAINT `property_images_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`),
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
