USE bte;

ALTER TABLE `amazon_asin_desc`
	ADD COLUMN `feature` TEXT NOT NULL AFTER `asin`,
	CHANGE COLUMN `desc` `description` TEXT NOT NULL AFTER `feature`;
