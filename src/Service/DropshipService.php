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
}
