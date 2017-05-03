-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: May 03, 2017 at 12:40 AM
-- Server version: 5.6.35
-- PHP Version: 7.0.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `porchfest`
--

-- --------------------------------------------------------

--
-- Table structure for table `bandstoporchfests`
--

CREATE TABLE `bandstoporchfests` (
  `BandID` int(11) NOT NULL,
  `PorchfestID` int(11) NOT NULL,
  `PorchLocation` varchar(255) NOT NULL,
  `Latitude` float NOT NULL,
  `Longitude` float NOT NULL,
  `TimeslotID` int(11) DEFAULT NULL,
  `Flagged` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `bandstoporchfests`
--

INSERT INTO `bandstoporchfests` (`BandID`, `PorchfestID`, `PorchLocation`, `Latitude`, `Longitude`, `TimeslotID`, `Flagged`) VALUES
(1, 1, '112 W Marshall St, Ithaca, NY 14850', 42.4463, -76.5002, 0, 0),
(2, 1, '202 Utica St, Ithaca, NY 14850', 42.4463, -76.4985, 0, 0),
(3, 1, '706 N Cayuga St, Ithaca, NY 14850', 42.4475, -76.4998, 0, 0),
(4, 1, '607 N Tioga St, Ithaca, NY 14850', 42.4467, -76.4968, 0, 0),
(5, 1, '307 Auburn St, Ithaca, NY 14850', 42.4499, -76.5005, 0, 0),
(6, 1, '202 Utica St, Ithaca, NY 14850', 42.4463, -76.4985, 0, 0),
(7, 1, '116 Cascadilla Ave, Ithaca, NY 14850', 42.4444, -76.498, 0, 0),
(8, 1, '511 Willow Ave, Ithaca, NY 14850', 42.4495, -76.5026, 0, 0),
(9, 1, '116 Cascadilla St, Ithaca, NY 14850', 42.4447, -76.5006, 0, 0),
(10, 1, '401 Linn St, Ithaca, NY 14850', 42.4474, -76.4944, 0, 0),
(11, 1, '720 N Aurora St, Ithaca, NY 14850', 42.4483, -76.4963, 0, 0),
(12, 1, '609 N Aurora St, Ithaca, NY 14850', 42.4468, -76.4957, 0, 0),
(13, 1, '421 N Aurora St, Ithaca, NY 14850', 42.4436, -76.4955, 0, 0),
(14, 1, '601 N Cayuga St, Ithaca, NY 14850', 42.4462, -76.499, 0, 0),
(15, 1, '1103 N Cayuga St, Ithaca, NY 14850', 42.4523, -76.4994, 0, 0),
(16, 1, '611 N Aurora St, Ithaca, NY 14850', 42.4468, -76.4957, 0, 0),
(17, 1, '210 Utica St, Ithaca, NY 14850', 42.4467, -76.4983, 0, 0),
(18, 1, '104 Adams St, Ithaca, NY 14850', 42.4497, -76.5013, 0, 0),
(19, 1, '211 Willow Ave, Ithaca, NY 14850', 42.4467, -76.5003, 0, 0),
(20, 1, '509 Utica St, Ithaca, NY 14850', 42.4501, -76.4982, 0, 0),
(21, 1, '1103 N Cayuga St, Ithaca, NY 14850', 42.4523, -76.4994, 0, 0),
(22, 1, '308 Lake Ave, Ithaca, NY 14850', 42.4472, -76.5019, 0, 0),
(23, 1, '104 E Jay St, Ithaca, NY 14850', 42.4509, -76.4991, 0, 0),
(24, 1, '614 N Cayuga St, Ithaca, NY 14850', 42.4469, -76.4998, 0, 0),
(25, 1, '602 N Cayuga St, Ithaca, NY 14850', 42.4462, -76.4997, 0, 0),
(26, 1, '912 N Cayuga St, Ithaca, NY 14850', 42.4501, -76.4995, 0, 0),
(27, 1, '100 Franklin St, Ithaca, NY 14850', 42.451, -76.5011, 0, 0),
(28, 1, '1112 N Tioga St, Ithaca, NY 14850', 42.4526, -76.4979, 0, 0),
(29, 1, '1112 N Tioga St, Ithaca, NY 14850', 42.4526, -76.4979, 0, 0),
(30, 1, '204 W Yates St, Ithaca, NY 14850', 42.4472, -76.501, 0, 0),
(31, 1, '204 W Yates St, Ithaca, NY 14850', 42.4472, -76.501, 0, 0),
(32, 1, '930 N Tioga St, Ithaca, NY 14850', 42.4506, -76.4975, 0, 0),
(33, 1, '100 Franklin St, Ithaca, NY 14850', 42.451, -76.5011, 0, 0),
(34, 1, '202 Utica St, Ithaca, NY 14850', 42.4463, -76.4985, 0, 0),
(35, 1, 'Thompson Park, Ithaca, NY 14850', 42.4455, -76.4999, 0, 0),
(36, 1, '218 Utica St, Ithaca, NY 14850', 42.447, -76.4985, 0, 0),
(37, 1, '114 Cascadilla Ave, Ithaca, NY 14850', 42.4444, -76.4981, 0, 0),
(38, 1, '100 Franklin St, Ithaca, NY 14850', 42.451, -76.5011, 0, 0),
(39, 1, '112 W Marshall St, Ithaca, NY 14850', 42.4463, -76.5002, 0, 0),
(40, 1, '707 N Aurora St, Ithaca, NY 14850', 42.4477, -76.4957, 0, 0),
(41, 1, '610 E Marshall St, Ithaca, NY 14850', 42.4462, -76.4948, 0, 0),
(42, 1, '115 W Yates St, Ithaca, NY 14850', 42.447, -76.5004, 0, 0),
(43, 1, '702 N Aurora St, Ithaca, NY 14850', 42.4475, -76.4964, 0, 0),
(44, 1, '612 N Cayuga St, Ithaca, NY 14850', 42.4468, -76.4998, 0, 0),
(45, 1, '607 N Aurora St, Ithaca, NY 14850', 42.4467, -76.4957, 0, 0),
(46, 1, '612 N Cayuga St, Ithaca, NY 14850', 42.4468, -76.4998, 0, 0),
(47, 1, '714 N Cayuga St, Ithaca, NY 14850', 42.448, -76.4999, 0, 0),
(48, 1, '432 N Tioga St, Ithaca, NY 14850', 42.4443, -76.4974, 0, 0),
(49, 1, '106 E Yates St, Ithaca, NY 14850', 42.4472, -76.4989, 0, 0),
(50, 1, '607 N Aurora St, Ithaca, NY 14850', 42.4467, -76.4957, 0, 0),
(51, 1, '104 Adams St, Ithaca, NY 14850', 42.4497, -76.5013, 0, 0),
(52, 1, '105 King St, Ithaca, NY 14850', 42.4501, -76.4969, 0, 0),
(53, 1, '116 Cascadilla Ave, Ithaca, NY 14850', 42.4444, -76.498, 0, 0),
(54, 1, '515 N Tioga St, Ithaca, NY 14850', 42.4459, -76.4968, 0, 0),
(55, 1, '617 N Cayuga St, Ithaca, NY 14850', 42.4471, -76.499, 0, 0),
(56, 1, '442 N Aurora St, Ithaca, NY 14850', 42.4445, -76.4962, 0, 0),
(57, 1, '442 N Aurora St, Ithaca, NY 14850', 42.4445, -76.4962, 0, 0),
(58, 1, '1301 N Cayuga St, Ithaca, NY 14850', 42.4536, -76.4995, 0, 0),
(59, 1, '219 Auburn St, Ithaca, NY 14850', 42.4493, -76.5004, 0, 0),
(60, 1, '219 Auburn St, Ithaca, NY 14850', 42.4493, -76.5004, 0, 0),
(61, 1, '617 N Cayuga St, Ithaca, NY 14850', 42.4471, -76.499, 0, 0),
(62, 1, '104 Adams St, Ithaca, NY 14850', 42.4497, -76.5013, 0, 0),
(63, 1, '620 N Tioga St, Ithaca, NY 14850', 42.4471, -76.4975, 0, 0),
(64, 1, '711 N Tioga St, Ithaca, NY 14850', 42.448, -76.4969, 0, 0),
(65, 1, '611 N Cayuga St, Ithaca, NY 14850', 42.4468, -76.4991, 0, 0),
(66, 1, '422 N Cayuga St, Ithaca, NY 14850', 42.4441, -76.4997, 0, 0),
(67, 1, '710 N Aurora St, Ithaca, NY 14850', 42.4477, -76.4964, 0, 0),
(68, 1, '711 N Tioga St, Ithaca, NY 14850', 42.448, -76.4969, 0, 0),
(69, 1, '502 Linn St, Ithaca, NY 14850', 42.4487, -76.4951, 0, 0),
(70, 1, '611 N Aurora St, Ithaca, NY 14850', 42.4468, -76.4957, 0, 0),
(71, 1, '702 N Aurora St, Ithaca, NY 14850', 42.4475, -76.4964, 0, 0),
(72, 1, '811 N Tioga St, Ithaca, NY 14850', 42.4492, -76.497, 0, 0),
(73, 1, '1301 N Cayuga St, Ithaca, NY 14850', 42.4536, -76.4995, 0, 0),
(74, 1, '318 Lake Ave, Ithaca, NY 14850', 42.4475, -76.5022, 0, 0),
(75, 1, '912 N Cayuga St, Ithaca, NY 14850', 42.4501, -76.4995, 0, 0),
(76, 1, '105 Second St, Ithaca, NY 14850', 42.4449, -76.5021, 0, 0),
(77, 1, '308 Utica St, Ithaca, NY 14850', 42.4477, -76.4987, 0, 0),
(78, 1, '206 E Jay St, Ithaca, NY 14850', 42.4509, -76.4979, 0, 0),
(79, 1, '412 N Aurora St, Ithaca, NY 14850', 42.4436, -76.4961, 0, 0),
(80, 1, '814 N Cayuga St, Ithaca, NY 14850', 42.4491, -76.4999, 0, 0),
(81, 1, '442 N Aurora St, Ithaca, NY 14850', 42.4445, -76.4962, 0, 0),
(82, 1, '908 N Cayuga St, Ithaca, NY 14850', 42.4499, -76.4996, 0, 0),
(83, 1, '810 N Aurora St, Ithaca, NY 14850', 42.4489, -76.4964, 0, 0),
(84, 1, 'Thompson Park, Ithaca, NY 14850', 42.4455, -76.4999, 0, 0),
(85, 1, '619 N Aurora St, Ithaca, NY 14850', 42.4472, -76.4957, 0, 0),
(86, 1, '112 W Marshall St, Ithaca, NY 14850', 42.4463, -76.5002, 0, 0),
(87, 1, '619 N Aurora St, Ithaca, NY 14850', 42.4472, -76.4957, 0, 0),
(88, 1, '216 Queen St, Ithaca, NY 14850', 42.4516, -76.4955, 0, 0),
(89, 1, '513 Utica St, Ithaca, NY 14850', 42.4504, -76.4983, 0, 0),
(90, 1, '104 Adams St, Ithaca, NY 14850', 42.4497, -76.5013, 0, 0),
(91, 1, '619 N Aurora St, Ithaca, NY 14850', 42.4472, -76.4957, 0, 0),
(92, 1, '203 Auburn St, Ithaca, NY 14850', 42.4486, -76.5004, 0, 0),
(93, 1, '317 Auburn St, Ithaca, NY 14850', 42.4502, -76.5009, 0, 0),
(94, 1, '102 Hancock St, Ithaca, NY 14850', 42.4485, -76.5011, 0, 0),
(95, 1, '102 Hancock St, Ithaca, NY 14850', 42.4485, -76.5011, 0, 0),
(96, 1, '406 E Marshall St, Ithaca, NY 14850', 42.4462, -76.4955, 0, 0),
(97, 1, '115 Cascadilla St, Ithaca, NY 14850', 42.4444, -76.5002, 0, 0),
(98, 1, '306 E Yates St, Ithaca, NY 14850', 42.4475, -76.4967, 0, 0),
(99, 1, '306 E Yates St, Ithaca, NY 14850', 42.4475, -76.4967, 0, 0),
(100, 1, '710 N Aurora St, Ithaca, NY 14850', 42.4477, -76.4964, 0, 0),
(101, 1, '104 Adams St, Ithaca, NY 14850', 42.4497, -76.5013, 0, 0),
(102, 1, '1102 N Cayuga St, Ithaca, NY 14850', 42.4521, -76.5, 0, 0),
(103, 1, '427 N Cayuga St, Ithaca, NY 14850', 42.4443, -76.499, 0, 0),
(104, 1, '202 2nd St, Ithaca, NY 14850', 42.4451, -76.5029, 0, 0),
(105, 1, '202 2nd St, Ithaca, NY 14850', 42.4451, -76.5029, 0, 0),
(106, 1, '912 N Cayuga St, Ithaca, NY 14850', 42.4501, -76.4995, 0, 0),
(107, 1, '306 N Aurora St, Ithaca, NY 14850', 42.4416, -76.496, 0, 0),
(108, 1, '116 Cascadilla Ave, Ithaca, NY 14850', 42.4444, -76.498, 0, 0),
(109, 1, '513 Willow Ave, Ithaca, NY 14850', 42.4496, -76.5027, 0, 0),
(110, 1, '210 E Marshall St, Ithaca, NY 14850', 42.4463, -76.4976, 0, 0),
(111, 1, '313 Utica St, Ithaca, NY 14850', 42.448, -76.4981, 0, 0),
(112, 1, '520 N Tioga St, Ithaca, NY 14850', 42.446, -76.4974, 0, 0),
(113, 1, '703 N Cayuga St, Ithaca, NY 14850', 42.4475, -76.4991, 0, 0),
(114, 1, '607 N Tioga St, Ithaca, NY 14850', 42.4467, -76.4968, 0, 0),
(115, 1, '502 Linn St, Ithaca, NY 14850', 42.4487, -76.4951, 0, 0),
(116, 1, '317 Utica St, Ithaca, NY 14850', 42.4481, -76.4981, 0, 0),
(117, 1, '916 N Aurora St, Ithaca, NY 14850', 42.451, -76.4966, 0, 0),
(118, 1, '615 Utica St, Ithaca, NY 14850', 42.4515, -76.4983, 0, 0),
(119, 1, '306 E Yates St, Ithaca, NY 14850', 42.4475, -76.4967, 0, 0),
(120, 1, '520 N Tioga St, Ithaca, NY 14850', 42.446, -76.4974, 0, 0),
(121, 1, '508 N. Aurora St, Ithaca, NY 14850', 42.4455, -76.4963, 0, 0),
(122, 1, '1112 N Tioga St, Ithaca, NY 14850', 42.4526, -76.4979, 0, 0),
(123, 1, '611 N Cayuga St, Ithaca, NY 14850', 42.4468, -76.4991, 0, 0),
(124, 1, '611 N Cayuga St, Ithaca, NY 14850', 42.4468, -76.4991, 0, 0),
(125, 1, '412 N Aurora St, Ithaca, NY 14850', 42.4436, -76.4961, 0, 0),
(126, 1, '507 Utica St, Ithaca, NY 14850', 42.45, -76.4982, 0, 0),
(127, 1, '206 E Jay St, Ithaca, NY 14850', 42.4509, -76.4979, 0, 0),
(128, 1, '203 Auburn St, Ithaca, NY 14850', 42.4486, -76.5004, 0, 0),
(129, 1, '608 Utica St, Ithaca, NY 14850', 42.4512, -76.4989, 0, 0),
(130, 1, '608 Utica St, Ithaca, NY 14850', 42.4512, -76.4989, 0, 0),
(131, 1, '802 N Cayuga St, Ithaca, NY 14850', 42.4485, -76.4998, 0, 0),
(132, 1, '713 N Cayuga St, Ithaca, NY 14850', 42.4481, -76.4991, 0, 0),
(133, 1, '100 Franklin St, Ithaca, NY 14850', 42.451, -76.5011, 0, 0),
(134, 1, '304 Linn St, Ithaca, NY 14850', 42.4465, -76.495, 0, 0),
(135, 1, '412 N Aurora St, Ithaca, NY 14850', 42.4436, -76.4961, 0, 0),
(136, 1, '516 Linn St, Ithaca, NY 14850', 42.4496, -76.4949, 0, 0),
(137, 1, '218 Utica St, Ithaca, NY 14850', 42.447, -76.4985, 0, 0),
(138, 1, '432 N Tioga St, Ithaca, NY 14850', 42.4443, -76.4974, 0, 0),
(139, 1, '421 N Aurora St, Ithaca, NY 14850', 42.4436, -76.4955, 0, 0),
(140, 1, '711 N Tioga St, Ithaca, NY 14850', 42.448, -76.4969, 0, 0),
(141, 1, '413 Auburn St, Ithaca, NY 14850', 42.4515, -76.5006, 0, 0),
(142, 1, '421 N Aurora St, Ithaca, NY 14850', 42.4436, -76.4955, 0, 0),
(143, 1, '909 N Cayuga St, Ithaca, NY 14850', 42.4501, -76.4993, 0, 0),
(144, 1, '108 W Yates, Ithaca, NY, 14850', 42.4474, -76.5, 0, 0),
(145, 1, '117 Farm St, Ithaca, NY 14850', 42.4447, -76.4981, 0, 0),
(146, 1, '209 E Jay St, Ithaca, NY 14850', 42.4505, -76.4979, 0, 0),
(147, 1, '216 Cascadilla St, Ithaca, NY 14850', 42.4447, -76.502, 0, 0),
(148, 1, '611 N Tioga St, Ithaca, NY 14850', 42.4469, -76.4968, 0, 0),
(149, 1, '213 Second St, Ithaca, NY 14850', 42.4457, -76.5027, 0, 0),
(150, 1, '321 N Tioga St, Ithaca, NY 14850', 42.4424, -76.497, 0, 0),
(151, 1, '507 Utica St, Ithaca, NY 14850', 42.45, -76.4982, 0, 0),
(152, 1, '519 Willow Ave, Ithaca, NY 14850', 42.4498, -76.5029, 0, 0),
(153, 1, '117 E York St, Ithaca, NY 14850', 42.4533, -76.4987, 0, 0),
(154, 1, '117 E York St, Ithaca, NY 14850', 42.4533, -76.4987, 0, 0),
(155, 1, '1106 N Cayuga St, Ithaca, NY 14850', 42.4523, -76.5001, 0, 0),
(156, 1, '1106 N Cayuga St, Ithaca, NY 14850', 42.4523, -76.5001, 0, 0),
(157, 1, '117 Farm St, Ithaca, NY 14850', 42.4447, -76.4981, 0, 0),
(158, 1, '401 Linn St, Ithaca, NY 14850', 42.4474, -76.4944, 0, 0),
(159, 1, '702 N Aurora St, Ithaca, NY 14850', 42.4475, -76.4964, 0, 0),
(160, 1, '610 N Cayuga St, Ithaca, NY 14850', 42.4467, -76.4997, 0, 0),
(161, 1, '423 E Lincoln St, Ithaca, NY 14850', 42.4518, -76.4948, 0, 0),
(162, 1, '716 N Aurora St, Ithaca, NY 14850', 42.4482, -76.4964, 0, 0),
(163, 1, '919 N Tioga St, Ithaca, NY 14850', 42.4506, -76.4971, 0, 0),
(164, 1, '511 N Aurora St, Ithaca, NY 14850', 42.4455, -76.4956, 0, 0),
(165, 1, '510 Linn St, Ithaca, NY 14850', 42.4492, -76.4953, 0, 0),
(166, 1, '308 Lake Ave, Ithaca, NY 14850', 42.4472, -76.5019, 0, 0),
(167, 1, '413 N Cayuga St, Ithaca, NY 14850', 42.4434, -76.4988, 0, 0),
(168, 1, '710 N Aurora St, Ithaca, NY 14850', 42.4477, -76.4964, 0, 0),
(169, 1, '117 E York St, Ithaca, NY 14850', 42.4533, -76.4987, 0, 0),
(170, 1, '118 Cascadilla Ave, Ithaca, NY 14850', 42.4443, -76.4978, 0, 0),
(171, 1, '713 N Aurora St, Ithaca, NY 14850', 42.448, -76.4957, 0, 0),
(172, 1, '304 E Marshall St, Ithaca, NY 14850', 42.4463, -76.4968, 0, 0),
(173, 1, '108 Auburn St, Ithaca, NY 14850', 42.4475, -76.5009, 0, 0),
(174, 1, '404 N Cayuga St, Ithaca, NY 14850', 42.443, -76.4995, 0, 0),
(175, 1, '212 2nd St, Ithaca, NY 14850', 42.4454, -76.5032, 0, 0),
(176, 1, '212 2nd St, Ithaca, NY 14850', 42.4454, -76.5032, 0, 0),
(177, 1, '410 Madison St, Ithaca, NY 14850', 42.4454, -76.5051, 0, 0),
(178, 1, '432 N Tioga St, Ithaca, NY 14850', 42.4443, -76.4974, 0, 0),
(179, 1, '811 N Tioga St, Ithaca, NY 14850', 42.4492, -76.497, 0, 0),
(180, 1, '410 Madison St, Ithaca, NY 14850', 42.4454, -76.5051, 0, 0),
(181, 1, '511 N Aurora St, Ithaca, NY 14850', 42.4455, -76.4956, 0, 0),
(182, 1, '204 W Yates St, Ithaca, NY 14850', 42.4472, -76.501, 0, 0),
(183, 1, '218 Utica St, Ithaca, NY 14850', 42.447, -76.4985, 0, 0),
(184, 1, '117 Farm St, Ithaca, NY 14850', 42.4447, -76.4981, 0, 0),
(185, 1, '108 Auburn St, Ithaca, NY 14850', 42.4475, -76.5009, 0, 0),
(186, 1, '109 W Jay St, Ithaca, NY 14850', 42.4504, -76.5005, 0, 0),
(187, 1, '206 E Jay St, Ithaca, NY 14850', 42.4509, -76.4979, 0, 0),
(188, 1, '513 Willow Ave, Ithaca, NY 14850', 42.4496, -76.5027, 0, 0),
(189, 1, '306 N Aurora St, Ithaca, NY 14850', 42.4416, -76.496, 0, 0),
(190, 1, '306 N Aurora St, Ithaca, NY 14850', 42.4416, -76.496, 0, 0),
(191, 1, '407 Utica St, Ithaca, NY 14850', 42.4489, -76.4981, 0, 0),
(192, 1, '306 N Aurora St, Ithaca, NY 14850', 42.4416, -76.496, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bandstoporchfests`
--
ALTER TABLE `bandstoporchfests`
  ADD PRIMARY KEY (`BandID`,`PorchfestID`),
  ADD KEY `BandID` (`BandID`,`PorchfestID`,`TimeslotID`),
  ADD KEY `PorchfestID` (`PorchfestID`),
  ADD KEY `TimeslotID` (`TimeslotID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bandstoporchfests`
--
ALTER TABLE `bandstoporchfests`
  ADD CONSTRAINT `bandstoporchfests_ibfk_1` FOREIGN KEY (`BandID`) REFERENCES `bands` (`BandID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bandstoporchfests_ibfk_2` FOREIGN KEY (`PorchfestID`) REFERENCES `porchfests` (`PorchfestID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bandstoporchfests_ibfk_3` FOREIGN KEY (`TimeslotID`) REFERENCES `porchfesttimeslots` (`TimeslotID`) ON DELETE CASCADE ON UPDATE CASCADE;
