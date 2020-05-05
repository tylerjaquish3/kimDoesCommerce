-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               5.7.24 - MySQL Community Server (GPL)
-- Server OS:                    Win64
-- HeidiSQL Version:             10.2.0.5599
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Dumping database structure for trivia
CREATE DATABASE IF NOT EXISTS `trivia` /*!40100 DEFAULT CHARACTER SET latin1 */;
USE `trivia`;

-- Dumping structure for table trivia.points
CREATE TABLE IF NOT EXISTS `points` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL,
  `round_id` int(11) unsigned DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Dumping data for table trivia.points: ~0 rows (approximately)
/*!40000 ALTER TABLE `points` DISABLE KEYS */;
/*!40000 ALTER TABLE `points` ENABLE KEYS */;

-- Dumping structure for table trivia.questions
CREATE TABLE IF NOT EXISTS `questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(500) NOT NULL,
  `answer` varchar(100) DEFAULT NULL,
  `round_id` smallint(5) unsigned DEFAULT NULL,
  `sort_order` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table trivia.questions: ~3 rows (approximately)
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
INSERT INTO `questions` (`id`, `question`, `answer`, `round_id`, `sort_order`) VALUES
	(1, 'What is Tyler\'s favorite color?', 'Green', 1, 1),
	(2, 'How high is the tallest mountain?', '32,054 feet', 1, 2),
	(3, 'What is the most popular topping on a Whopper?', 'Pickles', 1, 3);
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;

-- Dumping structure for table trivia.rounds
CREATE TABLE IF NOT EXISTS `rounds` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `season_id` smallint(5) unsigned DEFAULT NULL,
  `trivia_date` datetime DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

-- Dumping data for table trivia.rounds: ~3 rows (approximately)
/*!40000 ALTER TABLE `rounds` DISABLE KEYS */;
INSERT INTO `rounds` (`id`, `season_id`, `trivia_date`, `active`) VALUES
	(1, 1, '2020-05-24 21:33:32', 1),
	(2, 1, '2020-06-04 21:33:47', 0),
	(3, 1, '2020-06-11 21:33:47', 0);
/*!40000 ALTER TABLE `rounds` ENABLE KEYS */;

-- Dumping structure for table trivia.seasons
CREATE TABLE IF NOT EXISTS `seasons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table trivia.seasons: ~2 rows (approximately)
/*!40000 ALTER TABLE `seasons` DISABLE KEYS */;
INSERT INTO `seasons` (`id`, `name`, `active`) VALUES
	(1, 'Season 1', 1),
	(2, 'Season 2', 0);
/*!40000 ALTER TABLE `seasons` ENABLE KEYS */;

-- Dumping structure for table trivia.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

-- Dumping data for table trivia.users: ~1 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `name`, `active`) VALUES
	(2, 'Jorge', 1);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
