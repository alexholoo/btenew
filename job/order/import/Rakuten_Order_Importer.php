<?php

class Rakuten_Order_Importer extends Order_Importer
{
    public function import()
    {
        $filename = Filenames::get('rakuten.us.master.order');

        $orders = $this->getOrders($filename);
        $this->importMasterOrders($orders);
    }

    private function getOrders($filename)
    {
        $orders = [];

        if (!file_exists($filename)) {
            $this->log("Failed to open file: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        $columns = fgetcsv($fp, 0, "\t");

        while (($fields = fgetcsv($fp, 0, "\t"))) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__.' Error: '.$fields[0].' in file '.$filename);
                continue;
            }

            $order = array_combine($columns, $fields);

            $orderId = $order['Receipt_ID'];
            $orders[$orderId][] = $this->toStdOrder($order);
        }

        fclose($fp);

        return $orders;
    }

    private function toStdOrder($order)
    {
        $express = $this->isExpress($order);

        return [
             'orderId'      => $order['Receipt_ID'],
             'date'         => date('Y-m-d H:i:s', strtotime($order['Date_Entered'])),
             'orderItemId'  => $order['Receipt_Item_ID'],
             'channel'      => 'Rakuten-BUY',
             'express'      => $express,
             'buyer'        => $order['Ship_To_Name'],
             'address'      => $order['Ship_To_Street1'].' '.$order['Ship_To_Street2'],
             'city'         => $order['Ship_To_City'],
             'province'     => $order['Ship_To_State'],
             'country'      => 'US', // TODO ??
             'postalcode'   => $order['Ship_To_Zip'],
             'email'        => $order['Email'],
             'phone'        => $order['Bill_To_Phone'],
             'sku'          => $order['ReferenceId'],
             'qty'          => $order['Quantity'],
             'price'        => $order['Price'],
             'shipping'     => $order['ShippingFee'],
             'productName'  => $order['Title'],
        ];
    }

    private function isExpress($order)
    {
        # $order['ShippingMethodId'],
        return 0;
    }
}
