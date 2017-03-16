USE bte;

ALTER TABLE `purchase_order_log`
	ADD COLUMN `flag` VARCHAR(20) NOT NULL DEFAULT 'dropship' AFTER `invoice`;
