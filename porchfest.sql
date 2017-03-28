-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2017 at 05:59 AM
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

--
-- Dumping data for table `bandavailabletimes`
--

INSERT INTO `bandavailabletimes` (`BandID`, `TimeslotID`) VALUES
(18, 1),
(1, 2),
(18, 2),
(18, 3),
(15, 4),
(17, 4),
(18, 4),
(1, 5),
(15, 5),
(17, 5),
(18, 5),
(18, 6);

-- --------------------------------------------------------

--
-- Table structure for table `bands`
--

CREATE TABLE IF NOT EXISTS `bands` (
`BandID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `Members` varchar(255) NOT NULL,
  `Comment` varchar(255) NOT NULL,
  `Conflicts` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bands`
--

INSERT INTO `bands` (`BandID`, `Name`, `Description`, `Members`, `Comment`, `Conflicts`) VALUES
(1, '18 Strings of Luv New', 'new description2', '', 'comment me', ''),
(2, 'The Accords', 'The Accords is five voices and no instruments. Two men and three women singing popular songs from the 50s and 60s in street corner style.', '', '', ''),
(3, 'Arthur B and The Planetary Mix', 'Original Old School New School Future School R n B! Arthur Bakert, Karen Wyatte, Angie Beeler, Snowy LaJoie, and Corey Kunzman are dedicated to bringing on the good vibes!', '', '', ''),
(4, 'Auntie Ukulele Showcase', 'We showcase the ukulele in combos from solo to ensemble, and welcome players from Ithaca Ukes, Hickey''s Uke Circle and the Kendal Uke Players. With guest appearance by the Hula Hut Polynesian Dancers.', '', '', ''),
(5, 'Acoustic Rust', 'Acoustic Rust is made up of guitar from Bugs and Kat, Henry on the stand up bass and Steve rocking the kit with a blend of new and old folk, rock and indie music, as well as enough Neil Young tunes to stay true to the rust. Tightly blended harmonies and s', '', '', ''),
(6, 'Ageless Jazz Band', 'We''re the Ageless Jazz Band, a CSMA jazz ensemble under the expert direction of saxophonist Nick Pauldine. The band includes a full complement of saxophones, trumpets, and trombones, a swinging rhythm section and sizzling vocals. Performing the legendary ', '', '', ''),
(7, 'Alan Rose', 'Alan Rose is the songwriter over the 12-String Edge! Sometimes the leader of the 9-piece band Alan Rose and the Restless Elements, he will be appearing solo at this year''s Porchfest. His songs will stick with you while you wander the neighborhood.', '', '', ''),
(9, 'Alex Specker and Friends', 'Alex Specker on guitar, Harry Aceto on bass, and a guest or two, playing an instrumental mix of swing, latin jazz, blues/R&B. Danceable and ''sit-down listenable.''', '', '', ''),
(10, 'Alt-Ac Quartet', 'Alt-Ac is a string quartet made up of graduate students Elisabeth Strayer, Hao Shi, Walter Fu, and Stephen Kim. Since late 2015, they have been playing both pop covers and classical music together, ranging from Beethoven to Beyonce.', '', '', ''),
(11, 'Ann Warde', 'Beautiful Sounds: Sonatas and Interludes for Prepared Piano (1946-48), by John Cage, performed by Ann Warde. The piano preparations (nuts and bolts between the strings) create unexpectedly inviting, gentle buzzes, bells, resonant gongs, and extended piano', '', '', ''),
(12, 'Anna OConnell', 'Anna O''Connell presents inventive folk-pop on her harp, accompanied by luscious strings that bring to life an exciting tonal world.', '', '', ''),
(13, 'Testband', 'testband', '', '', ''),
(14, 'Testband2', 'testband2', '', '', ''),
(15, 'testband3', 'testband4', '', '', ''),
(16, 'finaltest', 'finaltest', '', 'doiwork', 'Band5,band6'),
(17, 'testingme', 'testingme', '', 'commentsallday', 'conflict1,conflict2'),
(18, 'multiple', '', '', '', ''),
(19, 'hello2', '', '', '', ''),
(20, 'hello555555555', '', '', '', '');

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

--
-- Dumping data for table `bandstoporchfests`
--

INSERT INTO `bandstoporchfests` (`BandID`, `PorchfestID`, `PorchLocation`, `TimeslotID`) VALUES
(1, 1, '516 West State Street, Ithaca, NY, United States', NULL),
(2, 1, '', NULL),
(3, 1, '', NULL),
(4, 1, '', NULL),
(5, 1, '', NULL),
(6, 1, '', NULL),
(7, 1, '', NULL),
(9, 1, '', NULL),
(10, 1, '', NULL),
(11, 1, '', NULL),
(12, 1, '', NULL),
(17, 1, 'testingme', NULL),
(18, 1, '', NULL),
(19, 1, '', NULL),
(20, 1, '', NULL);

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

-- --------------------------------------------------------

--
-- Table structure for table `porchfesttimeslots`
--

CREATE TABLE IF NOT EXISTS `porchfesttimeslots` (
`TimeslotID` int(11) NOT NULL,
  `PorchfestID` int(11) NOT NULL,
  `StartTime` datetime NOT NULL,
  `EndTime` datetime NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `porchfesttimeslots`
--

INSERT INTO `porchfesttimeslots` (`TimeslotID`, `PorchfestID`, `StartTime`, `EndTime`) VALUES
(1, 1, '2017-03-23 12:30:00', '2017-03-22 13:30:00'),
(2, 1, '2017-03-23 13:30:00', '2017-03-23 14:30:00'),
(3, 1, '2017-03-23 10:30:00', '2017-03-23 11:30:00'),
(4, 1, '2017-03-23 09:30:00', '2017-03-23 10:30:00'),
(5, 1, '2017-03-23 08:30:00', '2017-03-23 09:30:00'),
(6, 1, '2017-03-23 11:30:00', '2017-03-23 12:30:00');

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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Email`, `Password`, `Name`, `ContactInfo`) VALUES
(1, 'user@porchfest.com', 'porchfest', 'porchfest', 'porchfest');

-- --------------------------------------------------------

--
-- Table structure for table `userstobands`
--

CREATE TABLE IF NOT EXISTS `userstobands` (
  `UserID` int(11) NOT NULL,
  `BandID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userstobands`
--

INSERT INTO `userstobands` (`UserID`, `BandID`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `userstoporchfests`
--

CREATE TABLE IF NOT EXISTS `userstoporchfests` (
  `UserID` int(11) NOT NULL,
  `PorchfestID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `userstoporchfests`
--

INSERT INTO `userstoporchfests` (`UserID`, `PorchfestID`) VALUES
(1, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bandavailabletimes`
--
ALTER TABLE `bandavailabletimes`
 ADD PRIMARY KEY (`BandID`,`TimeslotID`), ADD KEY `BandID` (`BandID`,`TimeslotID`), ADD KEY `TimeslotID` (`TimeslotID`);

--
-- Indexes for table `bands`
--
ALTER TABLE `bands`
 ADD PRIMARY KEY (`BandID`);

--
-- Indexes for table `bandstoporchfests`
--
ALTER TABLE `bandstoporchfests`
 ADD PRIMARY KEY (`BandID`,`PorchfestID`), ADD KEY `BandID` (`BandID`,`PorchfestID`,`TimeslotID`), ADD KEY `PorchfestID` (`PorchfestID`), ADD KEY `TimeslotID` (`TimeslotID`);

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
 ADD PRIMARY KEY (`UserID`,`BandID`), ADD KEY `UserID` (`UserID`,`BandID`), ADD KEY `BandID` (`BandID`);

--
-- Indexes for table `userstoporchfests`
--
ALTER TABLE `userstoporchfests`
 ADD PRIMARY KEY (`UserID`,`PorchfestID`), ADD KEY `UserID` (`UserID`), ADD KEY `PorchfestID` (`PorchfestID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bands`
--
ALTER TABLE `bands`
MODIFY `BandID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=21;
--
-- AUTO_INCREMENT for table `porchfests`
--
ALTER TABLE `porchfests`
MODIFY `PorchfestID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `porchfesttimeslots`
--
ALTER TABLE `porchfesttimeslots`
MODIFY `TimeslotID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `bandavailabletimes`
--
ALTER TABLE `bandavailabletimes`
ADD CONSTRAINT `bandavailabletimes_ibfk_1` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bandavailabletimes_ibfk_2` FOREIGN KEY (`TimeslotID`) REFERENCES `porchfesttimeslots` (`TimeslotID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bandstoporchfests`
--
ALTER TABLE `bandstoporchfests`
ADD CONSTRAINT `bandstoporchfests_ibfk_1` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bandstoporchfests_ibfk_2` FOREIGN KEY (`PorchfestID`) REFERENCES `porchfests` (`PorchfestID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `bandstoporchfests_ibfk_3` FOREIGN KEY (`TimeslotID`) REFERENCES `porchfesttimeslots` (`TimeslotID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `porchfesttimeslots`
--
ALTER TABLE `porchfesttimeslots`
ADD CONSTRAINT `porchfesttimeslots_ibfk_1` FOREIGN KEY (`PorchfestID`) REFERENCES `porchfests` (`PorchfestID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userstobands`
--
ALTER TABLE `userstobands`
ADD CONSTRAINT `userstobands_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `userstobands_ibfk_2` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userstoporchfests`
--
ALTER TABLE `userstoporchfests`
ADD CONSTRAINT `userstoporchfests_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `userstoporchfests_ibfk_2` FOREIGN KEY (`PorchfestID`) REFERENCES `porchfests` (`PorchfestID`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
