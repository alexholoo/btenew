USE bte;

CREATE TABLE `newegg_order_item` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`OrderNumber` VARCHAR(40) NOT NULL,
	`ItemSellerPartNo` VARCHAR(40) NOT NULL,
	`ItemNeweggNo` VARCHAR(40) NOT NULL,
	`ItemUnitPrice` FLOAT(10,2) NOT NULL,
	`ExtendUnitPrice` FLOAT(10,2) NOT NULL,
	`ItemUnitShippingCharge` FLOAT(10,2) NOT NULL,
	`ExtendShippingCharge` FLOAT(10,2) NOT NULL,
	`QuantityOrdered` INT(11) NOT NULL,
	`QuantityShipped` INT(11) NOT NULL,
	`ShipDate` DATE NOT NULL,
	`ActualShippingCarrier` VARCHAR(40) NOT NULL,
	`ActualShippingMethod` VARCHAR(40) NOT NULL,
	`TrackingNumber` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `OrderNumber` (`OrderNumber`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
