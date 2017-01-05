<?php

namespace Service;

use Phalcon\Di\Injectable;

class ShoppingCartService extends Injectable
{
    /**
     * Get orders in shopping cart (that created today, not checked out yet)
     */
    public function getOrders($column = '')
    {
        // get all orders that are not dropshipped
        #$sql = 'SELECT sc.order_id as orderId, sc.sku, sc.qty
        #          FROM shopping_cart sc
        #     LEFT JOIN purchase_order_log po ON sc.order_id = po.orderid
        #         WHERE po.id IS NULL';

        // get all orders that are created today and not checked out
        $sql = 'SELECT order_id as orderId, sku, qty
                  FROM shopping_cart
                 WHERE checkedout=0 AND DATE(createdon)=DATE(NOW())';

        $orders = $this->db->fetchAll($sql);

        if ($column) {
            // $column should be 'orderId' if specified
            return array_column($orders, $column);
        }

        return $orders;
    }

    /**
     * Add an order to shopping cart
     */
    public function addOrder($order)
    {
        $this->db->insertAsDict('shopping_cart', [
            'order_id' => $order['orderId'],
            'sku'      => $order['sku'],
            'qty'      => $order['qty']
        ]);
    }

    /**
     * Find an order by orderId in shopping cart
     */
    public function findOrder($orderId)
    {
        $result = $this->db->fetchOne("SELECT order_id FROM shopping_cart WHERE order_id='$orderId'");
        return $result;
    }

    /**
     * Delete order in shopping cart
     *
     * If orderId is not specified, delete all orders.
     *
     * It only affectes orders that created today and not checked out
     */
    public function removeOrder($orderId)
    {
        $sql = 'DELETE FROM shopping_cart ';

        $where = $this->getWhere($orderId);

        $this->db->execute($sql.$where);

        return $this->db->affectedRows();
    }

    /**
     * Mark order in shopping cart as checkedout
     *
     * If orderId is not specified, mark all orders.
     *
     * It only affectes orders that created today and not checked out
     */
    public function markOrderAsCheckedout($orderId)
    {
        $sql = 'UPDATE shopping_cart SET checkedout=1 ';

        $where = $this->getWhere($orderId);

        $this->db->execute($sql.$where);

        return $this->db->affectedRows();
    }

    protected function getWhere($orderId)
    {
        $where = 'WHERE ';

        if ($orderId) {
            $where .= "order_id='$orderId' AND ";
        }

        $where .= 'checkedout=0 AND DATE(createdon)=DATE(NOW())';

        return $where;
    }
}
