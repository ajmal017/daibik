←
phpMyAdmin
HomeEmpty session dataphpMyAdmin documentationDocumentationNavigation panel settingsReload navigation panel
RecentFavorites
Collapse allUnlink from main panel
New
Expand/CollapseDatabase operationsinformation_schema
Expand/CollapseDatabase operationsmysql
Expand/CollapseDatabase operationsperformance_schema
Expand/CollapseDatabase operationssys
Database operationstojo
NewNew
Expand/CollapseStructureboomer
Expand/CollapseStructurebtst_wicks
Expand/CollapseStructurepre_open
Expand/CollapseStructurestandard_deviation
Server: localhost:8889
Databases Databases
SQL SQL
Status Status
User accounts User accounts
Export Export
Import Import
Settings Settings
Replication Replication
Variables Variables
Charsets Charsets
Engines Engines
Plugins Plugins
Click on the bar to scroll to top of page
SQL Query Console Console
ascendingdescendingOrder:Debug SQLExecution orderTime takenOrder by:Group queries
Some error occurred while getting SQL debug info.
OptionsSet default
Always expand query messages
Show query history at start
Show current browsing query
 Execute queries on Enter and insert new line with Shift + Enter. To make this permanent, view settings.
Switch to dark theme

[ Back ]

[ Refresh ]
-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Dec 02, 2019 at 10:56 AM
-- Server version: 5.7.26
-- PHP Version: 7.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `tojo`
--
CREATE DATABASE IF NOT EXISTS `tojo` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `tojo`;

-- --------------------------------------------------------

--
-- Table structure for table `boomer`
--

CREATE TABLE `boomer` (
  `id` int(11) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `matched` text NOT NULL,
  `next_day_high` decimal(10,2) NOT NULL,
  `next_day_low` decimal(10,2) NOT NULL,
  `executed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `btst_wicks`
--

CREATE TABLE `btst_wicks` (
  `id` int(11) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `body_to_length` decimal(10,2) NOT NULL,
  `low_high_percentage` decimal(10,2) NOT NULL,
  `volume` decimal(10,2) NOT NULL,
  `next_day_percentage_open` decimal(10,2) NOT NULL,
  `wicks_to_wicks` decimal(10,2) NOT NULL,
  `next_day_percentage` decimal(10,2) NOT NULL,
  `type` varchar(255) NOT NULL,
  `executed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pre_open`
--

CREATE TABLE `pre_open` (
  `id` int(11) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `percentage` decimal(10,2) NOT NULL,
  `executed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `standard_deviation`
--

CREATE TABLE `standard_deviation` (
  `id` int(11) NOT NULL,
  `symbol` varchar(255) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `executed` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boomer`
--
ALTER TABLE `boomer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `btst_wicks`
--
ALTER TABLE `btst_wicks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pre_open`
--
ALTER TABLE `pre_open`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `standard_deviation`
--
ALTER TABLE `standard_deviation`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boomer`
--
ALTER TABLE `boomer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `btst_wicks`
--
ALTER TABLE `btst_wicks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pre_open`
--
ALTER TABLE `pre_open`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `standard_deviation`
--
ALTER TABLE `standard_deviation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


[ Back ]

[ Refresh ]
Open new phpMyAdmin window