-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 26, 2025 at 03:30 PM
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
-- Database: `cursescheduling`
--

-- --------------------------------------------------------

--
-- Table structure for table `curriculum`
--

CREATE TABLE `curriculum` (
  `curriculum_id` int(11) NOT NULL,
  `curriculum_name` varchar(60) NOT NULL,
  `curriculum_sched` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`curriculum_sched`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(60) NOT NULL,
  `subject_name` varchar(60) NOT NULL,
  `subject_unit` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `subject_unit`) VALUES
(1, '0001', 'Subject 1', '2'),
(2, '0002', 'subject 2', '2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_username` varchar(60) NOT NULL,
  `user_email` varchar(60) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_type` enum('faculty','program chair','dean','gec') NOT NULL,
  `user_fname` varchar(60) NOT NULL,
  `user_mname` varchar(60) DEFAULT NULL,
  `user_lname` varchar(60) NOT NULL,
  `user_status` int(11) NOT NULL DEFAULT 1 COMMENT '0=disabled,1=active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_username`, `user_email`, `user_password`, `user_type`, `user_fname`, `user_mname`, `user_lname`, `user_status`) VALUES
(1, 'dean', 'dean@gmail.com', '$2a$12$fLOq7GrJKIAdsVycQYBA4Oh0KZelvFJ/Qj9NPBgA7jrHU633lW0Fm', 'dean', 'dean', '', 'dean', 1),
(2, 'juan', 'juan@gmail.com', '$2y$10$0MvyJd3XGWnNcmV63Qo.fezf8EDwhhVlMn0SSeS8L33EGsHb5qvv.', 'faculty', 'juanzz', '', 'dela cruz', 1),
(3, 'programchair', 'programchair@gmail.com', '$2y$10$y3ZrKlwGOky8Gzuq8Ls3Q.izCFCw2zwx6LN5UiCYTLONlX1pOGwcm', 'program chair', 'programchair', '', 'padilla', 1),
(4, 'test123', 'test1@gmail.com', '$2y$10$B2hrChQFiaIWOnR//NKaV.1zJgQEWTTTXENOy5HQZaDAd9nXt3rNC', 'faculty', 'programchair', '', 'padilla', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`curriculum_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `curriculum`
--
ALTER TABLE `curriculum`
  MODIFY `curriculum_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
