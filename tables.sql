-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server Version:               10.0.28-MariaDB-0+deb8u1 - (Debian)
-- Server Betriebssystem:        debian-linux-gnu
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Exportiere Datenbank Struktur für development
CREATE DATABASE IF NOT EXISTS `development` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `development`;

-- Exportiere Struktur von Tabelle development.gameLeagueOfLegends
CREATE TABLE IF NOT EXISTS `gameLeagueOfLegends` (
  `gameLeagueOfLegendsId` int(11) NOT NULL AUTO_INCREMENT,
  `playerId` int(11) NOT NULL DEFAULT '0',
  `server` varchar(24) NOT NULL DEFAULT '0',
  `summonerName` varchar(24) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameLeagueOfLegendsId`),
  KEY `playerId` (`playerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle development.gameLeagueOfLegends: ~0 rows (ungefähr)
DELETE FROM `gameLeagueOfLegends`;
/*!40000 ALTER TABLE `gameLeagueOfLegends` DISABLE KEYS */;
/*!40000 ALTER TABLE `gameLeagueOfLegends` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.gameOverwatch
CREATE TABLE IF NOT EXISTS `gameOverwatch` (
  `gameOverwatchId` int(11) NOT NULL AUTO_INCREMENT,
  `playerId` int(11) NOT NULL DEFAULT '0',
  `battletag` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameOverwatchId`),
  KEY `playerId` (`playerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle development.gameOverwatch: ~0 rows (ungefähr)
DELETE FROM `gameOverwatch`;
/*!40000 ALTER TABLE `gameOverwatch` DISABLE KEYS */;
/*!40000 ALTER TABLE `gameOverwatch` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.games
CREATE TABLE IF NOT EXISTS `games` (
  `gameId` int(11) NOT NULL AUTO_INCREMENT,
  `gameVendor` varchar(50) NOT NULL DEFAULT '0',
  `gameName` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle development.games: ~0 rows (ungefähr)
DELETE FROM `games`;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
/*!40000 ALTER TABLE `games` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.logger
CREATE TABLE IF NOT EXISTS `logger` (
  `loggerId` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `message` mediumtext NOT NULL,
  PRIMARY KEY (`loggerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle development.logger: ~0 rows (ungefähr)
DELETE FROM `logger`;
/*!40000 ALTER TABLE `logger` DISABLE KEYS */;
/*!40000 ALTER TABLE `logger` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.players
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `ts3id` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle development.players: ~0 rows (ungefähr)
DELETE FROM `players`;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;
/*!40000 ALTER TABLE `players` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.teamspeakGroupMap
CREATE TABLE IF NOT EXISTS `teamspeakGroupMap` (
  `teamspeakGroupMapId` int(11) NOT NULL AUTO_INCREMENT,
  `gameId` int(11) NOT NULL DEFAULT '0',
  `teamspeakGroupId` int(11) NOT NULL DEFAULT '0',
  `rankLimit` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`teamspeakGroupMapId`),
  UNIQUE KEY `teamspeakGroupId` (`teamspeakGroupId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle development.teamspeakGroupMap: ~0 rows (ungefähr)
DELETE FROM `teamspeakGroupMap`;
/*!40000 ALTER TABLE `teamspeakGroupMap` DISABLE KEYS */;
/*!40000 ALTER TABLE `teamspeakGroupMap` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Exportiere Daten aus Tabelle development.users: ~0 rows (ungefähr)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
