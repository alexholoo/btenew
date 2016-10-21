USE bte;

CREATE TABLE `newegg_order_file` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`filename` VARCHAR(40) NULL DEFAULT NULL,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `filename` (`filename`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `newegg_order_report`
	DROP COLUMN `Filename`;
