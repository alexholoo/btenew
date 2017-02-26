-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               10.1.16-MariaDB - mariadb.org binary distribution
-- Server OS:                    Win32
-- HeidiSQL Version:             9.3.0.4984
-- --------------------------------------------------------

-- Dumping structure for table bte.amazon_ca_order_report
CREATE TABLE IF NOT EXISTS `amazon_ca_order_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(40) NOT NULL,
  `order_item_id` varchar(20) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payments_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `buyer_email` varchar(80) NOT NULL,
  `buyer_name` varchar(40) NOT NULL,
  `buyer_phone_number` varchar(20) NOT NULL,
  `sku` varchar(40) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `quantity_purchased` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `item_price` float(10,2) NOT NULL,
  `item_tax` float(10,2) NOT NULL,
  `shipping_price` float(10,2) NOT NULL,
  `shipping_tax` float(10,2) NOT NULL,
  `ship_service_level` varchar(20) NOT NULL,
  `recipient_name` varchar(40) NOT NULL,
  `ship_address_1` varchar(80) NOT NULL,
  `ship_address_2` varchar(80) NOT NULL,
  `ship_address_3` varchar(80) NOT NULL,
  `ship_city` varchar(20) NOT NULL,
  `ship_state` varchar(20) NOT NULL,
  `ship_postal_code` varchar(10) NOT NULL,
  `ship_country` varchar(20) NOT NULL,
  `ship_phone_number` varchar(20) NOT NULL,
  `item_promotion_discount` float(10,2) NOT NULL,
  `item_promotion_id` varchar(20) NOT NULL,
  `ship_promotion_discount` float(10,2) NOT NULL,
  `ship_promotion_id` varchar(20) NOT NULL,
  `delivery_start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `delivery_end_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `delivery_time_zone` varchar(20) NOT NULL,
  `delivery_instructions` varchar(40) NOT NULL,
  `sales_channel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.amazon_order
CREATE TABLE IF NOT EXISTS `amazon_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Channel` varchar(20) NOT NULL,
  `OrderId` varchar(20) NOT NULL,
  `PurchaseDate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `LastUpdateDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `OrderStatus` varchar(20) NOT NULL,
  `FulfillmentChannel` varchar(4) NOT NULL,
  `SalesChannel` varchar(20) NOT NULL,
  `ShipServiceLevel` varchar(40) NOT NULL,
  `CurrencyCode` varchar(3) NOT NULL,
  `OrderTotalAmount` float(10,2) NOT NULL,
  `NumberOfItemsShipped` int(11) NOT NULL,
  `NumberOfItemsUnshipped` int(11) NOT NULL,
  `PaymentMethod` varchar(20) NOT NULL,
  `BuyerName` varchar(80) NOT NULL,
  `BuyerEmail` varchar(80) NOT NULL,
  `ShipmentServiceLevelCategory` varchar(20) NOT NULL,
  `ShippedByAmazonTFM` char(1) NOT NULL,
  `OrderType` varchar(20) NOT NULL,
  `EarliestShipDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LatestShipDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `EarliestDeliveryDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `LatestDeliveryDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `IsBusinessOrder` char(1) NOT NULL,
  `IsPrime` char(1) NOT NULL,
  `IsPremiumOrder` char(1) NOT NULL,
  `CreatedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderId` (`OrderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.amazon_order_item
CREATE TABLE IF NOT EXISTS `amazon_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderId` varchar(20) NOT NULL,
  `ASIN` varchar(20) NOT NULL,
  `SellerSKU` varchar(40) NOT NULL,
  `OrderItemId` varchar(40) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `QuantityOrdered` int(11) NOT NULL,
  `QuantityShipped` int(11) NOT NULL,
  `CurrencyCode` varchar(3) NOT NULL,
  `ItemPrice` float(10,2) NOT NULL,
  `ShippingPrice` float(10,2) NOT NULL,
  `GiftWrapPrice` float(10,2) NOT NULL,
  `ItemTax` float(10,2) NOT NULL,
  `ShippingTax` float(10,2) NOT NULL,
  `GiftWrapTax` float(10,2) NOT NULL,
  `ShippingDiscount` float(10,2) NOT NULL,
  `PromotionDiscount` float(10,2) NOT NULL,
  `ConditionId` varchar(20) NOT NULL,
  `ConditionSubtypeId` varchar(20) NOT NULL,
  `ConditionNote` varchar(200) NOT NULL,
  `CreatedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `OrderId` (`OrderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.amazon_order_shipping_address
