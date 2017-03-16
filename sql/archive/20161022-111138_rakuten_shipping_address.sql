USE bte;

CREATE TABLE IF NOT EXISTS `rakuten_order_shipping_address` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `Receipt_ID` VARCHAR(20) NOT NULL COMMENT 'Order ID',
    `Ship_To_Name` VARCHAR(40) NOT NULL,
    `Ship_To_Company` VARCHAR(40) NOT NULL,
    `Ship_To_Street1` VARCHAR(40) NOT NULL,
    `Ship_To_Street2` VARCHAR(40) NOT NULL,
    `Ship_To_City` VARCHAR(20) NOT NULL,
    `Ship_To_State` VARCHAR(20) NOT NULL,
    `Ship_To_Zip` VARCHAR(10) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `Receipt_ID` (`Receipt_ID`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
