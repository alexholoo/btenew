USE bte;

ALTER TABLE `master_order`
	ADD COLUMN `express` TINYINT NOT NULL COMMENT 'boolean' AFTER `order_id`;