CREATE TABLE IF NOT EXISTS `amazon_order_shipping_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderId` varchar(20) NOT NULL,
  `Name` varchar(80) NOT NULL,
  `AddressLine1` varchar(40) NOT NULL,
  `AddressLine2` varchar(40) NOT NULL,
  `AddressLine3` varchar(40) NOT NULL,
  `City` varchar(20) NOT NULL,
  `County` varchar(20) NOT NULL,
  `District` varchar(20) NOT NULL,
  `StateOrRegion` varchar(20) NOT NULL,
  `PostalCode` varchar(10) NOT NULL,
  `CountryCode` varchar(2) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderId` (`OrderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.amazon_us_order_report
CREATE TABLE IF NOT EXISTS `amazon_us_order_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` varchar(40) NOT NULL,
  `order_item_id` varchar(20) NOT NULL,
  `purchase_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payments_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `buyer_email` varchar(80) NOT NULL,
  `buyer_name` varchar(40) NOT NULL,
  `buyer_phone_number` varchar(20) NOT NULL,
  `sku` varchar(40) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `quantity_purchased` int(11) NOT NULL,
  `currency` varchar(10) NOT NULL,
  `item_price` float(10,2) NOT NULL,
  `item_tax` float(10,2) NOT NULL,
  `shipping_price` float(10,2) NOT NULL,
  `shipping_tax` float(10,2) NOT NULL,
  `ship_service_level` varchar(20) NOT NULL,
  `recipient_name` varchar(40) NOT NULL,
  `ship_address_1` varchar(80) NOT NULL,
  `ship_address_2` varchar(80) NOT NULL,
  `ship_address_3` varchar(80) NOT NULL,
  `ship_city` varchar(20) NOT NULL,
  `ship_state` varchar(20) NOT NULL,
  `ship_postal_code` varchar(10) NOT NULL,
  `ship_country` varchar(20) NOT NULL,
  `ship_phone_number` varchar(20) NOT NULL,
  `item_promotion_discount` float(10,2) NOT NULL,
  `item_promotion_id` varchar(20) NOT NULL,
  `ship_promotion_discount` float(10,2) NOT NULL,
  `ship_promotion_id` varchar(20) NOT NULL,
  `delivery_start_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `delivery_end_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `delivery_time_zone` varchar(20) NOT NULL,
  `delivery_instructions` varchar(40) NOT NULL,
  `sales_channel` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.ebay_order_item
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

-- Dumping structure for table bte.ebay_order_report_bte
CREATE TABLE IF NOT EXISTS `ebay_order_report_bte` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` varchar(40) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `BuyerUsername` varchar(40) NOT NULL,
  `DatePaid` date NOT NULL,
  `Currency` varchar(10) NOT NULL,
  `AmountPaid` float(10,2) NOT NULL,
  `SalesTaxAmount` float(10,2) NOT NULL,
  `ShippingService` varchar(40) NOT NULL,
  `ShippingServiceCost` float(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.ebay_order_report_odo
CREATE TABLE IF NOT EXISTS `ebay_order_report_odo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderID` varchar(40) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `BuyerUsername` varchar(40) NOT NULL,
  `DatePaid` date NOT NULL,
  `Currency` varchar(10) NOT NULL,
  `AmountPaid` float(10,2) NOT NULL,
  `SalesTaxAmount` float(10,2) NOT NULL,
  `ShippingService` varchar(40) NOT NULL,
  `ShippingServiceCost` float(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderID` (`OrderID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.ebay_order_shipping_address
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

-- Dumping structure for table bte.newegg_order_file
CREATE TABLE IF NOT EXISTS `newegg_order_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.newegg_order_item
CREATE TABLE IF NOT EXISTS `newegg_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNumber` varchar(40) NOT NULL,
  `ItemSellerPartNo` varchar(40) NOT NULL,
  `ItemNeweggNo` varchar(40) NOT NULL,
  `ItemUnitPrice` float(10,2) NOT NULL,
  `ExtendUnitPrice` float(10,2) NOT NULL,
  `ItemUnitShippingCharge` float(10,2) NOT NULL,
  `ExtendShippingCharge` float(10,2) NOT NULL,
  `QuantityOrdered` int(11) NOT NULL,
  `QuantityShipped` int(11) NOT NULL,
  `ShipDate` date NOT NULL,
  `ActualShippingCarrier` varchar(40) NOT NULL,
  `ActualShippingMethod` varchar(40) NOT NULL,
  `TrackingNumber` varchar(40) NOT NULL,
  `ShipFromAddress` varchar(80) NOT NULL,
  `ShipFromCity` varchar(20) NOT NULL,
  `ShipFromState` varchar(20) NOT NULL,
  `ShipFromZipcode` varchar(10) NOT NULL,
  `ShipFromName` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `OrderNumber` (`OrderNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.newegg_order_report
CREATE TABLE IF NOT EXISTS `newegg_order_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNumber` varchar(40) NOT NULL,
  `OrderDateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `SalesChannel` varchar(10) NOT NULL,
  `FulfillmentOption` varchar(20) NOT NULL,
  `OrderCustomerEmail` varchar(80) NOT NULL,
  `OrderShippingMethod` varchar(40) NOT NULL,
  `OrderShippingTotal` float(10,2) NOT NULL,
  `GSTorHSTTotal` float(10,2) NOT NULL,
  `PSTorQSTTotal` float(10,2) NOT NULL,
  `OrderTotal` float(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderNumber` (`OrderNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.newegg_order_shipping_address
CREATE TABLE IF NOT EXISTS `newegg_order_shipping_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderNumber` varchar(40) NOT NULL,
  `ShipToAddressLine1` varchar(80) NOT NULL,
  `ShipToAddressLine2` varchar(80) NOT NULL,
  `ShipToCity` varchar(20) NOT NULL,
  `ShipToState` varchar(20) NOT NULL,
  `ShipToZipCode` varchar(10) NOT NULL,
  `ShipToCountry` varchar(20) NOT NULL,
  `ShipToFirstName` varchar(20) NOT NULL,
  `ShipToLastName` varchar(20) NOT NULL,
  `ShipToCompany` varchar(40) NOT NULL,
  `ShipToPhoneNumber` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `OrderNumber` (`OrderNumber`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.rakuten_order_file
CREATE TABLE IF NOT EXISTS `rakuten_order_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.rakuten_order_item
CREATE TABLE IF NOT EXISTS `rakuten_order_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Receipt_ID` varchar(20) NOT NULL COMMENT 'Order ID',
  `Receipt_Item_ID` varchar(20) NOT NULL,
  `ListingID` varchar(20) NOT NULL,
  `Sku` varchar(40) NOT NULL,
  `ReferenceId` varchar(40) NOT NULL,
  `Quantity` int(11) NOT NULL,
  `Qty_Shipped` int(11) NOT NULL,
  `Qty_Cancelled` int(11) NOT NULL,
  `Title` varchar(200) NOT NULL,
  `Price` float(10,2) NOT NULL,
  `Product_Rev` float(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Receipt_ID` (`Receipt_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.rakuten_order_report
CREATE TABLE IF NOT EXISTS `rakuten_order_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `SellerShopperNumber` varchar(20) NOT NULL,
  `Receipt_ID` varchar(20) NOT NULL COMMENT 'Order ID',
  `Date_Entered` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Shipping_Cost` float(10,2) NOT NULL,
  `ProductOwed` float(10,2) NOT NULL,
  `ShippingOwed` float(10,2) NOT NULL,
  `Commission` float(10,2) NOT NULL,
  `ShippingFee` float(10,2) NOT NULL,
  `PerItemFee` float(10,2) NOT NULL,
  `Tax_Cost` float(10,2) NOT NULL,
  `Bill_To_Company` varchar(40) NOT NULL,
  `Bill_To_Phone` varchar(20) NOT NULL,
  `Bill_To_Fname` varchar(20) NOT NULL,
  `Bill_To_Lname` varchar(20) NOT NULL,
  `Email` varchar(80) NOT NULL,
  `ShippingMethodId` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Receipt_ID` (`Receipt_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Dumping structure for table bte.rakuten_order_shipping_address
CREATE TABLE IF NOT EXISTS `rakuten_order_shipping_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Receipt_ID` varchar(20) NOT NULL COMMENT 'Order ID',
  `Ship_To_Name` varchar(40) NOT NULL,
  `Ship_To_Company` varchar(40) NOT NULL,
  `Ship_To_Street1` varchar(40) NOT NULL,
  `Ship_To_Street2` varchar(40) NOT NULL,
  `Ship_To_City` varchar(20) NOT NULL,
  `Ship_To_State` varchar(20) NOT NULL,
  `Ship_To_Zip` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Receipt_ID` (`Receipt_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

