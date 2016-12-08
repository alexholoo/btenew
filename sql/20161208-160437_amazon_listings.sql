USE bte;

CREATE TABLE IF NOT EXISTS `amazon_ca_listings` (
	`sku` VARCHAR(40) NOT NULL,
	`asin` VARCHAR(20) NOT NULL,
	`price` VARCHAR(10) NOT NULL,
	`name` VARCHAR(80) NOT NULL,
	PRIMARY KEY (`sku`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `amazon_us_listings` (
	`sku` VARCHAR(40) NOT NULL,
	`asin` VARCHAR(20) NOT NULL,
	`price` VARCHAR(10) NOT NULL,
	`name` VARCHAR(80) NOT NULL,
	PRIMARY KEY (`sku`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
