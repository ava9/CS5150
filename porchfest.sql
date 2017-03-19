-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2017 at 11:52 PM
-- Server version: 5.5.25a
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
-- Table structure for table `bandavailabletimes`
--

CREATE TABLE IF NOT EXISTS `bandavailabletimes` (
  `BandID` int(11) NOT NULL,
  `TimeslotID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bandmembers`
--

CREATE TABLE IF NOT EXISTS `bandmembers` (
  `BandID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bands`
--

CREATE TABLE IF NOT EXISTS `bands` (
`BandID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Comment` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `bandstoporchfests`
--

CREATE TABLE IF NOT EXISTS `bandstoporchfests` (
  `BandID` int(11) NOT NULL,
  `PorchfestID` int(11) NOT NULL,
  `PorchLocation` varchar(255) NOT NULL,
  `TimeslotID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `porchfesttimeslots`
--

CREATE TABLE IF NOT EXISTS `porchfesttimeslots` (
`TimeslotID` int(11) NOT NULL,
  `PorchfestID` int(11) NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`UserID` int(11) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `ContactInfo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userstobands`
--

CREATE TABLE IF NOT EXISTS `userstobands` (
  `UserID` int(11) NOT NULL,
  `BandID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `userstoporchfests`
--

CREATE TABLE IF NOT EXISTS `userstoporchfests` (
  `UserID` int(11) NOT NULL,
  `PorchfestID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bandavailabletimes`
--
ALTER TABLE `bandavailabletimes`
 ADD KEY `BandID` (`BandID`,`TimeslotID`), ADD KEY `TimeslotID` (`TimeslotID`);

--
-- Indexes for table `bandmembers`
--
ALTER TABLE `bandmembers`
 ADD KEY `BandID` (`BandID`);

--
-- Indexes for table `bands`
--
ALTER TABLE `bands`
 ADD PRIMARY KEY (`BandID`);

--
-- Indexes for table `bandstoporchfests`
--
ALTER TABLE `bandstoporchfests`
 ADD KEY `BandID` (`BandID`,`PorchfestID`,`TimeslotID`), ADD KEY `PorchfestID` (`PorchfestID`), ADD KEY `TimeslotID` (`TimeslotID`);

--
-- Indexes for table `porchfests`
--
ALTER TABLE `porchfests`
 ADD PRIMARY KEY (`PorchfestID`);

--
-- Indexes for table `porchfesttimeslots`
--
ALTER TABLE `porchfesttimeslots`
 ADD PRIMARY KEY (`TimeslotID`), ADD KEY `PorchfestID` (`PorchfestID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `userstobands`
--
ALTER TABLE `userstobands`
 ADD KEY `UserID` (`UserID`,`BandID`), ADD KEY `BandID` (`BandID`);

--
-- Indexes for table `userstoporchfests`
--
ALTER TABLE `userstoporchfests`
 ADD KEY `UserID` (`UserID`), ADD KEY `PorchfestID` (`PorchfestID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bands`
--
ALTER TABLE `bands`
MODIFY `BandID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `porchfests`
--
ALTER TABLE `porchfests`
MODIFY `PorchfestID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `porchfesttimeslots`
--
ALTER TABLE `porchfesttimeslots`
MODIFY `TimeslotID` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bandavailabletimes`
--
ALTER TABLE `bandavailabletimes`
ADD CONSTRAINT `bandavailabletimes_ibfk_2` FOREIGN KEY (`TimeslotID`) REFERENCES `porchfesttimeslots` (`TimeslotID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bandavailabletimes_ibfk_1` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bandmembers`
--
ALTER TABLE `bandmembers`
ADD CONSTRAINT `bandmembers_ibfk_1` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bandstoporchfests`
--
ALTER TABLE `bandstoporchfests`
ADD CONSTRAINT `bandstoporchfests_ibfk_3` FOREIGN KEY (`TimeslotID`) REFERENCES `porchfesttimeslots` (`TimeslotID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bandstoporchfests_ibfk_1` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bandstoporchfests_ibfk_2` FOREIGN KEY (`PorchfestID`) REFERENCES `porchfests` (`PorchfestID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userstobands`
--
ALTER TABLE `userstobands`
ADD CONSTRAINT `userstobands_ibfk_2` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `userstobands_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userstoporchfests`
--
ALTER TABLE `userstoporchfests`
ADD CONSTRAINT `userstoporchfests_ibfk_2` FOREIGN KEY (`PorchfestID`) REFERENCES `porchfests` (`PorchfestID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `userstoporchfests_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
