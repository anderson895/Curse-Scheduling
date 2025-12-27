-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 09:16 AM
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
  `id` int(11) NOT NULL,
  `year_semester` varchar(50) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`id`, `year_semester`, `subject_id`) VALUES
(22, '1', 1),
(23, '1', 3),
(24, '2', 4),
(25, '2', 5);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `sch_id` int(11) NOT NULL,
  `sch_user_id` int(11) NOT NULL,
  `sch_schedule` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '{\r\n  "program": "Bachelor of Science in Mechanical Engineering",\r\n  "semester": "Second Semester SY 2025-2026",\r\n  "instructor": "Engr. Ivan Herbosa",\r\n  "schedule": {\r\n    "days": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],\r\n    "time_slots": [\r\n      {\r\n        "from": "08:00 AM",\r\n        "to": "09:30 AM",\r\n        "classes": {\r\n          "Monday": {\r\n            "subject": "Thermodynamics",\r\n            "code": "ME 201",\r\n            "room": "ME-301"\r\n          },\r\n          "Wednesday": {\r\n            "subject": "Thermodynamics",\r\n            "code": "ME 201",\r\n            "room": "ME-301"\r\n          }\r\n        }\r\n      },\r\n      {\r\n        "from": "10:00 AM",\r\n        "to": "11:30 AM",\r\n        "classes": {\r\n          "Tuesday": {\r\n            "subject": "Fluid Mechanics",\r\n            "code": "ME 202",\r\n            "room": "ME-302"\r\n          },\r\n          "Thursday": {\r\n            "subject": "Fluid Mechanics",\r\n            "code": "ME 202",\r\n            "room": "ME-302"\r\n   ' CHECK (json_valid(`sch_schedule`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`sch_id`, `sch_user_id`, `sch_schedule`) VALUES
(3, 2, '{\"program\":\"dawd\",\"semester\":\"sefsef\",\"instructor\":\"rgrdg\",\"schedule\":{\"Monday\":{\"16:15-16:15\":\"0001\"}}}');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(60) NOT NULL,
  `subject_name` varchar(60) NOT NULL,
  `subject_unit` varchar(60) NOT NULL,
  `subject_type` enum('Major','Minor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `subject_unit`, `subject_type`) VALUES
(1, '0001', 'Subject 1', '2', 'Minor'),
(3, '0002', 'subject 2', '2', 'Major'),
(4, '0003', 'subject 3', '3', 'Major'),
(5, '0004', 'subject 4', '2', 'Major'),
(6, '0004', 'subject 4', '88', 'Major'),
(7, 'qwert1', 'subject 4', '66', 'Minor');

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
(2, 'juans', 'juan@gmail.com', '$2y$10$0MvyJd3XGWnNcmV63Qo.fezf8EDwhhVlMn0SSeS8L33EGsHb5qvv.', 'faculty', 'juanzz', '', 'dela cruz', 1),
(3, 'programchair', 'programchair@gmail.com', '$2y$10$y3ZrKlwGOky8Gzuq8Ls3Q.izCFCw2zwx6LN5UiCYTLONlX1pOGwcm', 'program chair', 'programchair', '', 'padilla', 1),
(4, 'test123', 'test1@gmail.com', '$2y$10$B2hrChQFiaIWOnR//NKaV.1zJgQEWTTTXENOy5HQZaDAd9nXt3rNC', 'faculty', 'faculty', '', 'padilla', 0),
(5, 'gec', 'gec@gmail.com', '$2y$10$sGzxS0LpYOAXhAG3ARONsuydKb2lzq55A84R8nSAODuDpbf90kyoO', 'gec', 'gec', 'gec', 'gec', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`sch_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `sch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `curriculum`
--
ALTER TABLE `curriculum`
  ADD CONSTRAINT `curriculum_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
