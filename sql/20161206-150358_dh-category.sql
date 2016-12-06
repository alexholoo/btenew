USE bte;

CREATE TABLE IF NOT EXISTS `dh_category` (
	`category_id` VARCHAR(20) NOT NULL,
	`parent_id` VARCHAR(20) NOT NULL,
	`category_name` VARCHAR(80) NOT NULL,
	`category_desc` VARCHAR(200) NOT NULL,
	PRIMARY KEY (`category_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
