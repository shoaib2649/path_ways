-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 10:34 AM
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
-- Database: `path_way`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `repeat_type` varchar(255) DEFAULT NULL,
  `start` datetime DEFAULT NULL,
  `end` datetime DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `color_primary` varchar(255) DEFAULT NULL,
  `color_secondary` varchar(255) DEFAULT NULL,
  `actions` text DEFAULT NULL,
  `all_day` tinyint(1) NOT NULL DEFAULT 0,
  `resizable_before_start` tinyint(1) NOT NULL DEFAULT 0,
  `resizable_after_end` tinyint(1) NOT NULL DEFAULT 0,
  `draggable` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `provider_id`, `type`, `location`, `repeat_type`, `start`, `end`, `title`, `description`, `color_primary`, `color_secondary`, `actions`, `all_day`, `resizable_before_start`, `resizable_after_end`, `draggable`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL, NULL, '2025-05-15 10:00:00', '2025-05-15 11:00:00', 'Dental Checkup', 'Routine dental checkup and cleaning.', '#2196f3', '#bbdefb', '{\"edit\": true, \"delete\": true}', 0, 1, 1, 1, '2025-05-12 06:41:09', '2025-05-12 06:41:09');

-- --------------------------------------------------------

--
-- Table structure for table `billings`
--

CREATE TABLE `billings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `meeting_type` varchar(255) DEFAULT NULL,
  `time` int(10) UNSIGNED DEFAULT NULL,
  `amount` decimal(8,2) DEFAULT NULL,
  `rate` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `encounter_sections`
--

CREATE TABLE `encounter_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `facility_id` bigint(20) UNSIGNED DEFAULT NULL,
  `encounter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `chief_complaint` text DEFAULT NULL,
  `history` text DEFAULT NULL,
  `medical_history` text DEFAULT NULL,
  `surgical_history` text DEFAULT NULL,
  `family_history` text DEFAULT NULL,
  `social_history` text DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `review_of_systems` text DEFAULT NULL,
  `physical_exam` text DEFAULT NULL,
  `vital_sign` text DEFAULT NULL,
  `assessments` text DEFAULT NULL,
  `procedure` text DEFAULT NULL,
  `follow_up` text DEFAULT NULL,
  `json_dump` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `provider_id`, `title`, `description`, `start_time`, `end_time`, `location`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'General Checkup Day', 'Dr. Ahmed\'s full-day .', '2025-05-20 04:00:00', '2025-05-20 12:00:00', 'HealthCare Clinic, Room 101', 'active', '2025-05-12 06:39:37', '2025-05-12 06:39:37');

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `contact_information` text DEFAULT NULL,
  `facility_type` enum('Male','Female','Co-Ed Facility') DEFAULT NULL,
  `facility_capacity` text DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_patients`
--

CREATE TABLE `insurance_patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `insurance_provider_id` bigint(20) UNSIGNED NOT NULL,
  `patient_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `insurance_providers`
--

CREATE TABLE `insurance_providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `website_url` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `list_options`
--

CREATE TABLE `list_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `list_type` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `sequence` varchar(255) DEFAULT NULL,
  `is_default` varchar(255) DEFAULT NULL,
  `option_value` varchar(255) DEFAULT NULL,
  `mapping` varchar(255) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `codes` varchar(255) DEFAULT NULL,
  `toggle_setting_1` varchar(255) DEFAULT NULL,
  `toggle_setting_2` varchar(255) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `subtype` varchar(255) DEFAULT NULL,
  `edit_options` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `list_options`
--

