<?php

class Rakuten_Order_Merger extends OrderMerger
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
        $channel = 'Rakuten-BUY';
        $filename = Filenames::get('rakuten.us.master.order');

        $orderFile = new Marketplace\Rakuten\StdOrderListFile($filename, 'US');

        while ($order = $orderFile->read()) {
            $address = $order['Ship_To_Street1'].' '.$order['Ship_To_Street2'];
            $express = $order['ShippingMethodId'];
            $this->masterFile->write([
                $channel,
                date('Y-m-d', strtotime($order['Date_Entered'])),
                $order['Receipt_ID'],
                $order['Receipt_Item_ID'],
                $order['Receipt_ID'], // reference
                $express,
                $order['Ship_To_Name'],
                $address,
                $order['Ship_To_City'],
                $order['Ship_To_State'],
                $order['Ship_To_Zip'],
                'US',     // 'country',
                $order['Bill_To_Phone'],
                $order['Email'],
                $order['ReferenceId'], // sku
                $order['Price'],
                $order['Quantity'],
                $order['ShippingFee'],
            ]);
        }
    }
}
