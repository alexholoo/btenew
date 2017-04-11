<?php

class Amazon_Order_Merger extends OrderMerger
{
    public function run($argv = [])
    {
        try {
            $this->merge();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function merge()
    {
        $this->mergeAmazonOrders('CA');
        $this->mergeAmazonOrders('US');
    }

    protected function mergeAmazonOrders($site)
    {
        if ($site == 'CA') {
            $channel = 'Amazon-ACA';
            $filename = Filenames::get('amazon.ca.order');
        }

        if ($site == 'US') {
            $channel = 'Amazon-US';
            $filename = Filenames::get('amazon.us.order');
        }

        $orderFile = new Marketplace\Amazon\OrderReportFile($filename, $site);

        while ($order = $orderFile->read()) {
            $express = strpos($order['ShipServiceLevel'], 'Exp') !== false ? 1 : 0;
            $address = trim($order['Address1'].' '.$order['Address2'].' '.$order['Address3']);

            $this->masterFile->write([
                $channel,
                substr($order['Date'], 0, 10),
                $order['OrderId'],
                $order['OrderItemId'],
                $order['OrderId'], // reference
                $express,
                $order['Name'],
                $address,
                $order['City'],
                $order['StateOrRegion'],
                $order['PostalCode'],
                $order['CountryCode'],
                $order['Phone'],
                $order['BuyerEmail'],
                $order['SellerSKU'],
                $order['ItemPrice'],
                $order['Quantity'],
                $order['ShippingPrice'],
            ]);
        }
    }
}
