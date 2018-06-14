-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 26, 2017 at 06:04 AM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `projectplanner`
--

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `Client_ID` int(100) NOT NULL,
  `Client_CompanyName` varchar(100) NOT NULL,
  `Client_Firstname` varchar(100) NOT NULL,
  `Client_Lastname` varchar(100) NOT NULL,
  `Client_Industry` varchar(100) NOT NULL,
  `Client_Email` varchar(100) NOT NULL,
  `Client_Phone` varchar(15) NOT NULL,
  `Client_Street` varchar(100) NOT NULL,
  `Client_City` varchar(100) NOT NULL,
  `Client_State` varchar(2) NOT NULL,
  `Client_Zipcode` int(5) UNSIGNED NOT NULL,
  `Client_Country` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`Client_ID`, `Client_CompanyName`, `Client_Firstname`, `Client_Lastname`, `Client_Industry`, `Client_Email`, `Client_Phone`, `Client_Street`, `Client_City`, `Client_State`, `Client_Zipcode`, `Client_Country`) VALUES
(1, 'ABC INC.', 'John', 'Smith', 'OIL', 'OIL@OIL.com', '1243234233', '123 A street', 'garden city', 'OH', 99999, 'USA'),
(2, 'Pepper ACID Corp.', 'Linda', 'Low', 'Agriculture', 'linda@linda.com', '2353463234234', '874 Nihonkin avenue', 'saika', 'NA', 55555, 'JAPAN'),
(3, 'Open Field', 'Julie', 'Once', 'Coal', 'julie@julie.com', '2350503004', '4443 2nd Street', 'Glen burnie', 'MD', 93944, 'USA'),
(4, 'Belly Tomato', 'Golden', 'Boy', 'Agriculture', 'OOPSY@OPPS.com', '534509043', '9504 N street', 'Dailyville', 'MD', 93459, 'USA'),
(5, 'TIN TIN', 'luis', 'grolo', 'Metal', 'grolo@grolo', '2345255324', '2354 BING BONG', 'BING BING', 'CC', 25435, 'China'),
(6, 'Oil ABC', 'sdafsda', 'sadfsdaf', 'OIL', 'sadfsadf@ddfsa.com', '634234', '22 fail big', 'time', 'jh', 67554, 'China'),
(7, '2', 'j', 'l', 'IF', 'asld@umbc.edu', '2394823', '129 Leds Ter', 'Bal', 'MD', 21227, 'US');

-- --------------------------------------------------------

--
-- Table structure for table `phases`
--

CREATE TABLE `phases` (
  `Phase_ID` int(100) NOT NULL,
  `User_ID_FK` int(100) NOT NULL,
  `Project_ID_FK` int(100) NOT NULL,
  `Phase_Name` varchar(100) NOT NULL,
  `Phase_Description` varchar(500) NOT NULL,
  `Phase_Status` varchar(100) NOT NULL,
  `Phase_TotalHours` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `phases`
--

INSERT INTO `phases` (`Phase_ID`, `User_ID_FK`, `Project_ID_FK`, `Phase_Name`, `Phase_Description`, `Phase_Status`, `Phase_TotalHours`) VALUES
(7, 3, 51, 'Phase 1', 'Test', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `Project_ID` int(100) NOT NULL,
  `Client_ID_FK` int(100) DEFAULT NULL,
  `Project_Name` varchar(100) NOT NULL,
  `Project_Description` varchar(2000) NOT NULL,
  `Project_Status` varchar(100) NOT NULL,
  `Project_StartDate` date NOT NULL,
  `Project_EstimatedBudget` float NOT NULL,
  `Project_RemainedBudget` float NOT NULL,
  `Project_TotalHours` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`Project_ID`, `Client_ID_FK`, `Project_Name`, `Project_Description`, `Project_Status`, `Project_StartDate`, `Project_EstimatedBudget`, `Project_RemainedBudget`, `Project_TotalHours`) VALUES
(51, 1, 'dafsdaf', '', 'Requested', '2017-11-08', 324234, 324234, 2342),
(52, 3, '324234', '', 'Approved', '2017-10-31', 324234, 324234, 234234),
(53, 1, 'sdafsadf', '', 'Requested', '2017-11-01', 324234, 324234, 234234);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `Task_ID` int(100) NOT NULL,
  `Project_ID_FK` int(100) NOT NULL,
  `User_ID_FK` int(100) NOT NULL,
  `Phase_ID_FK` int(100) NOT NULL,
  `Task_Name` varchar(100) NOT NULL,
  `Task_Description` varchar(500) NOT NULL,
  `Task_EstimatedHours` int(100) NOT NULL,
  `Task_EstimatedCost` float NOT NULL,
  `Task_WorkedHours` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`Task_ID`, `Project_ID_FK`, `User_ID_FK`, `Phase_ID_FK`, `Task_Name`, `Task_Description`, `Task_EstimatedHours`, `Task_EstimatedCost`, `Task_WorkedHours`) VALUES
