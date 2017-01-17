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

-- Exportiere Struktur von Tabelle development.games
CREATE TABLE IF NOT EXISTS `games` (
  `gameId` int(11) NOT NULL AUTO_INCREMENT,
  `gameVendor` varchar(50) NOT NULL DEFAULT '0',
  `gameName` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`gameId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle development.games: ~1 rows (ungefähr)
DELETE FROM `games`;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;
INSERT INTO `games` (`gameId`, `gameVendor`, `gameName`) VALUES
	(1, 'Blizzard', 'Overwatch');
/*!40000 ALTER TABLE `games` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.playerAttributes
CREATE TABLE IF NOT EXISTS `playerAttributes` (
  `playerAttributesId` int(11) NOT NULL AUTO_INCREMENT,
  `gameId` int(11) NOT NULL DEFAULT '0',
  `playerId` int(11) NOT NULL DEFAULT '0',
  `key` varchar(50) NOT NULL DEFAULT '0',
  `value` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`playerAttributesId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle development.playerAttributes: ~1 rows (ungefähr)
DELETE FROM `playerAttributes`;
/*!40000 ALTER TABLE `playerAttributes` DISABLE KEYS */;
INSERT INTO `playerAttributes` (`playerAttributesId`, `gameId`, `playerId`, `key`, `value`) VALUES
	(1, 1, 1, 'battletag', 'snooops#2628');
/*!40000 ALTER TABLE `playerAttributes` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.players
CREATE TABLE IF NOT EXISTS `players` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL DEFAULT '0',
  `ts3id` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle development.players: ~1 rows (ungefähr)
DELETE FROM `players`;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;
INSERT INTO `players` (`id`, `userId`, `ts3id`) VALUES
	(1, 1, 'Yjnivc0ZY5g84bL1himAyAhK35I=');
/*!40000 ALTER TABLE `players` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.teamspeakGroupMap
CREATE TABLE IF NOT EXISTS `teamspeakGroupMap` (
  `teamspeakGroupMapId` int(11) NOT NULL AUTO_INCREMENT,
  `gameId` int(11) NOT NULL DEFAULT '0',
  `teamspeakGroupId` int(11) NOT NULL DEFAULT '0',
  `rankLimit` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`teamspeakGroupMapId`),
  UNIQUE KEY `teamspeakGroupId` (`teamspeakGroupId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

-- Exportiere Daten aus Tabelle development.teamspeakGroupMap: ~0 rows (ungefähr)
DELETE FROM `teamspeakGroupMap`;
/*!40000 ALTER TABLE `teamspeakGroupMap` DISABLE KEYS */;
INSERT INTO `teamspeakGroupMap` (`teamspeakGroupMapId`, `gameId`, `teamspeakGroupId`, `rankLimit`) VALUES
	(1, 1, 10, 1),
	(2, 1, 11, 1500),
	(3, 1, 12, 2000),
	(4, 1, 13, 2500),
	(5, 1, 14, 3000),
	(6, 1, 15, 3500),
	(7, 1, 16, 4000);
/*!40000 ALTER TABLE `teamspeakGroupMap` ENABLE KEYS */;

-- Exportiere Struktur von Tabelle development.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
  `password` varchar(32) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Exportiere Daten aus Tabelle development.users: ~1 rows (ungefähr)
DELETE FROM `users`;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `password`) VALUES
	(1, 'snooops', 'geheim');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
