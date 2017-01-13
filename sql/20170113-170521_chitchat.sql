USE bte;

CREATE TABLE IF NOT EXISTS `chitchat` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`order_id` VARCHAR(40) NOT NULL,
	`carrier` VARCHAR(40) NOT NULL,
	`trackingnum` VARCHAR(40) NOT NULL,
	`site` VARCHAR(40) NOT NULL,
	`date` DATE NOT NULL,
	`source` VARCHAR(40) NOT NULL,
	`contact` VARCHAR(40) NOT NULL,
	`address` VARCHAR(80) NOT NULL,
	`address2` VARCHAR(80) NOT NULL,
	`city` VARCHAR(40) NOT NULL,
	`province` VARCHAR(40) NOT NULL,
	`postalcode` VARCHAR(40) NOT NULL,
	`country` VARCHAR(40) NOT NULL,
	`class` VARCHAR(40) NOT NULL,
	`item` VARCHAR(40) NOT NULL,
	`lbs` VARCHAR(40) NOT NULL,
	`value` VARCHAR(40) NOT NULL,
	`handling` VARCHAR(40) NOT NULL,
	`sku` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `order_id` (`order_id`),
	UNIQUE INDEX `trackingnum` (`trackingnum`)
) ENGINE=InnoDB;
