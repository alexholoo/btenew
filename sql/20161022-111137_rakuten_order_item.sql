USE bte;

CREATE TABLE `rakuten_order_item` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `Receipt_ID` VARCHAR(20) NOT NULL COMMENT 'Order ID',
    `Receipt_Item_ID` VARCHAR(20) NOT NULL,
    `ListingID` VARCHAR(20) NOT NULL,
    `Sku` VARCHAR(40) NOT NULL,
    `ReferenceId` VARCHAR(40) NOT NULL,
    `Quantity` INT(11) NOT NULL,
    `Qty_Shipped` INT(11) NOT NULL,
    `Qty_Cancelled` INT(11) NOT NULL,
    `Title` VARCHAR(200) NOT NULL,
    `Price` FLOAT(10,2) NOT NULL,
    `Product_Rev` FLOAT(10,2) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `Receipt_ID` (`Receipt_ID`)
) COLLATE='utf8_general_ci' ENGINE=InnoDB;
