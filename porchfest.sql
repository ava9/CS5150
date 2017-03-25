-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 25, 2017 at 02:16 AM
-- Server version: 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `porchfest`
--

-- --------------------------------------------------------

--
-- Table structure for table `porchfests`
--

CREATE TABLE IF NOT EXISTS `porchfests` (
`PorchfestID` int(11) NOT NULL,
  `URL` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Location` varchar(255) NOT NULL,
  `Date` date NOT NULL,
  `Description` text NOT NULL,
  `Deadline` datetime NOT NULL,
  `Published` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `porchfests`
--

INSERT INTO `porchfests` (`PorchfestID`, `URL`, `Name`, `Location`, `Date`, `Description`, `Deadline`, `Published`) VALUES
(1, '', 'Ithaca Porchfest', 'Utica, NY', '2017-03-23', 'Porchfest began in 2007, inspired by some outdoor ukulele playing and a conversation between neighbors Gretchen Hildreth and Lesley Greene. They came up with the idea for it that day and gathered 20 bands to make it happen in September of that year. The number of bands has increased every year since then, with 185 in 2016.', '2017-03-31 10:00:00', 0);

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
MODIFY `PorchfestID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
