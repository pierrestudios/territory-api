-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 14, 2016 at 04:12 AM
-- Server version: 5.6.19-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `territory_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE IF NOT EXISTS `addresses` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `territory_id` int(11) NOT NULL,
  `street_id` int(11) NOT NULL,
  `inactive` tinyint(4) NOT NULL,
  `order` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `addresses_address_street_id_unique` (`address`,`street_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=159 ;

--
-- Dumping data for table `addresses`
--

INSERT INTO `addresses` (`id`, `territory_id`, `street_id`, `inactive`, `order`, `name`, `phone`, `address`, `created_at`, `updated_at`) VALUES
(103, 29, 20, 0, 0, '', '', '475', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(104, 29, 20, 0, 0, '', '', '495', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(105, 29, 20, 0, 0, 'Jacques', '786-267-0224', '555', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(109, 29, 21, 0, 0, 'Madel&egrave;ne/Janito', '305-721-5550', '420', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(110, 29, 21, 0, 0, 'Jn/Vanessa/Valencia', '', '490', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(111, 29, 21, 0, 0, 'Mme Simon Lodane', '', '510', '2016-01-14 00:39:52', '2016-01-13 18:39:52'),
(112, 29, 21, 0, 0, 'St Marc', '305-303-6176', '520', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(113, 29, 21, 0, 0, 'Marl&egrave;ne', '', '540', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(114, 29, 21, 0, 0, 'Johnny/ H&eacute;bert', '305-450-9216', '545', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(115, 29, 21, 1, 0, '', '', '570', '2016-01-14 00:29:05', '2016-01-13 18:29:05'),
(116, 29, 21, 0, 0, 'Yolande/Nancy', '', '575', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(117, 29, 21, 0, 0, 'Escarment', '', '580', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(118, 29, 22, 0, 0, 'Ofina', '305-681-8020', '465', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(119, 29, 22, 0, 0, '', '(chien)', '510', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(120, 29, 22, 1, 0, 'Josiane/Christine', '', '520', '2016-01-14 00:39:30', '2016-01-13 18:39:30'),
(121, 29, 22, 0, 0, 'Edmika', '', '535', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(122, 29, 22, 0, 0, 'Jn Robert', '786-290-9839', '580', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(123, 29, 23, 0, 0, 'Eug&egrave;ne', '', '425', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(124, 29, 23, 0, 0, 'Jean Myrvil/Benisse', '', '430', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(125, 29, 23, 0, 0, 'Guerline', '', '435', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(126, 29, 23, 0, 0, 'Samuel', '', '440', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(127, 29, 23, 0, 0, '', '', '445', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(128, 29, 23, 0, 0, 'Guy', '', '465', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(129, 29, 23, 0, 0, 'Julienne', '(305) 765-7876', '470', '2016-01-14 02:53:27', '2016-01-13 20:53:27'),
(130, 29, 23, 0, 0, '', '', '495', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(131, 29, 23, 0, 0, 'Roseline', '', '510', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(132, 29, 23, 0, 0, 'Mme Jean/ Gilles', '(786) 877-6789', '515', '2016-01-14 02:55:44', '2016-01-13 20:55:44'),
(133, 29, 23, 0, 0, 'Mme. Adam', '', '535', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(134, 29, 23, 0, 0, '', '', '540', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(135, 29, 23, 0, 0, '', '', '560', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(136, 29, 23, 0, 0, '', '', '580', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(137, 29, 23, 0, 0, 'Maurice', '', '590', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(138, 29, 24, 1, 0, 'F&eacute;vrier', '', '400', '2016-01-14 00:45:13', '2016-01-13 18:45:13'),
(139, 29, 24, 0, 0, 'Maryse/L&eacute;onie', '786-286-2742', '410', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(140, 29, 24, 0, 0, '', '', '420', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(141, 29, 24, 0, 0, '', '', '495', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(142, 29, 24, 0, 0, 'Mariela', '', '510', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(143, 29, 24, 0, 0, '', '', '535', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(144, 29, 24, 0, 0, 'Anne-Marie', '', '540', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(145, 29, 24, 0, 0, 'Ben/ Paula', '', '550', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(146, 29, 24, 0, 0, 'Dominique/ Joel', '', '555', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(147, 29, 24, 0, 0, 'Gislaine /Joachim', '', '565', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(148, 29, 24, 0, 0, '', '', '585', '2016-01-13 16:36:19', '2016-01-13 16:36:19'),
(149, 29, 25, 0, 0, 'Jonas', NULL, '12540', '2016-01-13 18:24:50', '2016-01-13 18:24:50'),
(150, 29, 25, 0, 0, 'Gabis', NULL, '12650', '2016-01-13 18:27:09', '2016-01-13 18:27:09'),
(151, 29, 25, 0, 0, 'Sherlie', NULL, '12705', '2016-01-13 18:27:52', '2016-01-13 18:27:52'),
(152, 29, 22, 0, 0, NULL, NULL, '485', '2016-01-13 18:34:13', '2016-01-13 18:34:13'),
(153, 29, 22, 0, 0, NULL, NULL, '505', '2016-01-13 18:39:11', '2016-01-13 18:39:11'),
(154, 29, 22, 0, 0, NULL, NULL, '560', '2016-01-13 18:40:31', '2016-01-13 18:40:31'),
(155, 29, 22, 0, 0, NULL, NULL, '585', '2016-01-13 18:41:04', '2016-01-13 18:41:04'),
(157, 29, 23, 0, 0, 'Simone', NULL, '460', '2016-01-13 18:44:41', '2016-01-13 18:44:41'),
(158, 29, 24, 0, 0, 'Andy / Katia', NULL, '415', '2016-01-13 18:46:04', '2016-01-13 18:46:04');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2014_10_12_000000_create_users_table', 1),
('2014_10_12_100000_create_password_resets_table', 1),
('2015_12_27_230651_create_publishers_table', 2),
('2015_12_29_070932_create_territories_table', 3),
('2015_12_29_070933_create_territories_table', 4),
('2015_12_30_213344_create_addresses_table', 5),
('2015_12_30_221020_create_notes_table', 5),
('2016_01_08_072623_create_streets_table', 6),
('2015_12_30_213345_create_addresses_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `archived` int(11) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `entity` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `content` mediumtext COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `archived`, `entity_id`, `entity`, `date`, `content`, `created_at`, `updated_at`) VALUES
(5, 2, NULL, 4, 'Address', '2015-11-17', 'A', '2016-01-06 02:33:53', '0000-00-00 00:00:00'),
(6, 2, NULL, 4, 'Address', '2015-11-18', 'G,O', '2016-01-06 02:33:50', '0000-00-00 00:00:00'),
(7, 2, NULL, 4, 'Address', '2015-10-23', 'G, O', '2016-01-06 02:33:49', '0000-00-00 00:00:00'),
(8, 2, NULL, 15, 'Address', '2015-11-13', 'A', '2016-01-06 02:33:48', '0000-00-00 00:00:00'),
(9, 2, NULL, 15, 'Address', '2015-11-10', 'G,O', '2016-01-06 02:33:47', '0000-00-00 00:00:00'),
(10, 2, NULL, 15, 'Address', '2015-11-17', 'G, O', '2016-01-06 02:33:46', '0000-00-00 00:00:00'),
(11, 2, NULL, 17, 'Address', '2015-11-18', 'G', '2016-01-06 02:33:44', '0000-00-00 00:00:00'),
(12, 2, NULL, 18, 'Address', '2015-11-25', 'F Luisanne, O', '2016-01-06 02:33:41', '2016-01-02 14:25:20'),
(13, NULL, NULL, 20, 'Address', '2015-11-25', 'F, R', '2016-01-01 15:36:51', '0000-00-00 00:00:00'),
(14, 2, NULL, 21, 'Address', '2015-11-19', 'G, R', '2016-01-06 02:33:54', '0000-00-00 00:00:00'),
(15, NULL, NULL, 22, 'Address', '2015-11-11', 'O, G, F', '2016-01-02 14:47:13', '2016-01-02 08:47:13'),
(16, NULL, NULL, 22, 'Address', '2015-11-20', 'F, CINDY, ADAM WIFE', '2016-01-01 15:38:12', '0000-00-00 00:00:00'),
(18, NULL, NULL, 26, 'Address', '2016-01-14', 'G', '2016-01-01 18:04:11', '2016-01-01 18:04:11'),
(19, 2, NULL, 27, 'Address', NULL, 'Test', '2016-01-06 00:22:46', '2016-01-05 18:22:46'),
(20, NULL, NULL, 30, NULL, '2016-01-12', 'A', '2016-01-02 20:54:48', '2016-01-02 14:54:48'),
(21, 2, NULL, 19, 'Address', '2015-12-24', 'F Lucy Lou', '2016-01-02 21:08:55', '2016-01-02 15:08:55'),
(22, 2, NULL, 32, 'Address', '2016-12-08', 'Test', '2016-01-04 07:40:47', '2016-01-04 07:40:47'),
(23, 6, NULL, 35, 'Address', '2015-12-28', 'Home evening', '2016-01-06 02:58:08', '2016-01-05 20:58:08'),
(24, 2, NULL, 35, 'Address', '2015-12-09', 'A', '2016-01-05 20:55:32', '2016-01-05 20:55:32'),
(25, 6, NULL, 33, 'Address', '2015-12-09', 'test', '2016-01-06 11:55:32', '2016-01-06 11:55:32'),
(26, 6, NULL, 33, 'Address', '2015-00-00', 'test date', '2016-01-06 17:56:26', '2016-01-06 11:56:26'),
(27, 6, NULL, 105, 'Address', '2016-03-02', 'F, O', '2016-01-14 00:19:45', '2016-01-13 18:19:45'),
(28, 2, NULL, 129, 'Address', NULL, 'Diaconese ', '2016-01-13 20:52:00', '2016-01-13 20:52:00'),
(29, 2, NULL, 132, 'Address', NULL, 'Voudou ', '2016-01-13 20:54:46', '2016-01-13 20:54:46');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `password_resets_email_index` (`email`),
  KEY `password_resets_token_index` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publishers`
--

CREATE TABLE IF NOT EXISTS `publishers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` enum('regular','pioneer','overseer') COLLATE utf8_unicode_ci DEFAULT 'regular',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

--
-- Dumping data for table `publishers`
--

INSERT INTO `publishers` (`id`, `user_id`, `first_name`, `last_name`, `type`, `created_at`, `updated_at`) VALUES
(1, 6, 'Kevin', 'Knowles', 'regular', '2016-01-06 02:41:57', '2016-01-05 20:41:57'),
(2, 5, 'Eric', 'Cledanor', 'regular', '2016-01-03 17:21:53', '2016-01-01 12:30:37'),
(3, NULL, 'Reginald', 'Dormeus', 'regular', '2016-01-05 04:06:31', '2016-01-04 22:06:31'),
(4, NULL, 'George', 'Prince', 'regular', '2016-01-02 07:46:49', '2016-01-02 01:46:49'),
(5, NULL, 'Tomas', 'Ernst', 'regular', '2016-01-02 07:28:01', '2016-01-02 01:28:01'),
(6, NULL, 'N9pL7kOFt4', 'HxgVnU5pE5', 'regular', '2015-12-29 13:25:53', '0000-00-00 00:00:00'),
(7, 2, 'Seth', 'Golds', 'regular', '2016-01-04 04:05:29', '2016-01-03 22:05:29'),
(8, NULL, 'Charlie', 'Crist Jr', 'regular', '2016-01-06 02:51:13', '2016-01-05 20:51:13'),
(9, NULL, 'Fred', 'FlintStone', 'regular', '2016-01-04 22:14:24', '2016-01-04 22:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `streets`
--

CREATE TABLE IF NOT EXISTS `streets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_apt_building` tinyint(4) DEFAULT NULL,
  `street` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `streets_street_unique` (`street`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=26 ;

--
-- Dumping data for table `streets`
--

INSERT INTO `streets` (`id`, `is_apt_building`, `street`, `created_at`, `updated_at`) VALUES
(20, 0, 'NW 125 ST', '2016-01-13 16:25:37', '2016-01-13 16:25:37'),
(21, 0, 'NW 126 ST', '2016-01-13 16:26:56', '2016-01-13 16:26:56'),
(22, 0, 'NW 127 ST', '2016-01-13 16:26:56', '2016-01-13 16:26:56'),
(23, 0, 'NW 128 ST', '2016-01-13 16:26:56', '2016-01-13 16:26:56'),
(24, 0, 'NW 129 ST', '2016-01-13 16:26:56', '2016-01-13 16:26:56'),
(25, 0, 'NW 5 Ave', '2016-01-13 18:24:50', '2016-01-13 18:24:50');

-- --------------------------------------------------------

--
-- Table structure for table `territories`
--

CREATE TABLE IF NOT EXISTS `territories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `publisher_id` int(11) DEFAULT NULL,
  `assigned_date` date NOT NULL,
  `number` int(11) DEFAULT NULL,
  `location` mediumtext COLLATE utf8_unicode_ci,
  `boundaries` text COLLATE utf8_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `territories_number_unique` (`number`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

--
-- Dumping data for table `territories`
--

INSERT INTO `territories` (`id`, `publisher_id`, `assigned_date`, `number`, `location`, `boundaries`, `created_at`, `updated_at`) VALUES
(29, 9, '2016-01-11', 1, 'NW 125-128 St / NW 4 - 6 Ave', NULL, '2016-01-14 02:28:14', '2016-01-13 20:28:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `level` tinyint(1) NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `level`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'test322@sitetest.com', '$2y$10$MLo0hSla56YwchHh9dzGKeHSW0PGiBmNExlOOr81vdz6LThwnwjJq', 4, NULL, '2015-12-27 17:50:35', '2015-12-25 17:16:40'),
(5, 'publisher2@test.com', '$2y$10$VkH3o.pYy/SK.6ZIRMVJ2u2Bb8j81Rqxo9uSzUEB8nhqMSR1jcOBS', 2, NULL, '2016-01-05 04:04:56', '2016-01-04 22:04:56'),
(6, 'demo@territory-api.com', '$2y$10$KeBf1/CbN7DG09b0VEGQqeBuHQL9IW3EGzst3vCIi9XBIhYhhYovq', 3, NULL, '2016-01-06 02:42:07', '2016-01-05 20:42:07');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
