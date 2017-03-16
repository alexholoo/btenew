USE bte;

CREATE TABLE IF NOT EXISTS `master_order` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`channel` VARCHAR(20) NOT NULL,
	`date` DATE NOT NULL,
	`order_id` VARCHAR(40) NOT NULL,
	`express` TINYINT(4) NOT NULL COMMENT 'boolean',
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
	`product_name` VARCHAR(200) NOT NULL,
	PRIMARY KEY (`id`),
	INDEX `order_id` (`order_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `master_order_shipping_address` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`date` DATE NOT NULL,
	`order_id` VARCHAR(40) NOT NULL,
	`buyer` VARCHAR(60) NOT NULL,
	`address` VARCHAR(120) NOT NULL,
	`city` VARCHAR(40) NOT NULL,
	`province` VARCHAR(20) NOT NULL,
	`postalcode` VARCHAR(20) NOT NULL,
	`country` VARCHAR(40) NOT NULL,
	`phone` VARCHAR(40) NOT NULL,
	`email` VARCHAR(80) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `order_id` (`order_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `master_order_status` (
	`order_item_id` INT(11) NOT NULL COMMENT 'master_order_item.id',
	`date` DATE NOT NULL,
	`channel` VARCHAR(20) NOT NULL,
	`order_id` VARCHAR(40) NOT NULL,
	`stock_status` VARCHAR(20) NOT NULL,
	`supplier` VARCHAR(20) NOT NULL COMMENT 'actual supplier',
	`supplier_sku` VARCHAR(40) NOT NULL COMMENT 'actual supplier sku',
	`mfrpn` VARCHAR(40) NOT NULL,
	`ponum` VARCHAR(20) NOT NULL,
	`invoice` VARCHAR(20) NOT NULL,
	`ship_method` VARCHAR(40) NOT NULL,
	`trackingnum` VARCHAR(40) NOT NULL,
	`remarks` VARCHAR(200) NOT NULL,
	`flag` VARCHAR(40) NOT NULL COMMENT 'auto/manual',
	`related_sku` VARCHAR(120) NOT NULL,
	`dimension` VARCHAR(60) NOT NULL,
	PRIMARY KEY (`order_item_id`),
	INDEX `order_id` (`order_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
