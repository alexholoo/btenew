<?php

class Newegg_Order extends OrderImporter
{
    public function import()
    {
        $filename = Filenames::get('newegg.ca.master.order');

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

        $columns = fgetcsv($fp);

        while (($fields = fgetcsv($fp))) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__.' Error: '.$fields[0].' in file '.$filename);
                continue;
            }

            $order = array_combine($columns, $fields);

            $orderId = $order['OrderNumber'];
            $orders[$orderId][] = $this->toStdOrder($order);
        }

        fclose($fp);

        return $orders;
    }

    private function toStdOrder($order)
    {
        $express = $this->isExpress($order);

        return [
             'orderId'      => $order['OrderNumber'],
             'date'         => date('Y-m-d H:i:s', strtotime($order['OrderDateTime'])),
             'orderItemId'  => '',
             'channel'      => 'NeweggCA',
             'express'      => $express,
             'buyer'        => $order['ShipToFirstName'].' '.$order['ShipToLastName'],
             'address'      => $order['ShipToAddressLine1'].' '.$order['ShipToAddressLine2'],
             'city'         => $order['ShipToCity'],
             'province'     => $order['ShipToState'],
             'country'      => $order['ShipToCountry'],
             'postalcode'   => $order['ShipToZipCode'],
             'email'        => $order['OrderCustomerEmail'],
             'phone'        => $order['ShipToPhoneNumber'],
             'sku'          => $order['ItemSellerPartNo'],
             'qty'          => $order['QuantityOrdered'],
             'price'        => $order['ItemUnitPrice'], // $order['OrderTotal'],
             'shipping'     => $order['ItemUnitShippingCharge'], // $order['OrderShippingTotal'],
             'productName'  => '',
        ];
    }

    private function isExpress($order)
    {
        # $order['SalesChannel'],
        # $order['FulfillmentOption'],
        # $order['OrderShippingMethod'],
        # $order['ItemNeweggNo'],
        # $order['ExtendUnitPrice'],
        # $order['ExtendShippingCharge'],
        # $order['ShipToCompany'],

        return 0;
    }
}
