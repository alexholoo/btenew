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

    // isOrderDropshipped
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

    // markOrderDropshipped
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

    // master_order/item/shipping_address

    public function getOrder($orderId)
    {
        $sql = "SELECT * FROM master_order WHERE order_id='$orderId'";
        $order = $this->db->fetchOne($sql);

        if ($order) {
            unset($order['id']);
        }

        return $order;
    }

    public function getOrderItems($orderId)
    {
        $sql = "SELECT * FROM master_order_item WHERE order_id=?";
        $result = $this->db->query($sql, [$orderId]);

        $items = [];
        while ($item = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $items[] = [
               #'orderId' => $item['order_id'],
                'sku'     => $item['sku'],
                'price'   => $item['price'],
                'qty'     => $item['qty'],
                'product' => $item['product_name'],
            ];
        }

        return $items;
    }

    public function getShippingAddress($orderId)
    {
        $sql = "SELECT * FROM master_order_shipping_address WHERE order_id='$orderId'";
        $shippingAddress = $this->db->fetchOne($sql);

        if ($shippingAddress) {
            unset($shippingAddress['id']);
            unset($shippingAddress['date']);
            unset($shippingAddress['order_id']);
        }

        return $shippingAddress;
    }

    // search orders by partial order id
    public function searchOrders($orderKey)
    {
        $sql = "SELECT * FROM master_order WHERE order_id LIKE '%$orderKey'";
        $orders = $this->db->fetchAll($sql);

        if (!$orders) {
            return false;
        }

        foreach ($orders as $key => $order) {
            $orderId = $order['order_id'];
            $orders[$key]['items'] = $this->getOrderItems($orderId);
            $orders[$key]['address'] = $this->getShippingAddress($orderId);
        }

        return $orders;
    }

    public function isShipped($orderId)
    {
        return $this->shipmentService->isOrderShipped($orderId);
    }

    public function markAsShipped($orderId)
    {
        $this->shipmentService->markOrderAsShipped($orderId);
    }
}
