USE bte;

ALTER TABLE `ca_order_notes`
	DROP COLUMN `status`,
	DROP COLUMN `actual_sku`;
