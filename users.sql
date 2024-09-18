-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 18, 2024 at 07:48 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ceylon-cuisine`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email_address`, `password`) VALUES
(1, 'Lochana Thilakarathne', 'lochanamaleeshathilakarathne@gmail.com', '$2y$10$bN8.kjysEGwW0dMVnY6d2OYLteRYIFdyeBJNBRjP4lwnEfmh4XiVy'),
(2, 'Lochana Thilakarathne', 'lochanamt@gmail.com', '$2y$10$x3OY9mug78e4lVGoq9m.Neas7vspQu2prtdvis9O2ZojQTx9gucrW'),
(3, 'Lochana Thilakarathne', 'ksll@gmail.com', '$2y$10$9ZIDuGpyyUcMS6Nkh8IV0uLVVdmE1mrHAgiFuaGyDFZEj2/FYJwSu'),
(4, 'Thilakarathne H.R.D.L.M', 'abc@gmail.com', '$2y$10$1LR8xilJfgbDja4lAbbI0.WRS1RFGewLFcS.TTUXHvsqe07K7rZYO'),
(5, 'Lochana Thilakarathne', 'nb@gmail.com', '$2y$10$mz4ngc8QLqOILY0FqbNFTe8MRmBjKEE/kKDrkiPNLJI7kKHiNNjh.'),
(6, 'Lochana Thilakarathne', '123@gmail.com', '$2y$10$Epf6BNIKYlJHSyPVW0OkPuqQkgnOuOEjAT5MhAa3.7LPs89gZ8OaW'),
(7, 'Lochana Thilakarathne', 'jkl@example.com', '$2y$10$Q1g7hQ5XQ6LHlBRCuqvGAeyf2UQ1JUZpyMmM5VBuOD6bRNACi2Qdy'),
(8, 'Lochana Thilakarathne', 'asd@exampl.com', '$2y$10$uG2yUKKRjS1gs1/o5fMic.L11nQIfiscbb4vrpUEHInQFLD4XfZWu');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
