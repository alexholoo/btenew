USE bte;

CREATE TABLE IF NOT EXISTS `role_permissions` (
	`id` INT(11) NOT NULL AUTO_INCREMENT,
	`role_id` INT(11) NOT NULL,
	`resource` VARCHAR(80) NOT NULL,
	PRIMARY KEY (`id`)
)
COLLATE='utf8_general_ci' ENGINE=InnoDB;
