-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 02, 2018 at 11:31 AM
-- Server version: 5.7.24-0ubuntu0.16.04.1
-- PHP Version: 7.0.32-3+ubuntu16.04.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stockgame`
--
CREATE DATABASE IF NOT EXISTS `stockgame-closed` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `stockgame-closed`;

-- --------------------------------------------------------

--
-- Table structure for table `portfolio_value_history`
--

DROP TABLE IF EXISTS `portfolio_value_history`;
CREATE TABLE `portfolio_value_history` (
  `id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `bank_balance` float NOT NULL,
  `portfolio` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `portfolio_value_history`
--

INSERT INTO `portfolio_value_history` (`id`, `timestamp`, `user_id`, `bank_balance`, `portfolio`) VALUES
(1, 1536214217, 1, 10000, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `segments`
--

DROP TABLE IF EXISTS `segments`;
CREATE TABLE `segments` (
  `segment_id` int(11) NOT NULL,
  `segment_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `value` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'classroom', '7541425881'),
(3, 'game_active', 'no');

-- --------------------------------------------------------

--
-- Table structure for table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE `stocks` (
  `stock_id` int(11) NOT NULL,
  `name` varchar(164) DEFAULT NULL,
  `code` varchar(4) NOT NULL,
  `segment_id` int(11) DEFAULT NULL,
  `prospectus` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `sxdata`
--

DROP TABLE IF EXISTS `sxdata`;
CREATE TABLE `sxdata` (
  `id` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `price` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `trade_history`
--

DROP TABLE IF EXISTS `trade_history`;
CREATE TABLE `trade_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  `stock_id` int(11) NOT NULL,
  `tx_price` float NOT NULL,
  `tx` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `bank_balance` float NOT NULL,
  `portfolio` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `name`, `bank_balance`, `portfolio`) VALUES
(1, 'evan.sharp@coastmountainacademy.ca', 'Evan Sharp', 10000, '[]');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `portfolio_value_history`
--
ALTER TABLE `portfolio_value_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `segments`
--
ALTER TABLE `segments`
  ADD PRIMARY KEY (`segment_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stocks`
--
ALTER TABLE `stocks`
  ADD PRIMARY KEY (`stock_id`);

--
-- Indexes for table `sxdata`
--
ALTER TABLE `sxdata`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trade_history`
--
ALTER TABLE `trade_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `portfolio_value_history`
--
ALTER TABLE `portfolio_value_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `segments`
--
ALTER TABLE `segments`
  MODIFY `segment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `stocks`
--
ALTER TABLE `stocks`
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `sxdata`
--
ALTER TABLE `sxdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `trade_history`
--
ALTER TABLE `trade_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
