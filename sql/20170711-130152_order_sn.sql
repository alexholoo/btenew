USE bte;

CREATE TABLE IF NOT EXISTS `order_sn` (
	`order_id` VARCHAR(40) NOT NULL,
	`sn` VARCHAR(40) NULL DEFAULT NULL,
	UNIQUE INDEX `order_id_sn` (`order_id`, `sn`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
