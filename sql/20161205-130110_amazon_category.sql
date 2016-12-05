USE bte;

CREATE TABLE IF NOT EXISTS `amazon_category` (
	`category_id` VARCHAR(20) NOT NULL,
	`category_name` VARCHAR(80) NOT NULL,
	`parent_id` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`category_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `amazon_sku_category` (
	`sku` VARCHAR(20) NOT NULL,
	`category_id` VARCHAR(20) NOT NULL,
	`category_name` VARCHAR(80) NOT NULL,
	PRIMARY KEY (`sku`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
