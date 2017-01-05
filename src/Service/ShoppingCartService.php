<?php

namespace Service;

use Phalcon\Di\Injectable;

class ShoppingCartService extends Injectable
{
    /**
     * Get orders in shopping cart (that created today, not checked out yet)
     */
    public function getPendingOrders()
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

        return $orders;
    }

    /**
     * Get a list of order ids, this is useful to detect if an
     * order is already in shopping cart
     */
    public function getOrders($date)
    {
        $sql = "SELECT order_id
                  FROM shopping_cart
                 WHERE DATE(createdon)>='$date'";

        $orders = $this->db->fetchAll($sql);

        return array_column($orders, 'order_id');
    }

    /**
     * Add an order to shopping cart
     *
     * @return 1 for order in shopping cart, 0 for not
     */
    public function addOrder($order)
    {
        $orderId = $order['orderId'];

        $inCart = $this->findOrder($orderId);

        if ($inCart) {
            $affectRows = $this->shoppingCartService->removeOrder($orderId);
            return 1-$affectRows; // order may or may not in shopping cart
        }

        // The order is not in shopping cart, add it to shopping cart
        $this->db->insertAsDict('shopping_cart', [
            'order_id' => $order['orderId'],
            'sku'      => $order['sku'],
            'qty'      => $order['qty']
        ]);

        return 1; // order is in shopping cart
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
     * It cannot delete orders not today or checked out
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
