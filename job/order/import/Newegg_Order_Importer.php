<?php

use Marketplace\Newegg\StdOrderListFile;

class Newegg_Order_Importer extends Order_Importer
{
    public function import()
    {
        // CA
        $this->channel = 'NeweggCA';
        $filename = Filenames::get('newegg.ca.master.order');
        if (file_exists($filename)) {
            $orders = $this->getOrders($filename, 'CA');
            $this->importMasterOrders($orders);
        }

        // US
        $this->channel = 'NeweggUSA';
        $filename = Filenames::get('newegg.us.master.order');
        if (file_exists($filename)) {
            $orders = $this->getOrders($filename, 'US');
            $this->importMasterOrders($orders);
        }
    }

    private function getOrders($filename, $site)
    {
        $orders = [];

        $orderFile = new StdOrderListFile($filename, $site);

        while (($order = $orderFile->read())) {
            $orderId = $order['Order Number'];
            $orders[$orderId][] = $this->toStdOrder($order);
        }

        return $orders;
    }

    private function toStdOrder($order)
    {
        $express = $this->isExpress($order);

        return [
             'orderId'      => $order['Order Number'],
             'date'         => date('Y-m-d H:i:s', strtotime($order['Order Date & Time'])),
             'orderItemId'  => '',
             'channel'      => $this->channel,
             'express'      => $express,
             'buyer'        => $order['Ship To First Name'].' '.$order['Ship To LastName'],
             'address'      => $order['Ship To Address Line 1'].' '.$order['Ship To Address Line 2'],
             'city'         => $order['Ship To City'],
             'province'     => $order['Ship To State'],
             'country'      => $order['Ship To Country'],
             'postalcode'   => $order['Ship To ZipCode'],
             'email'        => $order['Order Customer Email'],
             'phone'        => $order['Ship To Phone Number'],
             'sku'          => $order['Item Seller Part #'],
             'qty'          => $order['Quantity Ordered'],
             'price'        => $order['Item Unit Price'], // $order['OrderTotal'],
             'shipping'     => $order['Item Unit Shipping Charge'], // $order['OrderShippingTotal'],
             'productName'  => '',
        ];
    }

    private function isExpress($order)
    {
        # Expedited Shipping (3-5 business days)
        # One-Day Shipping(Next day)
        # Standard Shipping (5-7 business days)
        # Two-Day Shipping(2 business days)

        $shippingMethod = $order['Order Shipping Method'];

        if (strpos($shippingMethod, 'Standard Shipping') !== false) {
            return 0;
        }

        return 1;
    }
}
