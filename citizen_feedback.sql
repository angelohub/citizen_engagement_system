-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2025 at 08:04 PM
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
-- Database: `citizen_feedback`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registration_complete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `registration_complete`) VALUES
(26, 'Ishimwe Didier', '$2y$10$krQCNkyfSL.LYSzH0/0yEuDgEBfWtbbDFE/Fpxpe6Tfp9mypWJSo2', 0);

-- --------------------------------------------------------

--
-- Table structure for table `agencies`
--

CREATE TABLE `agencies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agencies`
--

INSERT INTO `agencies` (`id`, `name`, `category`) VALUES
(7, 'Rwanda National Police', 'Public Safety'),
(8, 'Rwanda Biomedical Center', 'Health Services'),
(9, 'Rwanda Housing Authority', 'Urban Planning'),
(10, 'Rwanda Energy Group', 'Utilities and Energy'),
(11, 'Water and Sanitation Corporation', 'Water and Sanitation');

-- --------------------------------------------------------

--
-- Table structure for table `agency_users`
--

CREATE TABLE `agency_users` (
  `id` int(11) NOT NULL,
  `agency_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agency_users`
--

INSERT INTO `agency_users` (`id`, `agency_id`, `username`, `email`, `password`, `name`, `role`, `created_at`) VALUES
(10, 7, 'sammy', 'sammy@gmail.com', '$2y$10$7UH9V37oC70aET3fj0OGCebIilLtdgdvvEvtOhoaguZyC2rX8nRNe', 'sammy', 'staff', '2025-05-18 11:23:18'),
(11, 8, 'marry', 'marry@gmail.com', '$2y$10$mMxS.1WsamiE.W2bgSeD0.l4uCbtQKIH7iDaNQ9WBkwgRV7GrOewS', 'marry', 'staff', '2025-05-18 11:57:39');

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `status` enum('Pending','In Progress','Resolved') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `title`, `description`, `category`, `status`, `created_at`, `response`) VALUES
(7, 40, 'Noise Disturbance in Nyamirambo at Night', 'I live near the main road in Nyamirambo, and every night after 10 PM, several motorcycle riders and street groups cause extreme noise with loud music, shouting, and reckless riding. This continues late into the night, disturbing our sleep and creating an unsafe environment. I kindly request the police to increase patrols in the area during these hours.', 'public safety', 'Resolved', '2025-05-18 12:38:10', 'To: Tuyishimire Angelo\r\nSubject: Response to Noise Disturbance Complaint in Nyamirambo\r\nDate:18/05/2025\r\n\r\nDear Tuyishimire Angelo,\r\n\r\nThank you for bringing the matter of nightly noise disturbances in Nyamirambo to our attention.\r\n\r\nWe take public safety and quality of life concerns seriously. In response to your complaint, we have initiated the following actions:\r\n\r\nA dedicated patrol team will be assigned to monitor the area during the night hours between 9 PM and 2 AM.\r\n\r\nWe are collaborating with local community leaders and neighborhood watch groups to identify repeat offenders.\r\n\r\nFurther investigations will be carried out to assess whether the disturbances involve violations of public order laws.\r\n\r\nWe appreciate your vigilance and cooperation. Should the issue persist or escalate, please contact our local precinct at 112 or visit the nearest police post.\r\n\r\nSincerely,\r\nInspector Jean Mugisha\r\nPublic Safety Division\r\nRwanda National Police'),
(9, 40, 'Unregulated Housing Construction in Kimironko', 'Several buildings are being constructed along KG 197 Street in Kimironko without visible permits or compliance with zoning regulations. This has led to noise, dust, and blocked drainage. I request the Housing Authority to investigate and enforce building codes for the safety and health of residents', 'Urban planning', 'Pending', '2025-05-18 12:39:00', NULL),
(10, 40, 'Frequent Power Outages in Remera', 'Over the past two weeks, the Remera area has been experiencing power outages nearly every evening between 6 PM and 10 PM. These outages disrupt work and daily life. No official explanation has been provided. I request REG to investigate and address this issue promptly.', 'Utilities and Energy', 'Pending', '2025-05-18 12:39:51', NULL),
(11, 40, ' Irregular Water Supply in Kanombe', 'Residents of Kanombe sector have not received a regular water supply for more than three days now. The tap water is only available between 3 AM and 5 AM, making it difficult for families to access clean water. Please investigate the reason for the disruption and provide a resolution.', 'Water and Sanitation', 'Pending', '2025-05-18 12:40:38', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `complaint_agencies`
--

CREATE TABLE `complaint_agencies` (
  `complaint_id` int(11) NOT NULL,
  `agency_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_agencies`
--

INSERT INTO `complaint_agencies` (`complaint_id`, `agency_id`, `assigned_at`) VALUES
(7, 7, '2025-05-18 12:43:06'),
(9, 9, '2025-05-18 12:42:53'),
(10, 10, '2025-05-18 12:42:31'),
(11, 11, '2025-05-18 12:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `complaint_id`, `message`, `is_read`, `created_at`) VALUES
(16, 40, 7, 'Your complaint titled \"Noise Disturbance in Nyamirambo at Night\" has been updated. Status: Resolved.', 0, '2025-05-18 12:48:39');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(40, 'Tuyishimire Angelo', 'tuyishimireangelo@gmail.com', '$2y$10$0OGYRAcIz2Ret1f0Vaj6vumdusk6YmSzby/.C2G907jB9xMc.AFQu', '2025-05-18 08:28:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `agencies`
--
ALTER TABLE `agencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agency_users`
--
ALTER TABLE `agency_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `agency_id` (`agency_id`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `complaint_agencies`
--
ALTER TABLE `complaint_agencies`
  ADD PRIMARY KEY (`complaint_id`,`agency_id`),
  ADD KEY `agency_id` (`agency_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `notifications_ibfk_2` (`complaint_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `agencies`
--
ALTER TABLE `agencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `agency_users`
--
ALTER TABLE `agency_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agency_users`
--
ALTER TABLE `agency_users`
  ADD CONSTRAINT `agency_users_ibfk_1` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `complaint_agencies`
--
ALTER TABLE `complaint_agencies`
  ADD CONSTRAINT `complaint_agencies_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_agencies_ibfk_2` FOREIGN KEY (`agency_id`) REFERENCES `agencies` (`id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
