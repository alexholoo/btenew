USE bte;

ALTER TABLE `master_order_tracking`
	ALTER `carrier` DROP DEFAULT;
ALTER TABLE `master_order_tracking`
	CHANGE COLUMN `carrier` `carrier_code` VARCHAR(40) NOT NULL AFTER `ship_date`,
	ADD COLUMN `carrier_name` VARCHAR(40) NOT NULL AFTER `carrier_code`;
