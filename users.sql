-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 13, 2024 at 11:06 AM
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
-- Database: `datatables_crud`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employeenumber` int(11) NOT NULL,
  `picture_path` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `mobile` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `city` varchar(30) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `job` varchar(250) NOT NULL,
  `secjob` varchar(255) NOT NULL,
  `type_of_work` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employeenumber`, `picture_path`, `username`, `mobile`, `email`, `city`, `status`, `job`, `secjob`, `type_of_work`) VALUES
(1, 2, 'uploads/2.webp', 'mjbhj', '0923024166', 'hybaayou@gmail.com', 'knkj', 'active', 'bvgh', 'hjvhj', 'فني درجة اولى'),
(2, 4, 'uploads/4.webp', 'hakim', '0923024166', 'hybaayou@gmail.com', 'benghazi', 'active', 'software', 'lkmfwfl', 'فورمان'),
(3, 56, 'uploads/56.webp', 'wrf', '0923024166', 'hybaayou@gmail.com', 'fv', 'active', 'wsfcw', 'sv', 'فني درجة اولى');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_employeenumber` (`employeenumber`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=172;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
