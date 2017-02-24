<?php

class Amazon_Order extends OrderDownloader
{
    public function download()
    {
        return;

        // Amazon CA
        $client = new Marketplace\Amazon\Client('bte-amazon-ca');
        $channel = 'Amazon-ACA';

        $orders = $client->getOrderList();
        foreach ($orders as $order) {
            $this->saveOrder($order, $channel);
        }

        // Amazon US
        $client = new Marketplace\Amazon\Client('bte-amazon-us');
        $channel = 'Amazon-US';

        $orders = $client->getOrderList();
        foreach ($orders as $order) {
            $this->saveOrder($order, $channel);
        }
    }

    public function saveOrder($order, $channel)
    {
        $data = $order->getData();

        if ($this->orderExists($data['AmazonOrderId'])) {
            return;
        }

        if ($data['OrderStatus'] == 'Canceled') {
            // TODO: delete order if exists?
            return;
        }

        if (!isset($data['OrderTotal'])) {
            $data['OrderTotal']['Amount'] = '0.00';
            $data['OrderTotal']['CurrencyCode'] = 'CAD';
        }
        if (!isset($data['PaymentMethod'])) {
            $data['PaymentMethod'] = '';
        }
        if (!isset($data['BuyerName'])) {
            $data['BuyerName'] = '';
        }
        if (!isset($data['BuyerEmail'])) {
            $data['BuyerEmail'] = '';
        }
        if (!isset($data['ShippedByAmazonTFM'])) {
            $data['ShippedByAmazonTFM'] = 'false';
        }
        if (!isset($data['EarliestDeliveryDate'])) {
            $data['EarliestDeliveryDate'] = null;
        }
        if (!isset($data['LatestDeliveryDate'])) {
            $data['LatestDeliveryDate'] = null;
        }

        try {
            $this->db->insertAsDict('amazon_order', [
                'Channel'                      => $channel,
                'OrderId'                      => $data['AmazonOrderId'],
                'PurchaseDate'                 => $this->dtime($data['PurchaseDate']),
                'LastUpdateDate'               => $this->dtime($data['LastUpdateDate']),
                'OrderStatus'                  => $data['OrderStatus'],
                'FulfillmentChannel'           => $data['FulfillmentChannel'],
                'SalesChannel'                 => $data['SalesChannel'],
                'ShipServiceLevel'             => $data['ShipServiceLevel'],
                'CurrencyCode'                 => $data['OrderTotal']['CurrencyCode'],
                'OrderTotalAmount'             => $data['OrderTotal']['Amount'],
                'NumberOfItemsShipped'         => $data['NumberOfItemsShipped'],
                'NumberOfItemsUnshipped'       => $data['NumberOfItemsUnshipped'],
                'PaymentMethod'                => $data['PaymentMethod'],
                'BuyerName'                    => $data['BuyerName'],
                'BuyerEmail'                   => $data['BuyerEmail'],
                'ShipmentServiceLevelCategory' => $data['ShipmentServiceLevelCategory'],
                'ShippedByAmazonTFM'           => $this->yesNo($data['ShippedByAmazonTFM']),
                'OrderType'                    => $data['OrderType'],
                'EarliestShipDate'             => $this->dtime($data['EarliestShipDate']),
                'LatestShipDate'               => $this->dtime($data['LatestShipDate']),
                'EarliestDeliveryDate'         => $this->dtime($data['EarliestDeliveryDate']),
                'LatestDeliveryDate'           => $this->dtime($data['LatestDeliveryDate']),
                'IsBusinessOrder'              => $this->yesNo($data['IsBusinessOrder']),
                'IsPrime'                      => $this->yesNo($data['IsPrime']),
                'IsPremiumOrder'               => $this->yesNo($data['IsPremiumOrder']),
            ]);

            echo $data['AmazonOrderId'], ' ', $channel, EOL;

            $this->saveOrderItem($order);
            $this->saveShippingAddress($order);

        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    private function saveOrderItem($order)
    {
        $items = $order->fetchItems();

        foreach ($items->getItems() as $item) {
            if (!isset($item['ConditionNote'])) {
                $item['ConditionNote'] = '';
            }

            try {
                $this->db->insertAsDict('amazon_order_item', [
                    'OrderId'            => $order->getAmazonOrderId(),
                    'ASIN'               => $item['ASIN'],
                    'SellerSKU'          => $item['SellerSKU'],
                    'OrderItemId'        => $item['OrderItemId'],
                    'Title'              => $item['Title'],
                    'QuantityOrdered'    => $item['QuantityOrdered'],
                    'QuantityShipped'    => $item['QuantityShipped'],
                    'CurrencyCode'       => $item['ItemPrice']['CurrencyCode'],
                    'ItemPrice'          => $item['ItemPrice']['Amount'],
                    'ShippingPrice'      => $item['ShippingPrice']['Amount'],
                    'GiftWrapPrice'      => $item['GiftWrapPrice']['Amount'],
                    'ItemTax'            => $item['ItemTax']['Amount'],
                    'ShippingTax'        => $item['ShippingTax']['Amount'],
                    'GiftWrapTax'        => $item['GiftWrapTax']['Amount'],
                    'ShippingDiscount'   => $item['ShippingDiscount']['Amount'],
                    'PromotionDiscount'  => $item['PromotionDiscount']['Amount'],
                    'ConditionId'        => $item['ConditionId'],
                    'ConditionSubtypeId' => $item['ConditionSubtypeId'],
                    'ConditionNote'      => $item['ConditionNote'],
                ]);
            } catch (\Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }
    }

    private function saveShippingAddress($order)
    {
        $address = $order->getShippingAddress();

        try {
            $this->db->insertAsDict('amazon_order_shipping_address', [
                'OrderId'       => $order->getAmazonOrderId(),
                'Name'          => $address['Name'],
                'AddressLine1'  => $address['AddressLine1'],
                'AddressLine2'  => $address['AddressLine2'],
                'AddressLine3'  => $address['AddressLine3'],
                'City'          => $address['City'],
                'County'        => $address['County'],
                'District'      => $address['District'],
                'StateOrRegion' => $address['StateOrRegion'],
                'PostalCode'    => $address['PostalCode'],
                'CountryCode'   => $address['CountryCode'],
                'Phone'         => $address['Phone'],
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    private function orderExists($orderId)
    {
        $sql = "SELECT OrderId FROM amazon_order WHERE OrderId='$orderId'";
        return $this->db->fetchOne($sql);
    }

    private function yesNo($value)
    {
        if ($value == 'false') return 'N';
        return 'Y';
    }

    private function dtime($value)
    {
        # "2016-09-22T06:59:59Z" => "2016-09-22 06:59:59"
        return str_replace(['T', 'Z'], [' ', ''], $value);
    }
}
