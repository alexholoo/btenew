<?php

namespace Service;

use Phalcon\Di\Injectable;

class OrderService extends Injectable
{
    public function isAmazonOrder($orderId)
    {
        return (bool)preg_match('/^\d{3}-\d{7}-\d{7}$/', $orderId);
    }
}
