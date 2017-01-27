USE bte;

ALTER TABLE `chitchat`
	DROP COLUMN `id`,
	DROP COLUMN `order_id`,
	DROP COLUMN `carrier`,
	DROP COLUMN `site`,
	DROP COLUMN `date`,
	DROP COLUMN `source`,
	DROP COLUMN `contact`,
	DROP COLUMN `address`,
	DROP COLUMN `address2`,
	DROP COLUMN `city`,
	DROP COLUMN `province`,
	DROP COLUMN `postalcode`,
	DROP COLUMN `country`,
	DROP COLUMN `class`,
	DROP COLUMN `item`,
	DROP COLUMN `lbs`,
	DROP COLUMN `value`,
	DROP COLUMN `handling`,
	DROP COLUMN `sku`,
	DROP PRIMARY KEY,
	DROP INDEX `order_id`,
	DROP INDEX `trackingnum`,
	ADD PRIMARY KEY (`trackingnum`);
