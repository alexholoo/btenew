USE bte;

ALTER TABLE `master_order`
	ADD COLUMN `reference_id` VARCHAR(40) NOT NULL AFTER `shipping`;

ALTER TABLE `master_order`
	ADD INDEX `reference_id` (`reference_id`);
