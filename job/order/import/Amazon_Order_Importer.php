<?php

use Marketplace\Amazon\OrderReportFile;

class Amazon_Order_Importer extends Order_Importer
{
    public function run($argv = [])
    {
        try {
            $this->import();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function import()
    {
        // Amazon CA
        $channel = 'Amazon-ACA';
        $filename = Filenames::get('amazon.ca.order');
        $orders = $this->getOrders($filename, $channel);
        $this->importMasterOrders($orders);

        // Amazon US
        $channel = 'Amazon-US';
        $filename = Filenames::get('amazon.us.order');
        $orders = $this->getOrders($filename, $channel);
        $this->importMasterOrders($orders);
    }

    public function getOrders($filename, $channel)
    {
        $orderFile = new OrderReportFile($filename);

        $orders = [];

        while ($order = $orderFile->read()) {
            $orderId = $order['OrderId'];
            $orders[$orderId][] = $this->toStdOrder($order, $channel);
        }

        return $orders;
    }

    private function toStdOrder($order, $channel)
    {
        $express = $this->isExpress($order);

        return [
             'orderId'      => $order['OrderId'],
             'date'         => substr($order['Date'], 0, 10),
             'orderItemId'  => $order['OrderItemId'],
             'channel'      => $channel,
             'express'      => $express,
             'buyer'        => $order['Name'],
             'address'      => $order['Address1'].' '.$order['Address1'].' '.$order['Address1'],
             'city'         => $order['City'],
             'province'     => $order['StateOrRegion'],
             'country'      => $order['CountryCode'],
             'postalcode'   => $order['PostalCode'],
             'email'        => $order['BuyerEmail'],
             'phone'        => $order['Phone'],
             'sku'          => $order['SellerSKU'],
             'qty'          => $order['Quantity'],
             'price'        => $order['ItemPrice'],
             'shipping'     => $order['ShippingPrice'],
             'productName'  => $order['Title'],
        ];
    }

    private function isExpress($order)
    {
        // $order['ShipServiceLevel']
        return 0;
    }
}
