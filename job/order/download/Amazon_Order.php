<?php

use Marketplace\Amazon\OrderReportFile;

class Amazon_Order extends OrderDownloader
{
    public function download()
    {
        // Amazon CA
        $client = new Marketplace\Amazon\Client('bte-amazon-ca');
        $filename = Filenames::get('amazon.ca.order');
        $orderFile = new OrderReportFile($filename);

        $orders = $client->getOrderList();
        foreach ($orders as $order) {
            $this->saveOrder($order, $orderFile);
        }

        // Amazon US
        $client = new Marketplace\Amazon\Client('bte-amazon-us');
        $filename = Filenames::get('amazon.us.order');
        $orderFile = new OrderReportFile($filename);

        $orders = $client->getOrderList();
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

       #if (!isset($data['OrderTotal'])) {
       #    $data['OrderTotal']['Amount'] = '0.00';
       #    $data['OrderTotal']['CurrencyCode'] = 'CAD';
       #}
       #if (!isset($data['PaymentMethod'])) {
       #    $data['PaymentMethod'] = '';
       #}
       #if (!isset($data['BuyerName'])) {
       #    $data['BuyerName'] = '';
       #}
        if (!isset($data['BuyerEmail'])) {
            $data['BuyerEmail'] = '';
        }
       #if (!isset($data['ShippedByAmazonTFM'])) {
       #    $data['ShippedByAmazonTFM'] = 'false';
       #}
       #if (!isset($data['EarliestDeliveryDate'])) {
       #    $data['EarliestDeliveryDate'] = null;
       #}
       #if (!isset($data['LatestDeliveryDate'])) {
       #    $data['LatestDeliveryDate'] = null;
       #}

        $address = $order->getShippingAddress();

        $items = $order->fetchItems();

        foreach ($items->getItems() as $item) {
           #if (!isset($item['ConditionNote'])) {
           #    $item['ConditionNote'] = '';
           #}

            $orderFile->write([
                $data['AmazonOrderId'],
                $data['PurchaseDate'],
               #$data['LastUpdateDate'],
                $data['OrderStatus'],
                $data['FulfillmentChannel'],
                $data['SalesChannel'],
                $data['ShipServiceLevel'],
               #$data['OrderTotal']['CurrencyCode'],
               #$data['OrderTotal']['Amount'],
               #$data['NumberOfItemsShipped'],
               #$data['NumberOfItemsUnshipped'],
               #$data['PaymentMethod'],
               #$data['BuyerName'],
                $data['BuyerEmail'],
               #$data['ShipmentServiceLevelCategory'],
               #$data['ShippedByAmazonTFM'],
               #$data['OrderType'],
               #$data['EarliestShipDate'],
               #$data['LatestShipDate'],
               #$data['EarliestDeliveryDate'],
               #$data['LatestDeliveryDate'],
               #$data['IsBusinessOrder'],
               #$data['IsPrime'],
               #$data['IsPremiumOrder'],
                // item
                $item['ASIN'],
                $item['SellerSKU'],
                $item['OrderItemId'],
                $item['Title'],
                $item['QuantityOrdered'],
               #$item['QuantityShipped'],
                $item['ItemPrice']['CurrencyCode'],
                $item['ItemPrice']['Amount'],
                $item['ShippingPrice']['Amount'],
               #$item['GiftWrapPrice']['Amount'],
               #$item['ItemTax']['Amount'],
               #$item['ShippingTax']['Amount'],
               #$item['GiftWrapTax']['Amount'],
               #$item['ShippingDiscount']['Amount'],
               #$item['PromotionDiscount']['Amount'],
               #$item['ConditionId'],
               #$item['ConditionSubtypeId'],
               #$item['ConditionNote'],
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
