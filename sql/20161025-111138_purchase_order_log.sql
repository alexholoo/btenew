USE bte;

ALTER TABLE `purchase_order_log`
	CHANGE COLUMN `status` `invoice` VARCHAR(40) NOT NULL DEFAULT '' AFTER `ponumber`,
	CHANGE COLUMN `trackingnum` `shipped` TINYINT NOT NULL DEFAULT '0' AFTER `invoice`;
