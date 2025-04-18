-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 12:19 PM
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
-- Database: `fms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `table_affected` varchar(50) NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `action`, `table_affected`, `record_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'create', 'users', 11, NULL, '{\"first_name\":\"New\",\"last_name\":\"User\",\"email\":\"new.user@university.edu\"}', '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0)', '2023-05-01 03:45:00'),
(2, 2, 'update', 'faculty', 3, '{\"office_hours\":\"Tue/Thu 2pm-4pm\"}', '{\"office_hours\":\"Tue/Thu 1pm-3pm\"}', '192.168.1.150', 'Mozilla/5.0 (Macintosh)', '2023-05-02 09:00:00'),
(3, 21, 'INSERT', 'evaluations', 5, NULL, '{\"faculty_id\":\"4\",\"academic_year\":\"2024-2025\",\"semester\":\"fall\"}', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-05 09:29:15'),
(4, 21, 'INSERT', 'faculty', 22, NULL, '{\"department_id\":\"4\",\"position\":\"Faculty\",\"rank\":\"assistant\",\"hire_date\":\"2025-04-01\",\"tenure_status\":\"tenured\",\"bio\":\"dsd\",\"office_location\":\"BLock-34\"}', '::1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', '2025-04-05 09:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) NOT NULL,
  `description` text DEFAULT NULL,
  `head_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`department_id`, `name`, `code`, `description`, `head_id`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', 'CS', 'Department of Computer Science', 2, '2022-01-10 02:30:00', '2022-06-15 04:30:00'),
(2, 'Mathematics', 'MATH', 'Department of Mathematics', 8, '2022-01-10 02:30:00', '2022-07-20 05:30:00'),
(3, 'Physics', 'PHYS', 'Department of Physics', NULL, '2022-01-10 02:30:00', '2022-01-10 02:30:00'),
(4, 'Biology', 'BIO', 'Department of Biological Sciences', NULL, '2022-01-10 02:30:00', '2022-01-10 02:30:00'),
(5, 'Chemistry', 'CHEM', 'Department of Chemistry', NULL, '2022-01-10 02:30:00', '2022-01-10 02:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `development_plans`
--

CREATE TABLE `development_plans` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `goals` text DEFAULT NULL,
  `timeline` varchar(255) DEFAULT NULL,
  `status` enum('draft','in_progress','completed','cancelled') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `development_plans`
--

INSERT INTO `development_plans` (`id`, `faculty_id`, `title`, `description`, `goals`, `timeline`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 3, 'Machine Learning Research Initiative', 'Plan to expand research in machine learning applications', 'Publish 2 papers, secure grant funding, develop new course', '2023-2024 academic year', 'in_progress', 2, '2023-01-20 04:30:00', '2023-05-01 05:30:00'),
(2, 4, 'Algebraic Geometry Textbook', 'Develop a textbook for advanced algebraic geometry course', 'Complete first draft, get peer reviews, submit to publisher', '2023-2025', 'in_progress', 8, '2023-02-15 08:30:00', '2023-04-20 09:30:00'),
(3, 6, 'Quantum Computing Lab', 'Establish a new quantum computing research lab', 'Secure funding, purchase equipment, hire research assistants', '2023-2026', 'draft', 5, '2023-03-10 05:30:00', '2023-03-10 05:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `evaluations`
--

CREATE TABLE `evaluations` (
  `evaluation_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `evaluator_id` int(11) NOT NULL,
  `academic_year` varchar(9) NOT NULL,
  `semester` enum('fall','spring','summer') NOT NULL,
  `status` enum('draft','submitted','reviewed','approved') DEFAULT 'draft',
  `overall_score` decimal(5,2) DEFAULT NULL,
  `comments` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluations`
--

INSERT INTO `evaluations` (`evaluation_id`, `faculty_id`, `evaluator_id`, `academic_year`, `semester`, `status`, `overall_score`, `comments`, `created_at`, `submitted_at`, `reviewed_at`, `approved_at`) VALUES
(1, 3, 2, '2022-2023', 'fall', 'approved', 4.25, 'Excellent performance in teaching and research', '2022-12-15 03:30:00', '2022-12-20 10:00:00', '2023-01-10 11:00:00', '2023-01-15 14:00:00'),
(2, 4, 8, '2022-2023', 'fall', 'reviewed', 3.75, 'Good performance with room for improvement in research output', '2022-12-16 04:30:00', '2022-12-21 11:00:00', '2023-01-11 10:00:00', NULL),
(3, 6, 5, '2022-2023', 'spring', 'submitted', NULL, 'Evaluation submitted for review', '2023-05-01 03:30:00', '2023-05-05 10:00:00', NULL, NULL),
(4, 7, 10, '2022-2023', 'spring', 'draft', NULL, 'Evaluation in progress', '2023-05-10 08:30:00', NULL, NULL, NULL),
(5, 4, 21, '2024-2025', 'fall', 'draft', 1.40, '', '2025-04-05 09:29:15', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_metrics`
--

CREATE TABLE `evaluation_metrics` (
  `metric_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `performance_metric_id` int(11) NOT NULL,
  `score` decimal(3,2) DEFAULT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_metrics`
--

INSERT INTO `evaluation_metrics` (`metric_id`, `evaluation_id`, `performance_metric_id`, `score`, `comments`) VALUES
(1, 1, 1, 4.50, 'Exceptional student feedback and innovative teaching methods'),
(2, 1, 2, 4.25, 'Published 3 journal articles and secured a research grant'),
(3, 1, 3, 4.00, 'Active participation in curriculum committee'),
(4, 1, 4, 3.75, 'Participated in university accreditation process'),
(5, 1, 5, 4.50, 'Attended two international conferences'),
(6, 2, 1, 3.50, 'Good teaching evaluations with some areas for improvement'),
(7, 2, 2, 3.25, 'Published 1 journal article, working on grant proposal'),
(8, 2, 3, 4.50, 'Excellent service as undergraduate advisor'),
(9, 2, 4, 3.00, 'Limited university service this year'),
(10, 2, 5, 4.00, 'Attended one national conference');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_scores`
--

CREATE TABLE `evaluation_scores` (
  `score_id` int(11) NOT NULL,
  `evaluation_id` int(11) NOT NULL,
  `metric_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_scores`
--

INSERT INTO `evaluation_scores` (`score_id`, `evaluation_id`, `metric_id`, `score`, `comments`) VALUES
(1, 1, 1, 4.50, 'Teaching evaluations were excellent'),
(2, 1, 2, 4.25, 'Strong research output'),
(3, 1, 3, 4.00, 'Good departmental service'),
(4, 1, 4, 3.75, 'Participated in university committees'),
(5, 1, 5, 4.50, 'Active in professional development'),
(6, 2, 1, 3.50, 'Solid teaching performance'),
(7, 2, 2, 3.25, 'Adequate research output'),
(8, 2, 3, 4.50, 'Exceptional departmental service'),
(9, 2, 4, 3.00, 'Limited university service'),
(10, 2, 5, 4.00, 'Good professional development'),
(11, 5, 1, 2.00, ''),
(12, 5, 2, 1.00, ''),
(13, 5, 3, 2.00, ''),
(14, 5, 4, 1.00, ''),
(15, 5, 5, 1.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `title`, `description`, `start_date`, `end_date`, `location`, `created_by`, `created_at`) VALUES
(1, 'Faculty Development Workshop', 'Annual workshop on teaching methodologies', '2023-08-15 09:00:00', '2023-08-16 16:00:00', 'University Conference Center', 1, '2023-04-10 04:30:00'),
(2, 'Research Symposium', 'Showcase of faculty and student research projects', '2023-10-20 10:00:00', '2023-10-21 15:00:00', 'Science Building Atrium', 2, '2023-04-15 05:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `faculty_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `position` varchar(100) NOT NULL,
  `rank` enum('assistant','associate','full','emeritus') NOT NULL,
  `hire_date` date NOT NULL,
  `tenure_status` enum('tenured','tenure_track','non_tenure') NOT NULL,
  `bio` text DEFAULT NULL,
  `office_location` varchar(50) DEFAULT NULL,
  `office_hours` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`faculty_id`, `department_id`, `position`, `rank`, `hire_date`, `tenure_status`, `bio`, `office_location`, `office_hours`) VALUES
(2, 1, 'Professor and Department Chair', 'full', '2010-08-15', 'tenured', 'Specializes in Artificial Intelligence and Machine Learning', 'CS-101', 'Mon/Wed 10am-12pm'),
(3, 1, 'Associate Professor', 'associate', '2015-08-20', 'tenure_track', 'Focuses on Database Systems and Distributed Computing', 'CS-205', 'Tue/Thu 1pm-3pm'),
(4, 2, 'Assistant Professor', 'assistant', '2018-08-15', 'tenure_track', 'Research interests in Algebraic Geometry', 'MATH-312', 'Mon/Wed 2pm-4pm'),
(6, 3, 'Professor', 'full', '2008-08-15', 'tenured', 'Specializes in Quantum Mechanics', 'PHYS-101', 'Tue/Thu 10am-12pm'),
(7, 4, 'Associate Professor', 'associate', '2014-08-15', 'tenured', 'Focuses on Molecular Biology', 'BIO-215', 'Mon/Wed 1pm-3pm'),
(8, 2, 'Professor and Department Chair', 'full', '2009-08-15', 'tenured', 'Research interests in Number Theory', 'MATH-101', 'Tue/Thu 2pm-4pm'),
(9, 5, 'Assistant Professor', 'assistant', '2020-08-15', 'tenure_track', 'Specializes in Organic Chemistry', 'CHEM-308', 'Fri 10am-2pm'),
(22, 4, 'Faculty', 'assistant', '2025-04-01', 'tenured', 'dsd', 'BLock-34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `notification_type` varchar(50) DEFAULT 'general',
  `related_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `recipient_id`, `sender_id`, `subject`, `message`, `notification_type`, `related_id`, `is_read`, `created_at`) VALUES
(1, 3, 2, 'Evaluation Approved', 'Your annual evaluation has been approved with an overall score of 4.25', 'evaluation', 1, 1, '2023-01-15 09:00:00'),
(2, 4, 8, 'Promotion Review', 'Your promotion request has moved to under review status', 'promotion', 1, 0, '2023-04-02 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `performance_metrics`
--

CREATE TABLE `performance_metrics` (
  `metric_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `weight` decimal(5,2) NOT NULL DEFAULT 1.00,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_metrics`
--

INSERT INTO `performance_metrics` (`metric_id`, `name`, `description`, `weight`, `is_active`, `created_at`) VALUES
(1, 'Teaching Effectiveness', 'Evaluation of teaching performance based on student feedback and peer reviews', 0.40, 1, '2022-01-10 02:30:00'),
(2, 'Research Productivity', 'Quantity and quality of research publications and grants', 0.30, 1, '2022-01-10 02:30:00'),
(3, 'Service to Department', 'Committee work and departmental responsibilities', 0.15, 1, '2022-01-10 02:30:00'),
(4, 'Service to University', 'University-wide committee work and initiatives', 0.10, 1, '2022-01-10 02:30:00'),
(5, 'Professional Development', 'Continuing education and professional activities', 0.05, 1, '2022-01-10 02:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `promotion_requests`
--

CREATE TABLE `promotion_requests` (
  `id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `current_rank` varchar(50) NOT NULL,
  `requested_rank` varchar(50) NOT NULL,
  `justification` text DEFAULT NULL,
  `status` enum('pending','under_review','approved','denied') DEFAULT 'pending',
  `reviewer_id` int(11) DEFAULT NULL,
  `review_comments` text DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotion_requests`
--

INSERT INTO `promotion_requests` (`id`, `faculty_id`, `current_rank`, `requested_rank`, `justification`, `status`, `reviewer_id`, `review_comments`, `submission_date`) VALUES
(1, 4, 'assistant', 'associate', 'Published 5 peer-reviewed papers, excellent teaching evaluations, led curriculum revision committee', 'under_review', 5, 'Strong case, needs external review letters', '2023-04-01 04:30:00'),
(2, 7, 'associate', 'full', 'Established research lab, secured NIH grant, department service for 5 years', 'pending', NULL, NULL, '2023-05-01 08:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `task_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`task_id`, `title`, `description`, `assigned_to`, `assigned_by`, `due_date`, `status`, `created_at`) VALUES
(1, 'Prepare Accreditation Report', 'Compile data for CS program accreditation review', 3, 2, '2023-06-15', 'in_progress', '2023-04-01 04:30:00'),
(2, 'Review Promotion Materials', 'Evaluate promotion packet for Assistant Professor', 5, 8, '2023-05-30', 'pending', '2023-04-15 08:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `salt` varchar(32) NOT NULL,
  `role` enum('admin','department_head','faculty','reviewer') NOT NULL,
  `account_status` enum('active','suspended','pending') DEFAULT 'pending',
  `last_login` datetime DEFAULT NULL,
  `failed_login_attempts` int(11) DEFAULT 0,
  `password_reset_token` varchar(64) DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `password_hash`, `salt`, `role`, `account_status`, `last_login`, `failed_login_attempts`, `password_reset_token`, `password_reset_expires`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Smith', 'john.smith@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt123', 'admin', 'active', '2023-05-15 09:30:00', 0, NULL, NULL, '2022-01-10 02:30:00', '2023-05-15 04:00:00'),
(2, 'Sarah', 'Johnson', 'sarah.johnson@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt456', 'department_head', 'active', '2023-05-14 14:15:00', 0, NULL, NULL, '2022-01-10 02:30:00', '2023-05-14 08:45:00'),
(3, 'Michael', 'Williams', 'michael.williams@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt789', 'faculty', 'active', '2023-05-14 10:45:00', 0, NULL, NULL, '2022-01-15 02:30:00', '2023-05-14 05:15:00'),
(4, 'Emily', 'Brown', 'emily.brown@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt012', 'faculty', 'active', '2023-05-13 16:20:00', 0, NULL, NULL, '2022-02-01 02:30:00', '2023-05-13 10:50:00'),
(5, 'David', 'Jones', 'david.jones@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt345', 'reviewer', 'active', '2023-05-12 11:10:00', 0, NULL, NULL, '2022-02-10 02:30:00', '2023-05-12 05:40:00'),
(6, 'Jennifer', 'Garcia', 'jennifer.garcia@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt678', 'faculty', 'active', '2023-05-11 13:25:00', 0, NULL, NULL, '2022-03-05 02:30:00', '2023-05-11 07:55:00'),
(7, 'Robert', 'Miller', 'robert.miller@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt901', 'faculty', 'active', '2023-05-10 09:15:00', 0, NULL, NULL, '2022-03-15 02:30:00', '2023-05-10 03:45:00'),
(8, 'Jessica', 'Davis', 'jessica.davis@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt234', 'department_head', 'active', '2023-05-09 15:30:00', 0, NULL, NULL, '2022-04-01 02:30:00', '2023-05-09 10:00:00'),
(9, 'Thomas', 'Rodriguez', 'thomas.rodriguez@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt567', 'faculty', 'active', '2023-05-08 10:20:00', 0, NULL, NULL, '2022-04-10 02:30:00', '2023-05-08 04:50:00'),
(10, 'Elizabeth', 'Martinez', 'elizabeth.martinez@university.edu', '$2a$10$xJwL5v5zJz6Z6Z6Z6Z6Z6e', 'somesalt890', 'reviewer', 'active', '2023-05-07 14:45:00', 0, NULL, NULL, '2022-05-01 02:30:00', '2023-05-07 09:15:00'),
(21, 'Admin', 'User2', 'admin2@university.edu', '$2y$10$c.orl5TD/4f.pfvcZbKweO30DoT49anMH2pzW3gwLTlimMlxAFxLS', 'c0d97fa6cba9816cf5f4923628074b97', 'admin', 'active', '2025-04-05 14:57:33', 0, NULL, NULL, '2025-04-05 09:27:26', '2025-04-05 09:27:33'),
(22, 'test', 'hah', 'testfaculty@university.edu', '$2y$10$1vaT3X3tUoB1CATxhlPpOel6LcnwJINsUu7UfUkbqfPr8zQYuVXkG', 'b01a556b6161e7111af73c6f96a384ee', 'faculty', 'active', NULL, 0, NULL, NULL, '2025-04-05 09:36:44', '2025-04-05 09:36:44');

-- --------------------------------------------------------

--
-- Table structure for table `workshops`
--

CREATE TABLE `workshops` (
  `workshop_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `max_participants` int(11) NOT NULL DEFAULT 20,
  `facilitator` varchar(255) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('scheduled','in_progress','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workshops`
--

INSERT INTO `workshops` (`workshop_id`, `title`, `description`, `start_date`, `end_date`, `location`, `max_participants`, `facilitator`, `department_id`, `created_by`, `status`, `created_at`) VALUES
(1, 'Grant Writing Workshop', 'Learn effective strategies for securing research funding', '2023-06-10 10:00:00', '2023-06-10 15:00:00', 'Library Room 203', 25, 'Dr. Smith from NSF', 1, 2, 'scheduled', '2023-04-20 05:30:00'),
(2, 'Active Learning Techniques', 'Workshop on implementing active learning in the classroom', '2023-07-15 09:00:00', '2023-07-15 12:00:00', 'Education Building 101', 30, 'Prof. Johnson', NULL, 1, 'scheduled', '2023-05-01 03:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `workshop_registrations`
--

CREATE TABLE `workshop_registrations` (
  `registration_id` int(11) NOT NULL,
  `workshop_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('confirmed','waitlisted','cancelled') DEFAULT 'confirmed',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `workshop_registrations`
--

INSERT INTO `workshop_registrations` (`registration_id`, `workshop_id`, `user_id`, `registration_date`, `status`, `notes`) VALUES
(1, 1, 3, '2023-04-25 04:30:00', 'confirmed', 'Interested in NSF funding opportunities'),
(2, 1, 4, '2023-04-26 05:30:00', 'confirmed', NULL),
(3, 2, 6, '2023-05-02 03:30:00', 'confirmed', 'Will attend with graduate students');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_action` (`action`),
  ADD KEY `idx_audit_table` (`table_affected`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`department_id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `head_id` (`head_id`),
  ADD KEY `idx_department_code` (`code`);

--
-- Indexes for table `development_plans`
--
ALTER TABLE `development_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD PRIMARY KEY (`evaluation_id`),
  ADD KEY `idx_faculty_evaluation` (`faculty_id`,`academic_year`,`semester`),
  ADD KEY `idx_evaluator` (`evaluator_id`);

--
-- Indexes for table `evaluation_metrics`
--
ALTER TABLE `evaluation_metrics`
  ADD PRIMARY KEY (`metric_id`),
  ADD KEY `evaluation_id` (`evaluation_id`),
  ADD KEY `performance_metric_id` (`performance_metric_id`);

--
-- Indexes for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD PRIMARY KEY (`score_id`),
  ADD UNIQUE KEY `uk_evaluation_metric` (`evaluation_id`,`metric_id`),
  ADD KEY `metric_id` (`metric_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`faculty_id`),
  ADD KEY `idx_department` (`department_id`),
  ADD KEY `idx_rank` (`rank`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `recipient_id` (`recipient_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `performance_metrics`
--
ALTER TABLE `performance_metrics`
  ADD PRIMARY KEY (`metric_id`);

--
-- Indexes for table `promotion_requests`
--
ALTER TABLE `promotion_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`task_id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `workshops`
--
ALTER TABLE `workshops`
  ADD PRIMARY KEY (`workshop_id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `workshop_registrations`
--
ALTER TABLE `workshop_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `workshop_id` (`workshop_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `development_plans`
--
ALTER TABLE `development_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `evaluations`
--
ALTER TABLE `evaluations`
  MODIFY `evaluation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `evaluation_metrics`
--
ALTER TABLE `evaluation_metrics`
  MODIFY `metric_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  MODIFY `score_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `performance_metrics`
--
ALTER TABLE `performance_metrics`
  MODIFY `metric_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `promotion_requests`
--
ALTER TABLE `promotion_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `task_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `workshops`
--
ALTER TABLE `workshops`
  MODIFY `workshop_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `workshop_registrations`
--
ALTER TABLE `workshop_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`head_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL;

--
-- Constraints for table `development_plans`
--
ALTER TABLE `development_plans`
  ADD CONSTRAINT `development_plans_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `development_plans_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `evaluations`
--
ALTER TABLE `evaluations`
  ADD CONSTRAINT `evaluations_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`),
  ADD CONSTRAINT `evaluations_ibfk_2` FOREIGN KEY (`evaluator_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `evaluation_metrics`
--
ALTER TABLE `evaluation_metrics`
  ADD CONSTRAINT `evaluation_metrics_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`evaluation_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evaluation_metrics_ibfk_2` FOREIGN KEY (`performance_metric_id`) REFERENCES `performance_metrics` (`metric_id`) ON UPDATE CASCADE;

--
-- Constraints for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD CONSTRAINT `evaluation_scores_ibfk_1` FOREIGN KEY (`evaluation_id`) REFERENCES `evaluations` (`evaluation_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_scores_ibfk_2` FOREIGN KEY (`metric_id`) REFERENCES `performance_metrics` (`metric_id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `faculty`
--
ALTER TABLE `faculty`
  ADD CONSTRAINT `faculty_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faculty_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `promotion_requests`
--
ALTER TABLE `promotion_requests`
  ADD CONSTRAINT `promotion_requests_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculty` (`faculty_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `promotion_requests_ibfk_2` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `workshops`
--
ALTER TABLE `workshops`
  ADD CONSTRAINT `workshops_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`department_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `workshops_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `workshop_registrations`
--
ALTER TABLE `workshop_registrations`
  ADD CONSTRAINT `workshop_registrations_ibfk_1` FOREIGN KEY (`workshop_id`) REFERENCES `workshops` (`workshop_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `workshop_registrations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
