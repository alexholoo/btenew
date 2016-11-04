USE bte;

CREATE TABLE IF NOT EXISTS `users` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`username` VARCHAR(40) NOT NULL,
	`email` VARCHAR(80) NOT NULL,
	`password` CHAR(80) NOT NULL,
	`role` VARCHAR(80) NOT NULL,
	`active` CHAR(1) NOT NULL DEFAULT 'Y',
	`createdon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	`updatedon` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `username` (`username`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `active`, `createdon`, `updatedon`) VALUES
	(1, 'testuser',  'testuser@email.com',  sha2('user123',  256), 'user',  'Y', NOW(), NOW()),
	(2, 'testadmin', 'testadmin@email.com', sha2('admin123', 256), 'admin', 'Y', NOW(), NOW());
