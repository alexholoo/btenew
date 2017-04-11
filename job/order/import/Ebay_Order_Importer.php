<?php

use Marketplace\eBay\OrderReportFile;

class Ebay_Order_Importer extends Order_Importer
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
        // BTE - CAD
        $filename = Filenames::get('ebay.gfs.order');
        $orders = $this->getOrders($filename);
        $this->importMasterOrders($orders);

        // ODO - USD
        $filename = Filenames::get('ebay.odo.order');
        $orders = $this->getOrders($filename);
        $this->importMasterOrders($orders);
    }

    public function getOrders($filename)
    {
        $orderFile = new OrderReportFile($filename);

        $orders = [];

        while ($order = $orderFile->read()) {
            $orderId = $order['OrderID'];
            $orders[$orderId][] = $this->toStdOrder($order);
        }

        return $orders;
    }

    private function toStdOrder($order)
    {
        $express = $this->isExpress($order);

        return [
             'orderId'      => $order['OrderID'],
             'date'         => $order['DatePaid'],
             'orderItemId'  => $order['ItemID'],
             'channel'      => 'eBay',
             'express'      => $express,
             'buyer'        => $order['Name'],
             'address'      => $order['Address'].' '.$order['Address2'],
             'city'         => $order['City'],
             'province'     => $order['Province'],
             'country'      => $order['Country'],
             'postalcode'   => $order['PostalCode'],
             'email'        => $order['Email'],
             'phone'        => $order['Phone'],
             'sku'          => $order['SKU'],
             'qty'          => $order['QuantityPurchased'],
             'price'        => $order['TransactionPrice'],
             'shipping'     => $order['ShippingServiceCost'],
             'productName'  => $order['ProductName'],
        ];
    }

    private function isExpress($order)
    {
        return $order['ShippingService'] == 'ShippingMethodExpress';
    }
}
