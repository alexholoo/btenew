<?php

class Newegg_Order_Merger extends OrderMerger
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
        $this->mergeNeweggOrders('CA');
        $this->mergeNeweggOrders('US');
    }

    protected function mergeNeweggOrders($site)
    {
        if ($site == 'CA') {
            $channel = 'NeweggCA';
            $filename = Filenames::get('newegg.ca.master.order');
        }

        if ($site == 'US') {
            $channel = 'NeweggUSA';
            $filename = Filenames::get('newegg.us.master.order');
        }

        $orderFile = new Marketplace\Newegg\StdOrderListFile($filename, $site);

        while ($order = $orderFile->read()) {
            $address = $order['Ship To Address Line 1'].' '.$order['Ship To Address Line 2'];
            $express = preg_match('/Standard|Economy/', $order['Order Shipping Method']) ? 0 : 1;
            $buyer = $order['Ship To First Name'].' '.$order['Ship To LastName'];

            $this->masterFile->write([
                $channel,
                date('Y-m-d', strtotime($order['Order Date & Time'])),
                $order['Order Number'],
                $order['Item Newegg #'],
                $order['Order Number'], // reference
                $express,
                $buyer,
                $address,
                $order['Ship To City'],
                $order['Ship To State'],
                $order['Ship To ZipCode'],
                $order['Ship To Country'],
                $order['Ship To Phone Number'],
                $order['Order Customer Email'],
                $order['Item Seller Part #'],
                $order['Item Unit Price'],
                $order['Quantity Ordered'],
                $order['Item Unit Shipping Charge'],
            ]);
        }
    }
}
