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

-- Dumping structure for table bte.keyword_bestbuy_category
CREATE TABLE IF NOT EXISTS `keyword_bestbuy_category` (
  `keyword` varchar(40) NOT NULL,
  `category` varchar(20) NOT NULL,
  `description` varchar(40) NOT NULL,
  PRIMARY KEY (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping data for table bte.keyword_bestbuy_category: ~0 rows (approximately)
/*!40000 ALTER TABLE `keyword_bestbuy_category` DISABLE KEYS */;
INSERT INTO `keyword_bestbuy_category` (`keyword`, `category`, `description`) VALUES
	('Audio Cable', 'CAT_315699', ''),
	('C14 TO C13', 'CAT_10494', ''),
	('C20 TO C19', 'CAT_10494', ''),
	('CABLE', 'CAT_10494', ''),
	('CARTRIDGE', 'CAT_1171', ''),
	('CASIO&CALCULATOR', 'CAT_1111', ''),
	('Cat5e', 'CAT_10494', ''),
	('Cat6', 'CAT_10494', ''),
	('CAT6E', 'CAT_10494', ''),
	('CPU AMD', 'CAT_10483', ''),
	('CPU INTEL', 'CAT_10483', ''),
	('Desktop Computer', 'CAT_1003', ''),
	('EARPHONE', 'CAT_321023', ''),
	('EPSON', 'CAT_26455', ''),
	('Ethernet&Adapter', 'CAT_19997', ''),
	('GB&DDR&MHZ', 'CAT_1076', ''),
	('GeForce', 'CAT_25617', ''),
	('Graphic&Card', 'CAT_25617', ''),
	('Hard Disk Drive', 'CAT_1084', ''),
	('Hard Drive', 'CAT_1084', ''),
	('HDD', 'CAT_1084', ''),
	('HDMI&CABLE', 'CAT_10494', ''),
	('HEADPHONE', 'CAT_321023', ''),
	('HEADSET', 'CAT_321023', ''),
	('HP&Laptop', 'CAT_1002', ''),
	('HP&Laser', 'CAT_26455', ''),
	('HP&Notebook', 'CAT_1002', ''),
	('IdeaPad', 'CAT_1002', ''),
	('In-Ear', 'CAT_321023', ''),
	('Inkjet&Printer', 'CAT_10781', ''),
	('Keyboard and Mouse Combo', 'CAT_14266', ''),
	('Label Printer', 'CAT_30057', ''),
	('Laptop Battery', 'CAT_25595', ''),
	('Laser&Printer', 'CAT_26455', ''),
	('Mini PC', 'CAT_1003', ''),
	('Monitor', 'CAT_1006', ''),
	('Motherboard', 'CAT_10481', ''),
	('MOUSE', 'CAT_14266', ''),
	('Multifunction Printer', 'CAT_26455', ''),
	('Network Adapter', 'CAT_19997', ''),
	('Network Cable', 'CAT_10494', ''),
	('Network Patch Cable', 'CAT_10494', ''),
	('NoteBook TP', 'CAT_1002', ''),
	('OfficeJet', 'CAT_10781', ''),
	('PHOTO PAPER', 'CAT_7673', ''),
	('Photo Printer', 'CAT_30057', ''),
	('Power Supply', 'CAT_10482', ''),
	('Processor', 'CAT_10483', ''),
	('PROJECTOR', 'CAT_1544', ''),
	('RJ45', 'CAT_10494', ''),
	('RJ45 NETWORK CABLE', 'CAT_10494', ''),
	('SATA SEAGATE', 'CAT_1084', ''),
	('SATA WD', 'CAT_1084', ''),
	('Scanner', 'CAT_1012', ''),
	('SNAGLESS&CABLE', 'CAT_10494', ''),
	('Solid State Drive', 'CAT_1084', ''),
	('Speaker', 'CAT_7675', ''),
	('SPORT&HeadPhone', 'CAT_321023', ''),
	('SSD', 'CAT_1084', ''),
	('Switch', 'CAT_10488', ''),
	('TONER CART', 'CAT_1171', ''),
	('Toner Cartridge', 'CAT_1171', ''),
	('USB&CABLE', 'CAT_10494', ''),
	('UTP CABLE', 'CAT_10494', ''),
	('VGA CABLE', 'CAT_10494', ''),
	('Video Card', 'CAT_25617', ''),
	('Wireless Headset', 'CAT_23274', ''),
	('Wireless&Mouse', 'CAT_14266', ''),
	('Wireless&Router', 'CAT_19994', '');
/*!40000 ALTER TABLE `keyword_bestbuy_category` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
