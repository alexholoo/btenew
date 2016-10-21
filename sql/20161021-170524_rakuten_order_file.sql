USE bte;

CREATE TABLE `rakuten_order_file` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`filename` VARCHAR(60) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `filename` (`filename`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
