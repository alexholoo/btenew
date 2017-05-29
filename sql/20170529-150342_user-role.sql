USE bte;

-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table bte.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(80) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(80) NOT NULL,
  `role` varchar(40) NOT NULL,
  `active` char(1) NOT NULL DEFAULT 'Y',
  `createdon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedon` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- Dumping data for table bte.users: ~8 rows (approximately)
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `active`, `createdon`, `updatedon`) VALUES
	(1, 'roy', 'roy@btecomputer.com', '9ad57e32ca0b55394c50b6432e68c0924fa5add9c6178745e533d7607f4a38e0', '1', 'Y', '2016-09-24 09:48:37', '2016-09-24 09:48:37'),
	(2, 'candy', 'candy@btecomputer.com', '7d46392f594b19f98efb79f0a3d9cddd4be1fbc79f51f98e1f58f62dd0cddcdd', '1', 'Y', '2016-09-24 09:48:37', '2016-09-24 09:48:37'),
	(3, 'hsli', 'hsli@btecomputer.com', '8b8246744dfb1568453f09129666ec5d86ff88d9cba1de9863f59dfd4ba66f7c', '1', 'Y', '2017-05-17 12:01:14', '2017-05-17 12:01:14'),
	(4, 'clarence', 'clarence@btecomputer.com', 'ad753d726e5ab2f4cd3db247b6ad697940bdb06c6f1f353c448559e9c264d8e7', '2', 'Y', '2017-05-17 12:01:39', '2017-05-17 12:01:39'),
	(5, 'doris', 'doris@btecomputer.com', 'e1218534e4cc82f3910496b305a5614700d115e2608d108d4e983a0614cc14f4', '2', 'Y', '2017-05-17 12:01:49', '2017-05-17 12:01:49'),
	(6, 'hangli', 'hangli@btecomputer.com', '869f1a37db3dff245957f151c66e63885602b85531d8c567d1e7fa782e08b2ee', '7', 'Y', '2017-05-17 12:09:40', '2017-05-17 12:09:40'),
	(7, 'tang', 'tang@btecomputer.com', '6709a92945eb35ac2918a046d5f4c0bbfe7409bede43cd7e22c12461618c9b2c', '3', 'Y', '2017-05-17 12:10:04', '2017-05-17 12:10:04'),
	(8, 'roman', 'roman@btecomputer.com', '4d72990af17cc63c8240b1c28a62e9012799624639db07b5335b9d1cdafd6aac', '4', 'Y', '2017-05-17 12:10:27', '2017-05-17 12:10:27'),
	(9, 'angie', 'angie@btecomputer.com', 'f56419b004c1c7a76f5d47a68d6bae55fecb61e1e81af10963b0fd6d50a044b0', '6', 'Y', '2017-05-17 12:11:55', '2017-05-17 12:11:55'),
	(10, 'jessie', 'jessie@btecomputer.com', 'e1480a33ea19bb85053a52c24edc85a34fead294ecc8505fd9ff1648006bf768', '5', 'Y', '2017-05-17 12:12:33', '2017-05-17 12:12:33'),
	(11, 'hector', 'hector@btecomputer.com', '8982d01d3bd36997c7aa577efd8b904f6496ac849c1cd9ac648d2d6c37099669', '6', 'Y', '2017-05-17 12:13:17', '2017-05-17 12:13:17'),
	(12, 'jessica', 'jessica@btecomputer.com', '929252f4083cac47f039f617f4113481c2f344eac11793ec94cf878c87233d38', '2', 'Y', '2017-05-17 12:16:30', '2017-05-17 12:16:30');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.4.0.5125
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table bte.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Dumping data for table bte.roles: ~6 rows (approximately)
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` (`id`, `name`) VALUES
	(1, 'Admin'),
	(2, 'Purchase'),
	(3, 'Marketing'),
	(4, 'Warehouse'),
	(5, 'Shipment'),
	(6, 'RMA'),
	(7, 'CSR');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
