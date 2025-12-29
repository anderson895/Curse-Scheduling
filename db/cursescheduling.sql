-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 29, 2025 at 12:59 PM
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
  `program` varchar(100) NOT NULL,
  `curriculum_year` varchar(20) NOT NULL,
  `year_level` varchar(20) NOT NULL,
  `semester` enum('1st','2nd','Summer') NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(255) NOT NULL,
  `lec_hours` decimal(4,1) DEFAULT 0.0,
  `lab_hours` decimal(4,1) DEFAULT 0.0,
  `lec_units` decimal(4,1) DEFAULT 0.0,
  `lab_units` decimal(4,1) DEFAULT 0.0,
  `prerequisite` varchar(255) DEFAULT NULL,
  `program_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `curriculum`
--

INSERT INTO `curriculum` (`curriculum_id`, `program`, `curriculum_year`, `year_level`, `semester`, `subject_code`, `subject_name`, `lec_hours`, `lab_hours`, `lec_units`, `lab_units`, `prerequisite`, `program_id`) VALUES
(11, 'BSCoE', '2000-2001', '99', '1st', 'CHEM 103', 'General and Inorganic Chemistry 1 - Lec', 3.0, 0.0, 3.0, 0.0, '', NULL),
(12, 'BSCoE', '2000-2001', '1', '1st', 'CHEM 103L', 'General and Inorganic Chemistry 1 - Laboratory', 0.0, 3.0, 0.0, 1.0, '', NULL),
(13, 'BSCoE', '2000-2001', '1', '1st', 'DRAW 111', 'Engineering Drawing 1', 0.0, 3.0, 0.0, 1.0, '', NULL),
(14, 'BSCoE', '2000-2001', '1', '1st', 'ENG 002', 'English Plus', 2.0, 0.0, 2.0, 0.0, NULL, NULL),
(15, 'BSCoE', '2000-2001', '1', '1st', 'FCL 100', 'The Perpetualite: A Helper of God', 3.0, 0.0, 3.0, 0.0, NULL, NULL),
(16, 'BSCoE', '2000-2001', '1', '1st', 'MAT 104', 'Plane and Spherical Trigonometry', 3.0, 0.0, 3.0, 0.0, NULL, NULL),
(17, 'BSCoE', '2000-2001', '1', '1st', 'MAT 114', 'College Algebra', 4.0, 0.0, 4.0, 0.0, NULL, NULL),
(18, 'BSCoE', '2000-2001', '1', '1st', 'NSTP100', 'National Service Training Program', 0.0, 0.0, 3.0, 0.0, NULL, NULL),
(19, 'BSCoE', '2000-2001', '1', '1st', 'PE.101', 'Physical Fitness', 0.0, 2.0, 0.0, 2.0, NULL, NULL),
(20, 'BSCoE', '2000-2001', '1', '1st', 'SOC300', 'Fundamentals of Logic', 3.0, 0.0, 3.0, 0.0, '', NULL),
(21, 'BSCoE', '2000-2001', '1', '2nd', 'CHEM 202', 'General and Inorganic Chemistry 2 - Lec', 3.0, 0.0, 3.0, 0.0, 'CHEM 103', NULL),
(3741, 'BSIE', '2024-2025', '', '1st', '', '', 0.0, 0.0, 0.0, 0.0, NULL, NULL);

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
(15, 2, '{\"program\":\"BS Computer Engineering (BSCoE)\",\"semester\":\"1st Sem SY 2025-2026\",\"schedule\":{\"Monday\":[{\"subject\":\"CHEM 103\",\"hours\":0.5,\"time\":{\"from\":\"07:00\",\"to\":\"07:30\"}}]}}'),
(21, 6, '{\"program\":\"BS Computer Engineering (BSCoE)\",\"semester\":\"2nd Sem SY 2025-2026\",\"schedule\":{\"Monday\":[{\"subject\":\"PSY100\",\"hours\":4,\"time\":{\"from\":\"07:00\",\"to\":\"11:00\"}},{\"subject\":\"PSY100\",\"hours\":4,\"time\":{\"from\":\"13:00\",\"to\":\"17:00\"}},{\"subject\":\"PSY100\",\"hours\":4,\"time\":{\"from\":\"17:00\",\"to\":\"21:00\"}},{\"subject\":\"FCL.200\",\"hours\":0.5,\"time\":{\"from\":\"11:00\",\"to\":\"11:30\"}}],\"Wednesday\":[{\"subject\":\"ENG 100\",\"hours\":2,\"time\":{\"from\":\"07:00\",\"to\":\"09:00\"}}]}}'),
(22, 5, '{\"program\":\"BS Computer Engineering (BSCoE)\",\"semester\":\"1st Sem SY 2025-2026\",\"schedule\":{\"Monday\":[{\"subject\":\"CHEM 103\",\"hours\":1.5,\"time\":{\"from\":\"07:00\",\"to\":\"08:30\"}}]}}');

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
(2, 'juans', 'juan@gmail.com', '$2a$12$Hdw2vDAREGbgE0KIMB8a8OOCLV80oU0ukM9pnnzgkdEGGB3HsC4NC', 'faculty', 'juanzz', '', 'dela cruz', 1),
(3, 'programchair', 'programchair@gmail.com', '$2y$10$y3ZrKlwGOky8Gzuq8Ls3Q.izCFCw2zwx6LN5UiCYTLONlX1pOGwcm', 'program chair', 'programchair', '', 'padilla', 1),
(4, 'test123', 'test1@gmail.com', '$2y$10$B2hrChQFiaIWOnR//NKaV.1zJgQEWTTTXENOy5HQZaDAd9nXt3rNC', 'faculty', 'faculty', '', 'padilla', 0),
(5, 'gec', 'gec@gmail.com', '$2y$10$sGzxS0LpYOAXhAG3ARONsuydKb2lzq55A84R8nSAODuDpbf90kyoO', 'gec', 'gec', 'gec', 'gec', 1),
(6, 'annkirsten', 'annkirsten@gmail.com', '$2y$10$7MIl4IOjbwLg13nJWdl11eeow6qXBVI21FILoZvyb2HvKQR/DY8DO', 'faculty', 'ann kirsten', '', 'dela cruz', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`sch_id`),
  ADD KEY `sch_user_id` (`sch_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `sch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`sch_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
