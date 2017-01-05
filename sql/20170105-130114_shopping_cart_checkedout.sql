USE bte;

ALTER TABLE `shopping_cart`
	ADD COLUMN `checkedout` TINYINT NOT NULL DEFAULT '0' AFTER `qty`;
