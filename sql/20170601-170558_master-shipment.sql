USE bte;

CREATE TABLE IF NOT EXISTS `master_shipment` (
	`tracking_number` VARCHAR(40) NOT NULL,
	`order_id` VARCHAR(40) NOT NULL,
	`carrier` VARCHAR(20) NOT NULL,
	`site` VARCHAR(20) NOT NULL,
	`createdon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`tracking_number`),
	INDEX `order_id` (`order_id`)
)
COLLATE='utf8_general_ci' ENGINE=InnoDB;
