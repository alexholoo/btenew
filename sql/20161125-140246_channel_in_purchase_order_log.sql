USE bte;

ALTER TABLE `purchase_order_log`
	ADD COLUMN `channel` VARCHAR(20) NOT NULL DEFAULT 'Amazon-ACA' AFTER `time`;
