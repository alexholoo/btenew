<?php

class Bestbuy_Order extends OrderImporter
{
    public function import()
    {
        $orders = $this->getBestbuyOrders();
        $this->importMasterOrders($orders);
    }

    protected function getBestbuyOrders()
    {
        $client = new Marketplace\Bestbuy\Client();

        // only today's orders
        $start = date('Y-m-d\T00:00:00');
        $end   = date('Y-m-d\T23:59:59');

        $orderList = $client->listOrders($start, $end);

        // reindex the array to fix multi-items-order issue
        foreach ($orderList as $orderId => $orders) {
            foreach ($orders as $key => $order) {
                if ($order['state'] != 'RECEIVED') {
                    // order_state:
                    // - WAITING_ACCEPTANCE
                    // - WAITING_DEBIT_PAYMENT
                    // - CANCELED
                    // - RECEIVED
                    unset($orders[$key]);
                    continue;
                }
                $orderList[$orderId][$key] = $this->toStdOrder($order);
            }
        }

        return $orderList;
    }

    private function toStdOrder($order)
    {
        return [
             'orderId'      => $order['orderId'],
             'date'         => $order['date'],
             'orderItemId'  => $order[''],
             'channel'      => 'Bestbuy',
             'express'      => $order['express'],
             'buyer'        => $order[''],
             'address'      => $order[''],
             'city'         => $order[''],
             'province'     => $order[''],
             'country'      => $order[''],
             'postalcode'   => $order[''],
             'email'        => $order[''],
             'phone'        => $order[''],
             'sku'          => $order['sku'],
             'qty'          => $order['qty'],
             'price'        => $order[''],
             'shipping'     => $order[''],
             'productName'  => $order[''],
        ];
    }
}
