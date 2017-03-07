USE bte;

CREATE TABLE IF NOT EXISTS `rakuten_us_listing` (
	`ListingId` VARCHAR(20) NOT NULL,
	`Sku` VARCHAR(40) NOT NULL,
	`Title` VARCHAR(200) NOT NULL,
	`ItemConditionId` VARCHAR(10) NOT NULL,
	`ItemCondition` VARCHAR(20) NOT NULL,
	`ListingStatusId` VARCHAR(10) NOT NULL,
	`ListingStatusName` VARCHAR(20) NOT NULL,
	`Quantity` VARCHAR(10) NOT NULL,
	`Price` VARCHAR(20) NOT NULL,
	`MAP` VARCHAR(20) NOT NULL,
	`MapTypeId` VARCHAR(10) NOT NULL,
	`MapTypeName` VARCHAR(20) NOT NULL,
	`OriginalPrice` VARCHAR(10) NOT NULL,
	`PriceReferenceId` VARCHAR(10) NOT NULL,
	`OfferExpeditedShipping` VARCHAR(10) NOT NULL,
	`OfferTwoDayShipping` VARCHAR(10) NOT NULL,
	`OfferOneDayShipping` VARCHAR(10) NOT NULL,
	`ShippingRateStandard` VARCHAR(10) NOT NULL,
	`ShippingRateExpedited` VARCHAR(10) NOT NULL,
	`ShippingRateTwoDay` VARCHAR(10) NOT NULL,
	`ShippingRateOneDay` VARCHAR(10) NOT NULL,
	`ShippingLeadTime` VARCHAR(20) NOT NULL,
	`ReferenceId` VARCHAR(40) NOT NULL,
	`DateModified` VARCHAR(40) NOT NULL,
	`SellerSku` VARCHAR(40) NOT NULL,
	`MSRP` VARCHAR(10) NOT NULL,
	`Weight` VARCHAR(10) NOT NULL,
	`TaxonomyCategoryId` VARCHAR(10) NOT NULL,
	`TaxonomyCategoryPath` VARCHAR(200) NOT NULL,
	`PartitionID` VARCHAR(10) NOT NULL,
	`PartitionName` VARCHAR(80) NOT NULL,
	`UPC` VARCHAR(20) NOT NULL,
	`MfgName` VARCHAR(40) NOT NULL,
	`MfgPartNo` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`Sku`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
