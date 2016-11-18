<?php

namespace Service;

use Phalcon\Di\Injectable;

class DropshipService extends Injectable
{
    public function getMultiItemOrders()
    {
        $sql = 'SELECT order_id, count(*) AS c FROM ca_order_notes GROUP BY order_id HAVING c>1';
        $orders = $this->db->fetchAll($sql);

        return array_column($orders, 'order_id');
    }

    public function getOrdersInShoppingCart($column = 'orderId')
    {
        // get all orders that are not dropshipped
        $sql = 'SELECT sc.order_id as orderId, sc.sku, sc.qty
                  FROM shopping_cart sc
             LEFT JOIN purchase_order_log po ON sc.order_id = po.orderid
                 WHERE po.id IS NULL';

        $orders = $this->db->fetchAll($sql);

        if ($column) {
            return array_column($orders, $column);
        }

        return $orders;
    }

    public function removeOrdersInShoppingCart($orderId = '')
    {
        $sql = 'DELETE FROM shopping_cart';

        if ($orderId) {
            $sql .= " WHERE order_id='$orderId'";
        }

        return $this->db->execute($sql);
    }

    public function isOrderPurchased($orderId)
    {
        $sql = "SELECT orderid, sku FROM purchase_order_log WHERE orderid='$orderId'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    public function getDateListFromOrders()
    {
        $sql = 'SELECT DISTINCT date FROM ca_order_notes ORDER BY date DESC LIMIT 30';
        $result = $this->db->fetchAll($sql);

        return array_column($result, 'date');
    }

    public function getOrders($params)
    {
        $date      = $params['date'];
        $status    = $params['status'];
        $overstock = $params['overstock'];
        $express   = $params['express'];
        $multitem  = $params['multitem'];
        $orderId   = $params['orderId'];

        $multiItemOrders = $this->getMultiItemOrders();
        $ordersInShoppingCart = $this->getOrdersInShoppingCart();

        $sql = 'SELECT * FROM ca_order_notes';

        $where = [];

        if ($date != 'all') {
            $where[] = "date = '$date'";
        }

        if ($overstock) {
            $where[] = "stock_status = 'overstock'";
        }

        if ($express) {
            $where[] = "express = 1";
        }

        if ($multitem) {
            $list = "('" . implode("', '", $multiItemOrders) . "')";
            $where[] = "order_id in $list";
        }

        if ($orderId) {
            $where = []; // ignore all other conditions
            $where[] = "order_id LIKE '%$orderId' ORDER BY id DESC";
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $result = $this->db->query($sql);

        $data = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            // is the order already purchased?
            $row['status'] = 'pending';
            if (($purchase = $this->isOrderPurchased($row['order_id']))) {
                $row['status'] = 'purchased';
                $row['actual_sku'] = $purchase['sku'];
            }

            // is the order a multi-item order?
            $row['multi_items'] = false;
            if (in_array($row['order_id'], $multiItemOrders)) {
                $row['multi_items'] = true;
            }

            // is the order in shopping cart?
            $row['in_cart'] = false;
            if (in_array($row['order_id'], $ordersInShoppingCart)) {
                $row['in_cart'] = true;
            }

            $row['related_sku'] = explode('|', $row['related_sku']);
            $row['related_sku'] = array_map('trim', $row['related_sku']);
            $row['related_sku'] = array_filter($row['related_sku']);

            if ($row['stock_status'] == 'overstock') {
                array_push($row['related_sku'], 'BTE-overstock');
            }

            if (empty($row['related_sku'])) {
                array_push($row['related_sku'], 'SKU-not-avail');
            }

            if ($status == 'all' || $status == $row['status']) {
                $data[] = $row;
            }
        }

        return $data;
    }
}
