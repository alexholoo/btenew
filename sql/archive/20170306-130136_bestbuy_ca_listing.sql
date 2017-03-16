USE bte;

CREATE TABLE IF NOT EXISTS `bestbuy_ca_listing` (
	`SKU` VARCHAR(40) NOT NULL,
	`Product_ID` VARCHAR(20) NOT NULL,
	`Category_Code` VARCHAR(20) NOT NULL,
	`Category_Label` VARCHAR(80) NOT NULL,
	`Product_Name` VARCHAR(200) NOT NULL,
	`Condition` VARCHAR(20) NOT NULL,
	`Price` VARCHAR(20) NOT NULL,
	`Qty` VARCHAR(20) NOT NULL,
	`Alert_Threshold` VARCHAR(20) NOT NULL,
	`Logistic_Class` VARCHAR(20) NOT NULL,
	`Activated` VARCHAR(20) NOT NULL,
	`Available_Start_Date` VARCHAR(20) NOT NULL,
	`Available_End_Date` VARCHAR(20) NOT NULL,
	PRIMARY KEY (`SKU`)
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB
;
