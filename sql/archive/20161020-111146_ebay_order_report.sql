USE bte;

ALTER TABLE `ebay_order_report_bte` 
    DROP COLUMN `ExtOrderID`,
    DROP COLUMN `Name`,
    DROP COLUMN `Address`,
    DROP COLUMN `Address2`,
    DROP COLUMN `City`,
    DROP COLUMN `Province`,
    DROP COLUMN `PostalCode`,
    DROP COLUMN `Country`,
    DROP COLUMN `Phone`,

    DROP COLUMN `SKU`,
    DROP COLUMN `QuantityPurchased`,
    DROP COLUMN `TransactionID`,
    DROP COLUMN `TransactionPrice`,
    DROP COLUMN `Tracking`,
    DROP COLUMN `ItemID`,
    DROP COLUMN `Email`,
    DROP COLUMN `RecordNumber`;

ALTER TABLE `ebay_order_report_bte` ADD UNIQUE INDEX `OrderID` (`OrderID`);

ALTER TABLE `ebay_order_report_odo`
    DROP COLUMN `ExtOrderID`,
    DROP COLUMN `Name`,
    DROP COLUMN `Address`,
    DROP COLUMN `Address2`,
    DROP COLUMN `City`,
    DROP COLUMN `Province`,
    DROP COLUMN `PostalCode`,
    DROP COLUMN `Country`,
    DROP COLUMN `Phone`,

    DROP COLUMN `SKU`,
    DROP COLUMN `QuantityPurchased`,
    DROP COLUMN `TransactionID`,
    DROP COLUMN `TransactionPrice`,
    DROP COLUMN `Tracking`,
    DROP COLUMN `ItemID`,
    DROP COLUMN `Email`,
    DROP COLUMN `RecordNumber`;

ALTER TABLE `ebay_order_report_odo` ADD UNIQUE INDEX `OrderID` (`OrderID`);
