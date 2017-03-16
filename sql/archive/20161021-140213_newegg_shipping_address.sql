USE bte;

CREATE TABLE IF NOT EXISTS `newegg_order_shipping_address` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`OrderNumber` VARCHAR(40) NOT NULL,
	`ShipToAddressLine1` VARCHAR(80) NOT NULL,
	`ShipToAddressLine2` VARCHAR(80) NOT NULL,
	`ShipToCity` VARCHAR(20) NOT NULL,
	`ShipToState` VARCHAR(20) NOT NULL,
	`ShipToZipCode` VARCHAR(10) NOT NULL,
	`ShipToCountry` VARCHAR(20) NOT NULL,
	`ShipToFirstName` VARCHAR(20) NOT NULL,
	`ShipToLastName` VARCHAR(20) NOT NULL,
	`ShipToCompany` VARCHAR(40) NOT NULL,
	`ShipToPhoneNumber` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `OrderNumber` (`OrderNumber`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
