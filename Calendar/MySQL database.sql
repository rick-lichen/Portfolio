-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 06, 2020 at 01:06 AM
-- Server version: 5.6.47
-- PHP Version: 7.0.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `module_5`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `Event_Name` varchar(300) DEFAULT NULL,
  `Event_ID` mediumint(9) NOT NULL,
  `User_ID` smallint(9) DEFAULT NULL,
  `Date_Created` datetime DEFAULT NULL,
  `Date_Due` datetime DEFAULT NULL,
  `Description` text NOT NULL,
  `Tag` char(100) DEFAULT NULL,
  `Created_ID` smallint(9) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`Event_Name`, `Event_ID`, `User_ID`, `Date_Created`, `Date_Due`, `Description`, `Tag`, `Created_ID`) VALUES
('grading', 100, 14, '2020-04-15 23:24:14', '2020-04-15 00:12:00', 'I cannot', 'tag_work,', 14),
('test group events', 99, 14, '2020-03-29 23:43:59', '2020-03-02 12:30:00', 'group events', 'tag_work,', 38),
('test group events', 98, 38, '2020-03-29 23:43:59', '2020-03-02 12:30:00', 'group events', 'tag_work,', 38),
('Test Event', 97, 38, '2020-03-29 23:43:08', '2020-04-01 02:03:00', 'Hi!', 'tag_work,', 38),
('shared event', 96, 14, '2020-03-29 23:39:25', '2020-04-19 14:02:00', 'This event was created by Rick, but shared with hihi', NULL, 37),
('shared event', 95, 37, '2020-03-29 23:39:24', '2020-04-19 14:02:00', 'This event was created by Rick, but shared with hihi', NULL, 37),
('Rick\'s Birthday', 93, 37, '2020-03-29 23:36:25', '2020-04-22 00:00:00', 'This is Rick\'s birthday!', 'tag_ent,', 37),
('Rick\'s Birthday', 92, 14, '2020-03-29 00:04:33', '2020-04-22 00:00:00', 'This is Rick\'s birthday!\n\n(This event has been shared with Rick, which can be accessed through username: rick, password: rick)', 'tag_ent,', 14),
('TEST', 102, 14, '2020-04-15 23:42:40', '2020-04-30 00:12:00', 'test', 'tag_ent,', 14),
('TEST', 101, 39, '2020-04-15 23:29:10', '2020-04-30 00:12:00', 'test', 'tag_ent,', 39),
('April Fool\'s Day', 90, 14, '2020-03-29 00:03:33', '2020-04-01 02:03:00', 'Gotcha!', 'tag_work,', 14);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `pass`) VALUES
(14, 'hihi', '$2y$10$bcLA9/L2HYKyx7pGmla7h.yyEK1XRRZz1DRsKhBMogotwPwrJ0Qmy'),
(37, 'rick', '$2y$10$6X5W3xPSG0NkuoAMsThlReqjZlleZc3SpWA9i/zQ5oaMwnzyiXYPu'),
(38, 'hihi2', '$2y$10$aYFbmClSFfSrGVrF/5ADA.s8OI5GLze1q.wOGgqdfsFOVsGVA9n2q'),
(39, 'ta', '$2y$10$FjgfEm52AoTmUn7nzfJK2.VBLJhUTtsz83dRJLGS/EsneTUR5P.oi');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`Event_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Created_ID` (`Created_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`) USING HASH;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `Event_ID` mediumint(9) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
