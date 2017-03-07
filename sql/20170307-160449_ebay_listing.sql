USE bte;

CREATE TABLE IF NOT EXISTS `ebay_bte_listing` (
	`sku` VARCHAR(40) NOT NULL,
	`item_id` VARCHAR(20) NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`price` VARCHAR(20) NOT NULL,
	`qty` VARCHAR(10) NOT NULL,
	PRIMARY KEY (`sku`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

CREATE TABLE IF NOT EXISTS `ebay_odo_listing` (
	`sku` VARCHAR(40) NOT NULL,
	`item_id` VARCHAR(20) NOT NULL,
	`title` VARCHAR(200) NOT NULL,
	`price` VARCHAR(20) NOT NULL,
	`qty` VARCHAR(10) NOT NULL,
	PRIMARY KEY (`sku`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
