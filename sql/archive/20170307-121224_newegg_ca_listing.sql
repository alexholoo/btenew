USE bte;

CREATE TABLE IF NOT EXISTS `newegg_ca_listing` (
	`sku` VARCHAR(40) NOT NULL,
	`newegg_item_id` VARCHAR(40) NOT NULL,
	`currency` VARCHAR(10) NOT NULL,
	`MSRP` VARCHAR(20) NOT NULL,
	`MAP` VARCHAR(20) NOT NULL,
	`checkout_map` VARCHAR(10) NOT NULL,
	`selling_price` VARCHAR(20) NOT NULL,
	`inventory` VARCHAR(40) NOT NULL,
	`fulfillment_option` VARCHAR(20) NOT NULL,
	`shipping` VARCHAR(20) NOT NULL,
	`activation_mark` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`sku`)
)
ENGINE=InnoDB
;