INSERT INTO `list_options` (`id`, `list_type`, `slug`, `title`, `sequence`, `is_default`, `option_value`, `mapping`, `notes`, `codes`, `toggle_setting_1`, `toggle_setting_2`, `activity`, `subtype`, `edit_options`, `created_at`, `updated_at`) VALUES
(1, 'location', 'person-appt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-12 06:38:35', '2025-05-12 06:38:35'),
(2, 'location', 'telehealth-appt', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-12 06:38:35', '2025-05-12 06:38:35'),
(3, 'location', 'unassigned', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-12 06:38:35', '2025-05-12 06:38:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_03_12_050829_create_admins_table', 1),
(5, '2025_03_14_121831_create_providers_table', 1),
(6, '2025_03_17_104039_create_provider_exceptions_table', 1),
(7, '2025_04_24_042515_create_personal_access_tokens_table', 1),
(8, '2025_04_24_110847_create_patients_table', 1),
(9, '2025_04_24_121115_create_roles_table', 1),
(10, '2025_04_25_042941_create_services_table', 1),
(11, '2025_04_25_042956_create_provider_team_members_table', 1),
(12, '2025_04_27_073424_create_list_options_table', 1),
(13, '2025_04_27_172519_create_appointments_table', 1),
(14, '2025_04_28_072728_create_billings_table', 1),
(15, '2025_04_28_082844_create_insurance_providers_table', 1),
(16, '2025_04_28_083128_create_insurance_patients_table', 1),
(17, '2025_04_28_111610_create_facilities_table', 1),
(18, '2025_04_28_113819_create_patient_encounters_table', 1),
(19, '2025_04_28_114024_create_encounter_sections_table', 1),
(20, '2025_05_02_053910_create_provider_availabilities_table', 1),
(21, '2025_05_09_134119_create_provider_availability_service_table', 1),
(22, '2025_05_12_094924_create_events_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mrn` varchar(20) DEFAULT NULL,
  `mr` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `social_security_number` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `individual_appointments` varchar(255) DEFAULT NULL,
  `group_appointments` varchar(255) DEFAULT NULL,
  `blood_score` varchar(255) DEFAULT '0',
  `lifestyle_score` varchar(255) DEFAULT '0',
  `supplement_medication_score` varchar(255) DEFAULT '0',
  `physical_vital_sign_score` varchar(255) DEFAULT '0',
  `image` text DEFAULT NULL,
  `module_level` varchar(255) DEFAULT '0',
  `referred_by` varchar(255) DEFAULT NULL,
  `wait_list` varchar(255) NOT NULL DEFAULT 'y',
  `qualification` text DEFAULT NULL,
  `provider_name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `mrn`, `mr`, `user_id`, `provider_id`, `suffix`, `social_security_number`, `type`, `individual_appointments`, `group_appointments`, `blood_score`, `lifestyle_score`, `supplement_medication_score`, `physical_vital_sign_score`, `image`, `module_level`, `referred_by`, `wait_list`, `qualification`, `provider_name`, `status`, `location`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 3, 1, 'MD', '123-45-6789', NULL, '1', '1', '90', '85', '88', '92', 'https://example.com/images/provider.jpg', 'advanced', NULL, 'y', 'MBBS, FCPS', 'Prime Care Center', 'active', 'Lahore, Clinic Block B', '2025-05-12 06:38:47', '2025-05-12 06:38:47');

-- --------------------------------------------------------

--
-- Table structure for table `patient_encounters`
--

CREATE TABLE `patient_encounters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED DEFAULT NULL,
  `patient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `facility_id` bigint(20) UNSIGNED DEFAULT NULL,
  `speciality_id` bigint(20) UNSIGNED DEFAULT NULL,
  `encounter_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `encounter_date` date DEFAULT NULL,
  `visit_reason` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `speciality` varchar(255) DEFAULT NULL,
  `encounter_type` varchar(255) DEFAULT NULL,
  `encounter_status` enum('active','inactive','draft','signed') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `specialization` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `license_expiry_date` date DEFAULT NULL,
  `experience_years` varchar(255) DEFAULT NULL,
  `education` text DEFAULT NULL,
  `certifications` text DEFAULT NULL,
  `clinic_name` varchar(255) DEFAULT NULL,
  `clinic_address` varchar(255) DEFAULT NULL,
  `available_days` varchar(255) DEFAULT NULL,
  `available_time` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `doctor_notes` text DEFAULT NULL,
  `consultation_fee` varchar(255) DEFAULT NULL,
  `profile_slug` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `admin_id`, `user_id`, `specialization`, `license_number`, `license_expiry_date`, `experience_years`, `education`, `certifications`, `clinic_name`, `clinic_address`, `available_days`, `available_time`, `is_verified`, `doctor_notes`, `consultation_fee`, `profile_slug`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 'Cardiology', 'CARD98765', '2031-12-31', '15', 'MBBS, FCPS (Cardiology)', 'ACLS, BLS, Advanced Cardiac Care', 'Heart Wellness Clinic', '456 Heart Lane, Lahore', 'Monday, Wednesday, Friday', '10:00 AM - 4:00 PM', 1, 'Specializing in heart health for over a decade.', '2000', 'dr-sarah-l-ahmed', '2025-05-12 06:38:43', '2025-05-12 06:38:43');

-- --------------------------------------------------------

--
-- Table structure for table `provider_availabilities`
--

CREATE TABLE `provider_availabilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `day_of_week` varchar(255) DEFAULT NULL,
  `slots` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`slots`)),
  `type` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `recurrence` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider_availability_service`
--

CREATE TABLE `provider_availability_service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_availability_id` bigint(20) UNSIGNED NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider_exceptions`
--

CREATE TABLE `provider_exceptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `day_off` tinyint(1) NOT NULL DEFAULT 0,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `provider_team_members`
--

CREATE TABLE `provider_team_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `provider_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `assigned_at` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `price` decimal(8,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `requires_approval` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `prefix` varchar(10) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `user_role` varchar(255) DEFAULT NULL,
  `subscribe_status` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `first_login_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `social_media` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `prefix`, `first_name`, `middle_name`, `last_name`, `full_name`, `name`, `email`, `google_id`, `email_verified_at`, `password`, `user_role`, `subscribe_status`, `phone`, `address`, `state`, `postal_code`, `country`, `date_of_birth`, `gender`, `age`, `is_active`, `first_login_at`, `last_login_at`, `profile_image`, `bio`, `social_media`, `city`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, 'Asad Test', 'asad25@gmail.com', NULL, NULL, '$2y$12$5UXxZVdIXLt2779aguJiiuCnYerblfSu5xejRV5A3t8jFeZNQRfzu', 'provider', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-12 06:38:35', '2025-05-12 06:38:35'),
(2, 'Dr.', 'Sarah', 'L', 'Ahmed', 'Dr. Sarah L Ahmed', NULL, 'sarah.ahmed@example.com', NULL, NULL, '$2y$12$TBBp85KYggLiAdvelGZ2ueUhll.clnSCEigXkmwP0eHAUy8sKwQo2', 'provider', 'active', '+11234567890', '123 Main Street', 'Punjab', '54000', 'Pakistan', '1985-07-20', 'female', 39, 1, NULL, NULL, 'https://yourdomain.com/images/sarah.jpg', 'Senior consultant with 15 years of experience.', '@drsarah', 'Lahore', NULL, '2025-05-12 06:38:43', '2025-05-12 06:38:43'),
(3, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$12$uMPGzRdtrXNJdDQzjmLWTOJwohZpxrn5S/iu8tKqotKol817ONzJm', 'patient', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-05-12 06:38:47', '2025-05-12 06:38:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointments_patient_id_foreign` (`patient_id`),
  ADD KEY `appointments_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `billings`
--
ALTER TABLE `billings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billings_patient_id_foreign` (`patient_id`),
  ADD KEY `billings_provider_id_foreign` (`provider_id`),
  ADD KEY `billings_appointment_id_foreign` (`appointment_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `encounter_sections`
--
ALTER TABLE `encounter_sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `encounter_sections_provider_id_foreign` (`provider_id`),
  ADD KEY `encounter_sections_patient_id_foreign` (`patient_id`),
  ADD KEY `encounter_sections_facility_id_foreign` (`facility_id`),
  ADD KEY `encounter_sections_encounter_id_foreign` (`encounter_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `events_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `insurance_patients`
--
ALTER TABLE `insurance_patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `insurance_patients_insurance_provider_id_foreign` (`insurance_provider_id`),
  ADD KEY `insurance_patients_patient_id_foreign` (`patient_id`);

--
-- Indexes for table `insurance_providers`
--
ALTER TABLE `insurance_providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `list_options`
--
ALTER TABLE `list_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `list_options_slug_list_type_unique` (`slug`,`list_type`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patients_mrn_unique` (`mrn`),
  ADD KEY `patients_user_id_foreign` (`user_id`),
  ADD KEY `patients_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `patient_encounters`
--
ALTER TABLE `patient_encounters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_encounters_provider_id_foreign` (`provider_id`),
  ADD KEY `patient_encounters_patient_id_foreign` (`patient_id`),
  ADD KEY `patient_encounters_facility_id_foreign` (`facility_id`),
  ADD KEY `patient_encounters_speciality_id_foreign` (`speciality_id`),
  ADD KEY `patient_encounters_encounter_type_id_foreign` (`encounter_type_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `providers_profile_slug_unique` (`profile_slug`),
  ADD KEY `providers_admin_id_foreign` (`admin_id`),
  ADD KEY `providers_user_id_foreign` (`user_id`);

--
-- Indexes for table `provider_availabilities`
--
ALTER TABLE `provider_availabilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_availabilities_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `provider_availability_service`
--
ALTER TABLE `provider_availability_service`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_availability_service_provider_availability_id_foreign` (`provider_availability_id`),
  ADD KEY `provider_availability_service_service_id_foreign` (`service_id`);

--
-- Indexes for table `provider_exceptions`
--
ALTER TABLE `provider_exceptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_exceptions_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `provider_team_members`
--
ALTER TABLE `provider_team_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `provider_team_members_provider_id_foreign` (`provider_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_email_index` (`email`),
  ADD KEY `users_user_role_index` (`user_role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `billings`
--
ALTER TABLE `billings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `encounter_sections`
--
ALTER TABLE `encounter_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `insurance_patients`
--
ALTER TABLE `insurance_patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `insurance_providers`
--
ALTER TABLE `insurance_providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `list_options`
--
ALTER TABLE `list_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_encounters`
--
ALTER TABLE `patient_encounters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `provider_availabilities`
--
ALTER TABLE `provider_availabilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provider_availability_service`
--
ALTER TABLE `provider_availability_service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provider_exceptions`
--
ALTER TABLE `provider_exceptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `provider_team_members`
--
ALTER TABLE `provider_team_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billings`
--
ALTER TABLE `billings`
  ADD CONSTRAINT `billings_appointment_id_foreign` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billings_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billings_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `encounter_sections`
--
ALTER TABLE `encounter_sections`
  ADD CONSTRAINT `encounter_sections_encounter_id_foreign` FOREIGN KEY (`encounter_id`) REFERENCES `patient_encounters` (`id`),
  ADD CONSTRAINT `encounter_sections_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`),
  ADD CONSTRAINT `encounter_sections_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `encounter_sections_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `insurance_patients`
--
ALTER TABLE `insurance_patients`
  ADD CONSTRAINT `insurance_patients_insurance_provider_id_foreign` FOREIGN KEY (`insurance_provider_id`) REFERENCES `insurance_providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `insurance_patients_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patient_encounters`
--
ALTER TABLE `patient_encounters`
  ADD CONSTRAINT `patient_encounters_encounter_type_id_foreign` FOREIGN KEY (`encounter_type_id`) REFERENCES `list_options` (`id`),
  ADD CONSTRAINT `patient_encounters_facility_id_foreign` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`id`),
  ADD CONSTRAINT `patient_encounters_patient_id_foreign` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_encounters_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patient_encounters_speciality_id_foreign` FOREIGN KEY (`speciality_id`) REFERENCES `list_options` (`id`);

--
-- Constraints for table `providers`
--
ALTER TABLE `providers`
  ADD CONSTRAINT `providers_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `admins` (`id`),
  ADD CONSTRAINT `providers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `provider_availabilities`
--
ALTER TABLE `provider_availabilities`
  ADD CONSTRAINT `provider_availabilities_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_availability_service`
--
ALTER TABLE `provider_availability_service`
  ADD CONSTRAINT `provider_availability_service_provider_availability_id_foreign` FOREIGN KEY (`provider_availability_id`) REFERENCES `provider_availabilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `provider_availability_service_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_exceptions`
--
ALTER TABLE `provider_exceptions`
  ADD CONSTRAINT `provider_exceptions_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `provider_team_members`
--
ALTER TABLE `provider_team_members`
  ADD CONSTRAINT `provider_team_members_provider_id_foreign` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
