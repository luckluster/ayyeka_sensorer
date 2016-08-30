-- phpMyAdmin SQL Dump
-- version 4.0.10.12
-- http://www.phpmyadmin.net
--
-- Host: 127.5.194.2:3306
-- Generation Time: Aug 30, 2016 at 10:05 PM
-- Server version: 5.5.50
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ayyeka_sensorer`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_processed`
--

CREATE TABLE IF NOT EXISTS `data_processed` (
  `dp_machine_id` int(11) NOT NULL,
  `dp_grouping_type` tinyint(4) NOT NULL COMMENT 'Average grouping type for this record -  GROUPING_TYPE__x - 1: year; 2: month; 3: day; 4: hour',
  `dp_grouping_value` varchar(13) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'The value we group by. For example if dp_grouping_type is 3 and this is 1 then it means Sunday',
  `dp_values_count` int(13) NOT NULL COMMENT 'How many values we grouped by now',
  `dp_value` double NOT NULL COMMENT 'The calculated average value for this grouping type',
  UNIQUE KEY `machine_grouping_index` (`dp_machine_id`,`dp_grouping_type`,`dp_grouping_value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_processed`
--

INSERT INTO `data_processed` (`dp_machine_id`, `dp_grouping_type`, `dp_grouping_value`, `dp_values_count`, `dp_value`) VALUES
(1, 1, '2016', 5, 8.8),
(1, 2, '2016-08', 5, 8.8),
(1, 3, '2016-08-29', 4, 11),
(1, 3, '2016-08-30', 1, 0),
(1, 4, '2016-08-29 23', 4, 11),
(1, 4, '2016-08-30 22', 1, 0),
(2, 1, '2016', 5, 12),
(2, 2, '2016-01', 1, 20),
(2, 2, '2016-08', 4, 10),
(2, 3, '2016-01-01', 1, 20),
(2, 3, '2016-08-29', 4, 10),
(2, 4, '2016-01-01 12', 1, 20),
(2, 4, '2016-08-29 23', 4, 10);

-- --------------------------------------------------------

--
-- Table structure for table `data_raw`
--

CREATE TABLE IF NOT EXISTS `data_raw` (
  `rd_id` int(11) NOT NULL AUTO_INCREMENT,
  `rd_machine_id` int(11) NOT NULL,
  `rd_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rd_value` int(11) NOT NULL,
  PRIMARY KEY (`rd_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Raw data, coming from the machine' AUTO_INCREMENT=39 ;

--
-- Dumping data for table `data_raw`
--

INSERT INTO `data_raw` (`rd_id`, `rd_machine_id`, `rd_timestamp`, `rd_value`) VALUES
(11, 1, '2016-08-30 03:10:49', 20),
(12, 1, '2016-08-30 03:10:54', 20),
(13, 1, '2016-08-30 03:11:01', 20),
(14, 1, '2016-08-30 03:11:06', 20),
(15, 1, '2016-08-30 03:16:05', 20),
(16, 1, '2016-08-30 03:16:43', 20),
(17, 1, '2016-08-30 03:19:22', 20),
(18, 1, '2016-08-30 03:21:28', 20),
(19, 1, '2016-08-30 03:22:15', 20),
(20, 1, '2016-08-30 03:22:58', 20),
(21, 1, '2016-08-30 03:24:02', 20),
(22, 1, '2016-08-30 03:24:21', 20),
(23, 1, '2016-08-30 03:24:36', 20),
(24, 2, '2016-08-30 03:25:17', 10),
(25, 2, '2016-08-30 03:25:37', 10),
(26, 1, '2016-08-30 03:27:01', 4),
(27, 2, '2016-08-30 03:27:20', 2),
(28, 2, '2016-08-30 03:33:07', 10),
(29, 2, '2016-08-30 03:33:16', 10),
(30, 2, '2016-08-30 03:33:46', 10),
(31, 2, '2016-08-30 03:34:02', 10),
(32, 2, '2016-08-30 03:34:09', 10),
(33, 1, '2016-08-30 03:34:52', 4),
(34, 2, '2016-08-30 03:35:09', 4),
(35, 1, '2016-08-30 03:46:39', 16),
(36, 2, '2016-08-30 03:46:53', 16),
(37, 2, '2016-01-01 17:12:12', 20),
(38, 1, '2016-08-31 02:02:53', 0);

-- --------------------------------------------------------

--
-- Table structure for table `machines`
--

CREATE TABLE IF NOT EXISTS `machines` (
  `mcn_id` int(11) NOT NULL AUTO_INCREMENT,
  `mcn_user_id` int(11) NOT NULL COMMENT 'User associated with this machine',
  `mcn_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`mcn_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `machines`
--

INSERT INTO `machines` (`mcn_id`, `mcn_user_id`, `mcn_title`) VALUES
(1, 1, 'Electric company kWh'),
(2, 1, 'Living room thermometer'),
(3, 1, 'Bedroom thermometer'),
(4, 2, 'Cat temperature'),
(5, 2, 'piggywiggy');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `usr_id` int(11) NOT NULL AUTO_INCREMENT,
  `usr_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'md5 password',
  `usr_status` tinyint(4) NOT NULL DEFAULT '2' COMMENT 'USER_STATUS__x: 1: new 2: active 3: banned',
  `usr_note` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`usr_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`usr_id`, `usr_name`, `usr_password`, `usr_status`, `usr_note`, `usr_phone`, `usr_last_login`) VALUES
(1, 'yaron', '202cb962ac59075b964b07152d234b70', 2, '123', '', '2016-08-31 02:01:12'),
(2, 'achinoam', '202cb962ac59075b964b07152d234b70', 2, '', '', '2016-08-31 00:10:11');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
