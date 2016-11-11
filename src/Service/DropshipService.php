<?php

namespace Service;

use Phalcon\Di\Injectable;

class DropshipService extends Injectable
{
    public function getMultiItemOrders()
    {
        $result = [];

        $sql = 'SELECT order_id, count(*) as c FROM ca_order_notes GROUP BY order_id HAVING c>1';
        $orders = $this->db->fetchAll($sql);

        foreach ($orders as $order) {
            $result[] = $order['order_id'];
        }

        return $result;
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
        $result = $this->db->query($sql);

        $dates = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $dates[] = $row['date'];
        }

        return $dates;
    }

    public function getOrders($params)
    {
        $date      = $params['date'];
        $stage     = $params['stage'];
        $overstock = $params['overstock'];
        $express   = $params['express'];
        $multitem  = $params['multitem'];
        $orderId   = $params['orderId'];

        $multiItemOrders = $this->getMultiItemOrders();

        $sql = 'SELECT * FROM ca_order_notes';

        $where = [];

        if ($date != 'all') {
            $where[] = "date = '$date'";
        }

        #if ($stage != 'all') {
        #    $where[] = "status = '$stage'";
        #}

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

            $row['related_sku'] = explode('|', $row['related_sku']);
            $row['related_sku'] = array_map('trim', $row['related_sku']);
            $row['related_sku'] = array_filter($row['related_sku']);

            if ($stage == 'all' || $stage == $row['status']) {
                $data[] = $row;
            }
        }

        return $data;
    }
}
