USE bte;

CREATE TABLE IF NOT EXIST `master_order` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`channel` VARCHAR(20) NOT NULL,
	`date` DATE NOT NULL,
	`order_id` VARCHAR(40) NOT NULL,
	`express` TINYINT(4) NOT NULL,
	`buyer` VARCHAR(60) NOT NULL,
	`address` VARCHAR(80) NOT NULL,
	`city` VARCHAR(40) NOT NULL,
	`province` VARCHAR(20) NOT NULL,
	`postalcode` VARCHAR(20) NOT NULL,
	`country` VARCHAR(40) NOT NULL,
	`phone` VARCHAR(40) NOT NULL,
	`email` VARCHAR(80) NOT NULL,
	`shipping` VARCHAR(10) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `order_id` (`order_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `master_order_item` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`order_id` VARCHAR(40) NOT NULL,
	`sku` VARCHAR(40) NOT NULL,
	`price` VARCHAR(20) NOT NULL,
	`qty` VARCHAR(10) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `order_id_sku` (`order_id`, `sku`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
