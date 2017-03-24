<?php

use Marketplace\Amazon\OrderReportFile;

class Amazon_Order_Downloader extends Order_Downloader
{
    public function download()
    {
        // Amazon CA
        $client = new Marketplace\Amazon\Client('bte-amazon-ca');
        $filename = Filenames::get('amazon.ca.order');
        $orderFile = new OrderReportFile($filename);

        $orders = $client->getOrderList(); // TODO: start time
        foreach ($orders as $order) {
            $this->saveOrder($order, $orderFile);
        }

        // Amazon US
        $client = new Marketplace\Amazon\Client('bte-amazon-us');
        $filename = Filenames::get('amazon.us.order');
        $orderFile = new OrderReportFile($filename);

        $orders = $client->getOrderList(); // TODO: start time
        foreach ($orders as $order) {
            $this->saveOrder($order, $orderFile);
        }
    }

    public function saveOrder($order, $orderFile)
    {
        $data = $order->getData();

        if ($data['OrderStatus'] == 'Canceled') {
            // TODO: delete order if exists?
            return;
        }

        if (!isset($data['BuyerEmail'])) {
            $data['BuyerEmail'] = '';
        }

        $address = $order->getShippingAddress();

        $items = $order->fetchItems();

        foreach ($items->getItems() as $item) {
            $orderFile->write([
                $data['AmazonOrderId'],
                $data['PurchaseDate'],
                $data['OrderStatus'],
                $data['FulfillmentChannel'],
                $data['SalesChannel'],
                $data['ShipServiceLevel'],
                $data['BuyerEmail'],
                // item
                $item['ASIN'],
                $item['SellerSKU'],
                $item['OrderItemId'],
                $item['Title'],
                $item['QuantityOrdered'],
                $item['ItemPrice']['CurrencyCode'],
                $item['ItemPrice']['Amount'],
                $item['ShippingPrice']['Amount'],
                // address
                $address['Name'],
                $address['AddressLine1'],
                $address['AddressLine2'],
                $address['AddressLine3'],
                $address['City'],
                $address['County'],
                $address['District'],
                $address['StateOrRegion'],
                $address['PostalCode'],
                $address['CountryCode'],
                $address['Phone'],
            ]);
        }
    }
}
