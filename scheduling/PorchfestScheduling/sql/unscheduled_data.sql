-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 07, 2017 at 10:27 PM
-- Server version: 5.6.35
-- PHP Version: 7.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `porchfest`
--

-- --------------------------------------------------------

--
-- Table structure for table `porchfests`
--

CREATE TABLE `porchfests` (
  `PorchfestID` int(11) NOT NULL,
  `URL` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Nickname` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `Description` text NOT NULL,
  `Deadline` date NOT NULL,
  `Published` tinyint(1) NOT NULL,
  `Scheduled` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `porchfests`
--

INSERT INTO `porchfests` (`PorchfestID`, `URL`, `Name`, `Nickname`, `Location`, `Date`, `Description`, `Deadline`, `Published`, `Scheduled`) VALUES
(1, '', 'Ithaca Porchfest', 'ithaca', 'Utica, NY', '2017-03-23', 'Porchfest began in 2007, inspired by some outdoor ukulele playing and a conversation between neighbors Gretchen Hildreth and Lesley Greene. They came up with the idea for it that day and gathered 20 bands to make it happen in September of that year. The number of bands has increased every year since then, with 185 in 2016.', '2017-03-31', 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `porchfests`
--
ALTER TABLE `porchfests`
  ADD PRIMARY KEY (`PorchfestID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `porchfests`
--
ALTER TABLE `porchfests`
  MODIFY `PorchfestID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;