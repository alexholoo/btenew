USE bte;

CREATE TABLE IF NOT EXISTS `ebay_order_report_odo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ExtOrderID` varchar(40) NOT NULL,
  `OrderID` varchar(40) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `BuyerUsername` varchar(40) NOT NULL,
  `DatePaid` date NOT NULL,
  `Currency` varchar(10) NOT NULL,
  `AmountPaid` float(10,2) NOT NULL,
  `SalesTaxAmount` float(10,2) NOT NULL,
  `ShippingService` varchar(40) NOT NULL,
  `ShippingServiceCost` float(10,2) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `Address` varchar(80) NOT NULL,
  `Address2` varchar(80) NOT NULL,
  `City` varchar(20) NOT NULL,
  `Province` varchar(40) NOT NULL,
  `PostalCode` varchar(20) NOT NULL,
  `Country` varchar(20) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `QuantityPurchased` int(11) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `SKU` varchar(40) NOT NULL,
  `TransactionID` varchar(20) NOT NULL,
  `TransactionPrice` float(10,2) NOT NULL,
  `Tracking` varchar(20) NOT NULL,
  `ItemID` varchar(20) NOT NULL,
  `RecordNumber` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderID` (`ExtOrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;