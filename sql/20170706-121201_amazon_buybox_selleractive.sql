USE bte;

CREATE TABLE `amazon_us_buybox_report_selleractive` (
	`sku` VARCHAR(40) NOT NULL,
	`title` VARCHAR(120) NOT NULL,
	`condition` VARCHAR(10) NOT NULL,
	`qty` INT(11) NOT NULL,
	`buybox_owned` VARCHAR(10) NOT NULL,
	`buybox_price` VARCHAR(10) NOT NULL,
	`low_new_price` VARCHAR(10) NOT NULL,
	`low_used_price` VARCHAR(10) NOT NULL,
	`current_price` VARCHAR(10) NOT NULL,
	`shipping_price` VARCHAR(10) NOT NULL,
	`price_rank` VARCHAR(10) NOT NULL,
	`sales_rank` VARCHAR(10) NOT NULL,
	`fulfilled_by` VARCHAR(10) NOT NULL,
	PRIMARY KEY (`sku`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;

CREATE TABLE amazon_ca_buybox_report_selleractive LIKE amazon_us_buybox_report_selleractive;
