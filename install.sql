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
CREATE DATABASE IF NOT EXISTS `stockgame` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `stockgame`;

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
(1810, 1536214217, 67, 10000, ''),
(1811, 1536214315, 67, 10000, '[]'),
(1812, 1541183240, 67, 10000, '[]');

-- --------------------------------------------------------

--
-- Table structure for table `segments`
--

DROP TABLE IF EXISTS `segments`;
CREATE TABLE `segments` (
  `segment_id` int(11) NOT NULL,
  `segment_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `segments`
--

INSERT INTO `segments` (`segment_id`, `segment_name`) VALUES
(6, 'Energy'),
(9, 'Packaging'),
(10, 'Defense'),
(11, 'Commodities'),
(12, 'Electronics'),
(13, 'Automotive');

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

--
-- Dumping data for table `stocks`
--

INSERT INTO `stocks` (`stock_id`, `name`, `code`, `segment_id`, `prospectus`) VALUES
(1, 'United Steel', 'STL', 13, '.'),
(2, 'Cole\'s Coal Co.', 'CCC', 6, '.'),
(3, 'Flotilla Boat Co.', 'FLBC', 10, '.'),
(4, 'Alberta Munitions Ltd.', 'ABM', 10, '.'),
(5, 'Ross Rifle Ltd.', 'RR', 10, '.'),
(6, 'Fibre Wood Paper & Pulp Inc.', 'FWPP', 9, 'Thin, white, and easy to write on! Made from the best wood, by the best people - quality paper for your all writing endeavours! est. 1920.'),
(7, 'Cans For Days Ltd.', 'CFD', 9, 'Cans! What CAN you use them for!? Need a drum set bang on? Use one of our legendary cans with a spatula for some exquisite tunes.'),
(8, 'Hydra Hydro Ltd.', 'HDHD', 6, 'Our, accomplished, brilliantly designed plants produces the cleanest and most powerful energy on the planet, ensuring peace of mind with your electrically powered home. '),
(9, 'Freud Motors', 'FM', 13, 'We build the most beautiful and comfortable cars. The cars are handmade, and therefore have the finest quality. These cars have the newest technology inside them including a radio, a great speaker system and a sunroof! Our new engines are the most efficient in any car so far!'),
(10, 'Foundaries Consolidated', 'FC', 13, 'We take the most prestigious metals and smelt them into the purest of metals available on the market. Our metal is imported from only the highest tier and quality metals available globally. Our mines might go down, but our stock will only go up. By buying our stock your hearts will melt just like our metal, only at Foundries Consolidated.'),
(11, 'RadioFlix Inc.', 'RFX', 12, 'At RadioFlix Inc. we ensure the highest quality radios that have unmatched crisp and full sounds. Listen to all your favourite shows and music without any interruption with our advanced systems. Our product will help you learn to relax and take some time for yourself. Technology is advancing quickly, so make sure you stay on top of it with premium radios from RadioFlix Inc.'),
(12, 'Tubes Electrical Ltd.', 'TUBE', 12, 'From your bathroom night light to your fridge, we power it all. The source of everything electric. Without us, you would live in the dark all day. Who would want that? Tubes Electrical makes your everyday life so much easier by powering up your life. Invest now and money is bound to enter your pocket.'),
(13, 'Coffee', 'CFFE', 11, 'Feel tired this morning? Stayed up all night watching your favourite Christmas movie, with all this cold weather? We got the solution for you. '),
(14, 'Sugar', 'GLYC', 11, 'When you have our white gold you also have endless possibilities. Our white gold will bring sweetness and joy to you and your families. There is plenty to go around! Sugar has been a natural ingredient in our diets for many many years. So get baking!'),
(15, 'Wheat', 'WHT', 11, 'Our golden strands of wheat create the most premium bread imaginable. The possibilities are endless with our wheat. From noodles to cakes to crumpets, our wheat can create it all. Wheat is the best crop to feed every farm animal and every human in the world. The supply is exponential, it takes up the most area than any other crop. The world will soon revolve around wheat, so buy stocks now and enjoy the ride!'),
(16, 'Timber', 'TMBR', 11, 'Have you ever wanted a beautiful dining room table? Well our top quality wood comes from pure rainbow eucalyptus, which means that your table will be attractive and durable. “Oh no! I just spilled coffee on the table” don’t worry the rainbow eucalyptus table is liquid resistant and your drink won\'t leave a stain!');

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

--
-- Dumping data for table `sxdata`
--

INSERT INTO `sxdata` (`id`, `stock_id`, `timestamp`, `price`) VALUES
(817, 1, 1536214315, 0),
(818, 2, 1536214315, 0),
(819, 3, 1536214315, 0),
(820, 4, 1536214315, 0),
(821, 5, 1536214315, 0),
(822, 6, 1536214315, 0),
(823, 7, 1536214315, 0),
(824, 8, 1536214315, 0),
(825, 9, 1536214315, 0),
(826, 10, 1536214315, 0),
(827, 11, 1536214315, 0),
(828, 12, 1536214315, 0),
(829, 13, 1536214315, 0),
(830, 14, 1536214315, 0),
(831, 15, 1536214315, 0),
(832, 16, 1536214315, 0),
(833, 1, 1541183240, 2.03),
(834, 2, 1541183240, 4.09),
(835, 3, 1541183240, 42.5),
(836, 4, 1541183240, 50.01),
(837, 5, 1541183240, 47.61),
(838, 6, 1541183240, 35.9),
(839, 7, 1541183240, 28.88),
(840, 8, 1541183240, 3.79),
(841, 9, 1541183240, 22.78),
(842, 10, 1541183240, 4.8),
(843, 11, 1541183240, 1.75),
(844, 12, 1541183240, 1.95),
(845, 13, 1541183240, 0.82),
(846, 14, 1541183240, 0.5),
(847, 15, 1541183240, 0.32),
(848, 16, 1541183240, 0.98);

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
(67, 'evan.sharp@coastmountainacademy.ca', 'Evan Sharp', 10000, '[]');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1813;
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
  MODIFY `stock_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `sxdata`
--
ALTER TABLE `sxdata`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=849;
--
-- AUTO_INCREMENT for table `trade_history`
--
ALTER TABLE `trade_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=68;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
