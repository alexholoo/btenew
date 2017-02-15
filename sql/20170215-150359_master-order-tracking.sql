USE bte;

CREATE TABLE `master_order_tracking` (
	`order_id` VARCHAR(40) NOT NULL,
	`ship_date` DATE NOT NULL,
	`carrier` VARCHAR(40) NOT NULL,
	`ship_method` VARCHAR(40) NOT NULL DEFAULT '',
	`tracking_number` VARCHAR(40) NOT NULL,
	`sender` VARCHAR(40) NOT NULL COMMENT 'BTE/DH-DS/SYN-DS/TD-DS',
	`createdon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`order_id`),
	UNIQUE INDEX `trackingnum` (`tracking_number`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
