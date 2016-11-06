USE bte;

ALTER TABLE `all_mgn_orders`
    DROP INDEX `order_id`,
    ADD INDEX `order_id` (`order_id`);

ALTER TABLE `ca_order_notes`
    DROP INDEX `order_id`,
    ADD INDEX `order_id` (`order_id`);
