-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 26, 2017 at 11:28 AM
-- Server version: 5.7.17-log
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `als`
--

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `level` int(100) NOT NULL,
  `name` varchar(99) NOT NULL,
  `permissions` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `levels`
--

INSERT INTO `levels` (`level`, `name`, `permissions`) VALUES
(10, 'Developer', 'user_*|create_post|can_edit|can_post|make_payments|set_paymentType'),
(20, 'Cashier', 'manage_time|view_user_*');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `field` text NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`field`, `value`) VALUES
('site_name', 'MST Script'),
('site_url', 'localhost/als/demo/'),
('site_email', 'support@lovemst.com'),
('site_enabled', '1'),
('site_theme', 'ubold'),
('site_lang', 'en_us'),
('secret_key', '26492513487648721879487365abcd12'),
('login_enable', '1'),
('register_enable', '1'),
('pin_required', '1'),
('activation_required', '0'),
('minimum_age_required', '0'),
('minimum_age', '18'),
('username_change', '1'),
('force_https', '0'),
('captcha_key', '6LdZCdMSAAAAABvQVvo3iiloXRLGz1AclntNjCoL'),
('captcha_secret', '6LdZCdMSAAAAAHxCDRKUivqC-oWdAimu1KM1pwPH'),
('same_ip_login', '1'),
('max_verified_devices', '10'),
('twilio_auth_token', '313e801bd481d2ea8ce2f389b1b798e8'),
('twilio_account_sid', 'AC8dfc0b431ff49da20e57dcbaa626546e'),
('twilio_phone_number', '+18183346907'),
('site_timezone', 'America/Los_Angeles'),
('templates_folder', 'templates'),
('site_path', 'D:\\AppServ\\www\\ALS\\demo'),
('loading_timestamp', '1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) DEFAULT NULL,
  `username` varchar(45) DEFAULT NULL,
  `firstName` varchar(45) DEFAULT NULL,
  `lastName` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `level` int(10) DEFAULT NULL,
  `password` mediumtext,
  `date_joined` varchar(45) DEFAULT NULL,
  `last_login` varchar(45) DEFAULT NULL,
  `expire` int(11) DEFAULT NULL,
  `token` text,
  `reset_code` varchar(45) DEFAULT NULL,
  `pin_number` varchar(45) DEFAULT NULL,
  `banned` tinyint(1) DEFAULT NULL,
  `activated` tinyint(1) DEFAULT NULL,
  `activation_code` varchar(45) DEFAULT NULL,
  `xp` int(100) DEFAULT NULL,
  `xp_lost` int(100) DEFAULT NULL,
  `has_doubleXP` tinyint(1) NOT NULL,
  `doubleXP_until` text NOT NULL,
  `must_signin_again` tinyint(1) NOT NULL,
  `devices` longtext NOT NULL,
  `twoFactor_enabled` tinyint(1) NOT NULL,
  `verification_code` text NOT NULL,
  `lastLogin_ip` text NOT NULL,
  `birth_date` text NOT NULL,
  `preferred_language` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `firstName`, `lastName`, `email`, `level`, `password`, `date_joined`, `last_login`, `expire`, `token`, `reset_code`, `pin_number`, `banned`, `activated`, `activation_code`, `xp`, `xp_lost`, `has_doubleXP`, `doubleXP_until`, `must_signin_again`, `devices`, `twoFactor_enabled`, `verification_code`, `lastLogin_ip`, `birth_date`, `preferred_language`) VALUES
(1, 'lovemst', 'Masis', 'Bajaqejian', 'mstbchakgyan@gmail.com', 100, '$2y$12$zlEc4R36XZ/heIzLtyAGveKFZZ8iSAPQki5V5Sc2FNow3jlQLjJhK', '2017-02-25 22:38:30', '2017-06-23 14:08:49', 0, '', '', '1f5940a5270e13c43b2a366afba14a85', 0, 1, '0', 750, 900000, 0, '', 0, 'a:5:{i:0;a:4:{s:2:\"ip\";s:3:\"::1\";s:7:\"browser\";s:6:\"Chrome\";s:7:\"version\";s:12:\"56.0.2924.87\";s:2:\"os\";s:7:\"Windows\";}i:1;a:4:{s:2:\"ip\";s:11:\"192.168.1.3\";s:7:\"browser\";s:6:\"Chrome\";s:7:\"version\";s:12:\"56.0.2924.87\";s:2:\"os\";s:7:\"Android\";}i:2;a:4:{s:2:\"ip\";s:3:\"::1\";s:7:\"browser\";s:6:\"Chrome\";s:7:\"version\";s:13:\"57.0.2987.133\";s:2:\"os\";s:7:\"Windows\";}i:3;a:4:{s:2:\"ip\";s:3:\"::1\";s:7:\"browser\";s:6:\"Chrome\";s:7:\"version\";s:13:\"58.0.3029.110\";s:2:\"os\";s:7:\"Windows\";}i:4;a:4:{s:2:\"ip\";s:3:\"::1\";s:7:\"browser\";s:7:\"Firefox\";s:7:\"version\";s:4:\"53.0\";s:2:\"os\";s:7:\"Windows\";}}', 0, '', 'f528764d624db129b32c21fbca0cb8d6', '09/13/1996', 'en_us'),
(2, 'masis96', 'Masis', 'Bajaqejian', 'masisbchakgyan1@gmail.com', 10, '98cca24fec032041db6ce7abcdc6427f', '2017-03-08 04:05:54', '2017-03-08 04:05:54', 0, '', '0', '1f5940a5270e13c43b2a366afba14a85', 0, 1, '0', 405, NULL, 0, '', 0, '', 0, '', '', '', '');
