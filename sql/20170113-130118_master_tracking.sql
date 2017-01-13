USE bte;

CREATE TABLE IF NOT EXISTS `master_tracking` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`order_id` VARCHAR(40) NOT NULL,
	`carrier` VARCHAR(40) NOT NULL,
	`trackingnum` VARCHAR(40) NOT NULL,
	`site` VARCHAR(40) NOT NULL,
	`shipdate` DATE NOT NULL,
	`source` VARCHAR(40) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `order_id` (`order_id`),
	UNIQUE INDEX `trackingnum` (`trackingnum`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
