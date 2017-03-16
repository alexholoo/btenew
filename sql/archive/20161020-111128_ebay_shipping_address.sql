USE bte;

CREATE TABLE IF NOT EXISTS `ebay_order_shipping_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` varchar(40) NOT NULL,
  `Name` varchar(40) NOT NULL,
  `Address` varchar(80) NOT NULL,
  `Address2` varchar(80) NOT NULL,
  `City` varchar(20) NOT NULL,
  `Province` varchar(40) NOT NULL,
  `PostalCode` varchar(20) NOT NULL,
  `Country` varchar(20) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
