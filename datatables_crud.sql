-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 16, 2024 at 01:39 PM
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

INSERT INTO `users` (`id`, `employeenumber`, `username`, `mobile`, `email`, `city`, `status`, `job`, `secjob`, `type_of_work`) VALUES
(1, 12, 'حكيم', '0923024166', 'hybaayou@yahoo.com', 'بنغازي', 'inactive', 'لياس', 'مساعد', 'فني درجة اولى'),
(2, 12, 'حكيم', '0923024166', 'hybaayou@yahoo.com', 'بنغازي', 'inactive', 'لياس', 'مساعد', 'فني درجة اولى'),
(3, 12, 'حكيم', '0923024166', 'hybaayou@yahoo.com', 'بنغازي', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(4, 12, 'حكيم', '0923024166', 'hybaayou@yahoo.com', 'بنغازي', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(5, 12, 'حكيم', '0923024166', 'hybaayou@yahoo.com', 'بنغازي', 'active', 'لياس', 'مساعد', 'فورمان'),
(6, 12, 'حكيم', '0923024166', 'hybaayou@yahoo.com', 'بنغازي', 'active', 'لياس', 'مساعد', 'فورمان'),
(7, 12, 'حكيم', '0923024166', 'hybaayou@yahoo.com', 'بنغازي', 'active', 'لياس', 'مساعدد', 'فني'),
(8, 124, 'hakimbaa', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'inactive', 'لياس', 'مساعد', 'عامل'),
(9, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني'),
(10, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'inactive', 'لياس', 'مساعد', 'فني'),
(11, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'inactive', 'لياس', 'مساعد', 'فني'),
(12, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني'),
(13, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'inactive', 'لياس', 'مساعد', 'فني'),
(14, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني'),
(15, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'inactive', 'لياس', 'مساعد', 'فني'),
(16, 415654, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(17, 0, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(18, 0, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(19, 0, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(20, 0, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(21, 0, 'hakim', '0923024166', 'hybaayou@yahoo.com', 'benghazi', 'active', 'لياس', 'مساعد', 'فني درجة اولى'),
(152, 0, 'حكيم', '923024166', 'hybaayou@yahoo.com', 'بنغازي', 'inactive', '', '', NULL),
(153, 0, 'حكيم', '923024166', 'hybaayou@yahoo.com', 'بنغازي', 'inactive', '', '', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=154;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
