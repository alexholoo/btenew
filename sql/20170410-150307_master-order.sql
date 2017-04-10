USE bte;

ALTER TABLE `master_order`
	ADD COLUMN `reference` VARCHAR(40) NOT NULL AFTER `shipping`;

ALTER TABLE `master_order`
	ADD INDEX `reference` (`reference`);
