<?php

namespace Service;

use Phalcon\Di\Injectable;

class ShoppingCartService extends Injectable
{
    public function getOrders($column = 'orderId')
    {
        // get all orders that are not dropshipped
        #$sql = 'SELECT sc.order_id as orderId, sc.sku, sc.qty
        #          FROM shopping_cart sc
        #     LEFT JOIN purchase_order_log po ON sc.order_id = po.orderid
        #         WHERE po.id IS NULL';

        // get all orders that are not checked out 
        $sql = 'SELECT order_id as orderId, sku, qty
                  FROM shopping_cart
                 WHERE checkedout=0 AND DATE(createdon)=DATE(NOW())';

        $orders = $this->db->fetchAll($sql);

        if ($column) {
            return array_column($orders, $column);
        }

        return $orders;
    }

    public function removeOrder($orderId = '')
    {
        $sql = 'DELETE FROM shopping_cart';

        if ($orderId) {
            $sql .= " WHERE order_id='$orderId' AND checkedout=0";
        }

        return $this->db->execute($sql);
    }

    public function markOrderAsCheckedout($orderId = '')
    {
        $sql = 'UPDATE shopping_cart SET checkedout=1 ';

        $where = 'WHERE ';
        if ($orderId) {
            $where .= "order_id='$orderId' AND ";
        }
        $where .= 'DATE(createdon)=DATE(NOW())';

        $sql .= $where;

        return $this->db->execute($sql);
    }
}
