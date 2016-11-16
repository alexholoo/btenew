USE bte;

ALTER TABLE `purchase_order_log`
	DROP INDEX `ponumber`,
	ADD INDEX `ponumber` (`ponumber`);

ALTER TABLE `purchase_order_log`
	ALTER `orderid` DROP DEFAULT;

ALTER TABLE `purchase_order_log`
	CHANGE COLUMN `orderid` `orderid` VARCHAR(40) NOT NULL AFTER `time`;
