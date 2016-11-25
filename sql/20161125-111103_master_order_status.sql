USE bte;

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
	`trackingnum` VARCHAR(40) NOT NULL,
	`remarks` VARCHAR(200) NOT NULL,
	`flag` VARCHAR(40) NOT NULL COMMENT 'auto/manual',
	`related_sku` VARCHAR(120) NOT NULL,
	`dimension` VARCHAR(60) NOT NULL,
	PRIMARY KEY (`order_item_id`),
	INDEX `order_id` (`order_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
