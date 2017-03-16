USE bte;

ALTER TABLE `all_mgn_orders`
	CHANGE COLUMN `mgn_invoice_id` `product_name` VARCHAR(200) NULL DEFAULT NULL AFTER `shipping`;