(4, 51, 3, 7, 'Task 1', 'Testing', 24, 300, 0),
(5, 51, 3, 7, 'Task 2', 'Testing', 24, 300, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int(100) NOT NULL,
  `User_Name` varchar(100) NOT NULL,
  `User_Password` varchar(255) NOT NULL,
  `User_Firstname` varchar(100) NOT NULL,
  `User_Lastname` varchar(100) NOT NULL,
  `User_Role` int(1) UNSIGNED NOT NULL,
  `User_Phone` varchar(9) NOT NULL,
  `User_Email` varchar(100) NOT NULL,
  `User_Street` varchar(100) NOT NULL,
  `User_City` varchar(100) NOT NULL,
  `User_State` varchar(2) NOT NULL,
  `User_Zipcode` int(5) UNSIGNED NOT NULL,
  `User_Birthdate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `User_Name`, `User_Password`, `User_Firstname`, `User_Lastname`, `User_Role`, `User_Phone`, `User_Email`, `User_Street`, `User_City`, `User_State`, `User_Zipcode`, `User_Birthdate`) VALUES
(1, 'employee1', '$2y$10$rBMhQ/gUrzDPhWmy91jQxu4SbTg6NKg/oWxv2RdcXVWD2F/ASpI0C', 'a', 'a', 0, '123456789', '123@123.com', '123 Wish', 'baltimore', 'MD', 21228, '2017-11-01'),
(2, 'manager1', '$2y$10$6Bjb9wNzWINGAbEcBt4vve0IGKo.RJ05xWg9tYpRtUEIDSK8DwHLC', 'b', 'b', 1, '987654321', 'abc@abc.com', '881 N Street', 'Glen burnie', 'PA', 1234, '2017-11-29'),
(3, 'jlutz1', '$2y$10$za5U6crE4NrKMiiOMqNx3OF.bI6fTxfYU7Jy6Bo8LzwEKC.4vn06W', 'Jacob', 'Lutz', 1, '443939238', 'jlutz1@umbc.edu', '1219 Leeds Terrace', 'Baltimore', 'MD', 21227, '1995-08-22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`Client_ID`);

--
-- Indexes for table `phases`
--
ALTER TABLE `phases`
  ADD PRIMARY KEY (`Phase_ID`),
  ADD KEY `Project_ID` (`Project_ID_FK`),
  ADD KEY `User_ID` (`User_ID_FK`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`Project_ID`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`Task_ID`),
  ADD KEY `phases` (`Phase_ID_FK`),
  ADD KEY `project` (`Project_ID_FK`),
  ADD KEY `user` (`User_ID_FK`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `User_Name` (`User_Name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `Client_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `phases`
--
ALTER TABLE `phases`
  MODIFY `Phase_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `Project_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `Task_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `phases`
--
ALTER TABLE `phases`
  ADD CONSTRAINT `Project_ID` FOREIGN KEY (`Project_ID_FK`) REFERENCES `projects` (`Project_ID`),
  ADD CONSTRAINT `User_ID` FOREIGN KEY (`User_ID_FK`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `phases` FOREIGN KEY (`Phase_ID_FK`) REFERENCES `phases` (`Phase_ID`),
  ADD CONSTRAINT `project` FOREIGN KEY (`Project_ID_FK`) REFERENCES `projects` (`Project_ID`),
  ADD CONSTRAINT `user` FOREIGN KEY (`User_ID_FK`) REFERENCES `users` (`User_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
