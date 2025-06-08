-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 08, 2025 at 06:33 AM
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
-- Database: `ceylon_cuisine`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email_address`, `password`, `profile_picture`, `role`, `created_at`) VALUES
(1, 'Lochana Thilakarathne', 'lochanamt@gmail.com', '$2y$10$nbk99T.lkwk4w8UnDsPDeej1iM0iRXNUl3r72fr/RAHBLqZozUxWO', NULL, 'user', '2025-06-08 04:32:54'),
(2, 'Amara Perera', 'amaraperera@gmail.com', '$2y$10$mb3y3hEcW3G37Ii6G4KUxuHMmHhXA4Rxz9gpd4vg2otPbNn7DhfYi', '../uploads/pexels-danxavier-1102341.jpg', 'user', '2025-06-08 04:32:54'),
(50, 'Anil Tissera', 'aniltissera@gmail.com', '$2y$10$VpYQIC5Svb8PF6uEshZuqe.CzLzUh4aMReI8IbjGSgdDxFGic5Xt2', '../uploads/download.jfif', 'user', '2025-06-08 04:32:54'),
(51, 'Admin', 'admin@ceyloncuisine.com', '$2y$10$y45sOqKwcElESZe1plrX3uZy3n4ONxSzY6sirltGbkXz6g25VdcvO', 'default.png', 'admin', '2025-06-08 04:32:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email_address` (`email_address`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
