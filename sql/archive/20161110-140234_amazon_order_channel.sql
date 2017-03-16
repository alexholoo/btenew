USE bte;

ALTER TABLE `amazon_order`
	ADD COLUMN `Channel` VARCHAR(20) NOT NULL AFTER `id`;
