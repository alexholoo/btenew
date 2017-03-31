<?php

use Marketplace\Bestbuy\OrderReportFile;

class Bestbuy_Order_Importer extends Order_Importer
{
    public function import()
    {
        $orders = $this->getBestbuyOrders();
        $this->importMasterOrders($orders);
    }

    protected function getBestbuyOrders()
    {
        $filename = Filenames::get('bestbuy.order');
        $orderFile = new OrderReportFile($filename);

        $orders = [];

        while ($order = $orderFile->read()) {
            if ($order['status'] == 'CANCELED') {
                continue;
            }
            $orderId = $order['orderId'];
            $orders[$orderId][] = $this->toStdOrder($order);
        }

        return $orders;
    }

    private function toStdOrder($order)
    {
        return [
             'orderId'      => $order['orderId'],
             'date'         => $order['date'],
             'orderItemId'  => $order['orderItemId'],
             'channel'      => 'Bestbuy',
             'express'      => $order['express'],
             'buyer'        => $order['buyer'],
             'address'      => $order['address'],
             'city'         => $order['city'],
             'province'     => $order['state'],
             'country'      => $order['country'],
             'postalcode'   => $order['zipcode'],
             'email'        => '',
             'phone'        => $order['phone'],
             'sku'          => $order['sku'],
             'qty'          => $order['qty'],
             'price'        => $order['price'],
             'shipping'     => $order['shipping'],
             'productName'  => $order['product'],
        ];
    }
}
