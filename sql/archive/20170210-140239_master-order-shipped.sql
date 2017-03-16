USE bte;

CREATE TABLE `master_order_shipped` (
	`order_id` VARCHAR(40) NOT NULL,
	`createdon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`order_id`)
) ENGINE=InnoDB;
