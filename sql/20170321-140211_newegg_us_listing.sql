USE bte;

CREATE TABLE IF NOT EXISTS `newegg_us_listing` (
	`sku` VARCHAR(40) NOT NULL,
	`newegg_item_id` VARCHAR(40) NOT NULL,
	`warehouse_location` VARCHAR(10) NOT NULL,
	`fulfillment_option` VARCHAR(20) NOT NULL,
	`inventory` VARCHAR(10) NOT NULL,
	PRIMARY KEY (`sku`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
