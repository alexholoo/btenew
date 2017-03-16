USE bte;

ALTER TABLE `chitchat`
	ADD COLUMN `seqno` INT NOT NULL AUTO_INCREMENT FIRST,
	DROP PRIMARY KEY,
	ADD PRIMARY KEY (`seqno`),
	ADD UNIQUE INDEX `trackingnum` (`trackingnum`);
