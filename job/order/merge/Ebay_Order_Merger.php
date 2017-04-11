<?php

class Ebay_Order_Merger extends OrderMerger
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
        $this->mergeEbayOrders('GFS');
        $this->mergeEbayOrders('ODO');
    }

    protected function mergeEbayOrders($site)
    {
        if ($site == 'GFS') {
            $channel = 'eBay-GFS';
            $filename = Filenames::get('ebay.gfs.order');
        }

        if ($site == 'ODO') {
            $channel = 'eBay-ODO';
            $filename = Filenames::get('ebay.odo.order');
        }

        $orderFile = new Marketplace\eBay\OrderReportFile($filename);

        while ($order = $orderFile->read()) {
            $express = ($order['ShippingService'] == 'ShippingMethodExpress') ? 1 : 0;
            $this->masterFile->write([
                $channel,
                $order['DatePaid'],
                $order['OrderID'],
                $order['ItemID'],
                $order['RecordNumber'], // reference
                $express,
                $order['Name'],
                $order['Address'].' '.$order['Address2'],
                $order['City'],
                $order['Province'],
                $order['PostalCode'],
                $order['Country'],
                $order['Phone'],
                $order['Email'],
                $order['SKU'],
                $order['TransactionPrice'],
                $order['QuantityPurchased'],
                $order['ShippingServiceCost'],
            ]);
        }
    }
}
