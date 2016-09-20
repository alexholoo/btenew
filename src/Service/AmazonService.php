<?php

namespace Service;

use Phalcon\Di\Injectable;

class AmazonService extends Injectable
{
    public function isAmazonOrder($order)
    {
        return substr($order['channel'], 0, 6) == 'Amazon';
        #return (boolean)preg_match('/^\d{3}-\d{7}-\d{7}$/', $orderId);
    }

    public function isOrderCanceled($order)
    {
        $store = 'bte-amazon-ca';

        if ($order['channel'] == 'Amazon-US') {
            $store = 'bte-amazon-us';
        }

        $orderId = $order['orderId'];
        //$orderId = '701-8728845-2735459'; // Canceled

        $order = new \AmazonOrder($store);
        $order->setOrderId($orderId);
        $order->fetchOrder();

        return $order->getOrderStatus() == 'Canceled';
    }

    public function doSomething()
    {
        fpr(__FILE__."\n".__METHOD__);

        $ebayService = $this->di->get('ebayService');
        $ebayService->doSomething();
    }
}
