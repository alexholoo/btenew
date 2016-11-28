USE bte;

ALTER TABLE `master_order_item`
	ADD COLUMN `product_name` VARCHAR(200) NOT NULL AFTER `qty`;

ALTER TABLE `master_order_status`
	ADD COLUMN `ship_method` VARCHAR(40) NOT NULL AFTER `invoice`;
