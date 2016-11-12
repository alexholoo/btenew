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
}
