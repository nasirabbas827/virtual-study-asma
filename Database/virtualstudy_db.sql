-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2024 at 09:13 AM
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
-- Database: `virtualstudy_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `discussionboard`
--

CREATE TABLE `discussionboard` (
  `PostID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `PostedBy` int(11) NOT NULL,
  `Content` text NOT NULL,
  `PostedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discussionboard`
--

INSERT INTO `discussionboard` (`PostID`, `GroupID`, `PostedBy`, `Content`, `PostedAt`) VALUES
(1, 2, 3, 'das', '2024-12-06 06:27:37'),
(2, 2, 3, 'dfaewa', '2024-12-06 06:27:41'),
(3, 3, 3, 'Hello', '2024-12-06 07:23:47');

-- --------------------------------------------------------

--
-- Table structure for table `groupmembers`
--

CREATE TABLE `groupmembers` (
  `MemberID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Role` enum('Leader','Member') DEFAULT 'Member',
  `JoinedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groupmembers`
--

INSERT INTO `groupmembers` (`MemberID`, `GroupID`, `UserID`, `Role`, `JoinedAt`) VALUES
(2, 2, 3, 'Leader', '2024-12-06 05:33:32'),
(3, 3, 3, 'Leader', '2024-12-06 07:19:03'),
(4, 3, 4, 'Leader', '2024-12-06 07:24:03'),
(5, 2, 4, 'Member', '2024-12-06 07:24:17');

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `ResourceID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `UploadedBy` int(11) NOT NULL,
  `ResourceTitle` varchar(255) NOT NULL,
  `FilePath` varchar(255) NOT NULL,
  `Topic` varchar(255) DEFAULT NULL,
  `UploadedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`ResourceID`, `GroupID`, `UploadedBy`, `ResourceTitle`, `FilePath`, `Topic`, `UploadedAt`) VALUES
(1, 2, 3, 'Bio', 'uploads/6752959f472b5.png', 'Topic 1', '2024-12-06 06:11:43'),
(2, 3, 3, 'Bio', 'uploads/6752a66fb370a.png', 'Topic 1', '2024-12-06 07:23:27');

-- --------------------------------------------------------

--
-- Table structure for table `studygroups`
--

CREATE TABLE `studygroups` (
  `GroupID` int(11) NOT NULL,
  `GroupName` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedBy` int(11) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studygroups`
--

INSERT INTO `studygroups` (`GroupID`, `GroupName`, `Description`, `CreatedBy`, `CreatedAt`) VALUES
(2, 'Group 1', 'Groupdas', 3, '2024-12-06 05:33:32'),
(3, 'Group 2', 'dfeda', 3, '2024-12-06 07:19:03');

-- --------------------------------------------------------

--
-- Table structure for table `studysessions`
--

CREATE TABLE `studysessions` (
  `SessionID` int(11) NOT NULL,
  `GroupID` int(11) NOT NULL,
  `SessionTitle` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `SessionDate` date NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `studysessions`
--

INSERT INTO `studysessions` (`SessionID`, `GroupID`, `SessionTitle`, `Description`, `SessionDate`, `StartTime`, `EndTime`) VALUES
(2, 2, 'Updated Session', 'dsafd', '2024-12-10', '2024-12-10 17:22:00', '2024-12-10 02:22:00'),
(3, 2, 'New Session', 'dfa', '2024-12-13', '2024-12-13 11:52:00', '2024-12-13 23:52:00'),
(4, 3, 'New Session', 'dsa', '2025-01-25', '2025-01-25 14:21:00', '2025-01-25 12:25:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `age` int(11) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `phone`, `age`, `full_name`, `bio`, `created_at`) VALUES
(3, 'nasir', '$2y$10$a/SgsGBswoo1YfNrJd./p.Jqey.MDYC.QoLsyJGas3H5cdesUPvjq', 'nasiryt.827@gmail.com', '9853', 23, 'NASIR ABBAS', 'sd', '2024-12-06 12:40:14'),
(4, 'newuser', '$2y$10$Yc4o/da20hrrNpA0dB.qTuFM/KXjAHaPd0y08dw8cJbXMVdn6nvQ6', 'user@gmail.com', '43443', 23, '', NULL, '2024-12-06 12:40:14'),
(5, 'nasiryd', '$2y$10$lkSUouqVUg/bwyuDLC2AueX.dKkPqxFtzQupcb0zv7AhyRFmqu6bW', 'nasiryt.87@gmail.com', '4512', 23, '', NULL, '2024-12-06 12:42:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `discussionboard`
--
ALTER TABLE `discussionboard`
  ADD PRIMARY KEY (`PostID`),
  ADD KEY `GroupID` (`GroupID`),
  ADD KEY `PostedBy` (`PostedBy`);

--
-- Indexes for table `groupmembers`
--
ALTER TABLE `groupmembers`
  ADD PRIMARY KEY (`MemberID`),
  ADD KEY `GroupID` (`GroupID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`ResourceID`),
  ADD KEY `GroupID` (`GroupID`),
  ADD KEY `UploadedBy` (`UploadedBy`);

--
-- Indexes for table `studygroups`
--
ALTER TABLE `studygroups`
  ADD PRIMARY KEY (`GroupID`),
  ADD KEY `CreatedBy` (`CreatedBy`);

--
-- Indexes for table `studysessions`
--
ALTER TABLE `studysessions`
  ADD PRIMARY KEY (`SessionID`),
  ADD KEY `GroupID` (`GroupID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `discussionboard`
--
ALTER TABLE `discussionboard`
  MODIFY `PostID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `groupmembers`
--
ALTER TABLE `groupmembers`
  MODIFY `MemberID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `resources`
--
ALTER TABLE `resources`
  MODIFY `ResourceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `studygroups`
--
ALTER TABLE `studygroups`
  MODIFY `GroupID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `studysessions`
--
ALTER TABLE `studysessions`
  MODIFY `SessionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `discussionboard`
--
ALTER TABLE `discussionboard`
  ADD CONSTRAINT `discussionboard_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `studygroups` (`GroupID`) ON DELETE CASCADE,
  ADD CONSTRAINT `discussionboard_ibfk_2` FOREIGN KEY (`PostedBy`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `groupmembers`
--
ALTER TABLE `groupmembers`
  ADD CONSTRAINT `groupmembers_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `studygroups` (`GroupID`),
  ADD CONSTRAINT `groupmembers_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Constraints for table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `studygroups` (`GroupID`),
  ADD CONSTRAINT `resources_ibfk_2` FOREIGN KEY (`UploadedBy`) REFERENCES `users` (`id`);

--
-- Constraints for table `studygroups`
--
ALTER TABLE `studygroups`
  ADD CONSTRAINT `studygroups_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`id`);

--
-- Constraints for table `studysessions`
--
ALTER TABLE `studysessions`
  ADD CONSTRAINT `studysessions_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `studygroups` (`GroupID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
