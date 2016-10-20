USE bte;

CREATE TABLE IF NOT EXISTS `ebay_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` varchar(40) NOT NULL,
  `SKU` varchar(40) NOT NULL,
  `QuantityPurchased` int(11) NOT NULL,
  `TransactionID` varchar(20) NOT NULL,
  `TransactionPrice` float(10,2) NOT NULL,
  `Tracking` varchar(20) NOT NULL,
  `ItemID` varchar(20) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `RecordNumber` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
