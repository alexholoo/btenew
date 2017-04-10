USE bte;

ALTER TABLE `master_order_status`
	ALTER `order_item_id` DROP DEFAULT;
ALTER TABLE `master_order_status`
	CHANGE COLUMN `order_item_id` `order_item_id` VARCHAR(40) NOT NULL COMMENT 'master_order_item.id' FIRST;


ALTER TABLE `master_order_item`
	ADD COLUMN `order_item_id` VARCHAR(40) NOT NULL AFTER `order_id`;
