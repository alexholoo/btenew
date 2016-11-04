USE bte;

CREATE TABLE IF NOT EXISTS `master_order_shipping_address` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `date` DATE NOT NULL,
    `order_id` VARCHAR(40) NOT NULL,
    `buyer` VARCHAR(60) NOT NULL,
    `address` VARCHAR(120) NOT NULL,
    `city` VARCHAR(40) NOT NULL,
    `province` VARCHAR(20) NOT NULL,
    `postalcode` VARCHAR(20) NOT NULL,
    `country` VARCHAR(40) NOT NULL,
    `phone` VARCHAR(40) NOT NULL,
    `email` VARCHAR(80) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `order_id` (`order_id`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
