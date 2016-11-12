<?php

namespace Service;

use App\Models\Orders;
use Phalcon\Di\Injectable;

class OrderService extends Injectable
{
    public function isAmazonOrder($orderId)
    {
        return (bool)preg_match('/^\d{3}-\d{7}-\d{7}$/', $orderId);
    }

    public function getOrderDetail($orderId)
    {
        $order = Orders::findFirst("orderId='$orderId'");
        if ($order) {
            return $order->toArray();
           #return array_map("utf8_encode", $order->toArray());
        }
        return false;
    }

    public function isOrderPurchased($orderId)
    {
        $sql = "SELECT orderid FROM purchase_order_log WHERE orderid='$orderId'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    public function isOrderCanceled($order)
    {
        $channel = $order['channel'];
        $orderId = $order['orderId'];

        if (substr($channel, 0, 6) != 'Amazon') {
            return false; // not cancelled
        }

        return $this->amazonService->isOrderCanceled($order);
    }

    protected function markOrderPurchased($orderId, $sku)
    {
        // mark the order as 'purchased' to prevent purchase twice
        // $sql = "UPDATE ca_order_notes SET status='purchased', actual_sku=? WHERE order_id=?";
        // $result = $this->db->query($sql, array($sku, $orderId));
        // return $result;
    }

    protected function isOrderPending($orderId)
    {
        // $sql = "SELECT status FROM ca_order_notes WHERE order_id=? LIMIT 1";
        // $result = $this->db->query($sql, array($orderId))->fetch();
        // return $result['status'] == 'pending';
    }
}
