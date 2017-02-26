<?php

class Amazon_Order extends OrderImporter
{
    public function import()
    {
        // Amazon CA
        $channel = 'Amazon-ACA';
        $client = new Marketplace\Amazon\Client('bte-amazon-ca');
        $orders = $client->getOrderList();
        $orders = $this->reindexOrders($orders, $channel);
        $this->importMasterOrders($orders);

        // Amazon US
        $channel = 'Amazon-US';
        $client = new Marketplace\Amazon\Client('bte-amazon-us');
        $orders = $client->getOrderList();
        $orders = $this->reindexOrders($orders, $channel);
        $this->importMasterOrders($orders);
    }

    public function reindexOrders($orders, $channel)
    {
        $data = $order->getData();

        if ($this->orderExists($data['AmazonOrderId'])) { return; }
        if ($data['OrderStatus'] == 'Canceled') { return; }

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

        $this->db->insertAsDict('amazon_order', [
            $channel,
            $data['AmazonOrderId'],
            $this->dtime($data['PurchaseDate']),
            $this->dtime($data['LastUpdateDate']),
            $data['OrderStatus'],
            $data['FulfillmentChannel'],
            $data['SalesChannel'],
            $data['ShipServiceLevel'],
            $data['OrderTotal']['CurrencyCode'],
            $data['OrderTotal']['Amount'],
            $data['NumberOfItemsShipped'],
            $data['NumberOfItemsUnshipped'],
            $data['PaymentMethod'],
            $data['BuyerName'],
            $data['BuyerEmail'],
            $data['ShipmentServiceLevelCategory'],
            $this->yesNo($data['ShippedByAmazonTFM']),
            $data['OrderType'],
            $this->dtime($data['EarliestShipDate']),
            $this->dtime($data['LatestShipDate']),
            $this->dtime($data['EarliestDeliveryDate']),
            $this->dtime($data['LatestDeliveryDate']),
            $this->yesNo($data['IsBusinessOrder']),
            $this->yesNo($data['IsPrime']),
            $this->yesNo($data['IsPremiumOrder']),
        ]);

        $items = $order->fetchItems();

        foreach ($items->getItems() as $item) {
            if (!isset($item['ConditionNote'])) {
                $item['ConditionNote'] = '';
            }

            $this->db->insertAsDict('amazon_order_item', [
                $order->getAmazonOrderId(),
                $item['ASIN'],
                $item['SellerSKU'],
                $item['OrderItemId'],
                $item['Title'],
                $item['QuantityOrdered'],
                $item['QuantityShipped'],
                $item['ItemPrice']['CurrencyCode'],
                $item['ItemPrice']['Amount'],
                $item['ShippingPrice']['Amount'],
                $item['GiftWrapPrice']['Amount'],
                $item['ItemTax']['Amount'],
                $item['ShippingTax']['Amount'],
                $item['GiftWrapTax']['Amount'],
                $item['ShippingDiscount']['Amount'],
                $item['PromotionDiscount']['Amount'],
                $item['ConditionId'],
                $item['ConditionSubtypeId'],
                $item['ConditionNote'],
            ]);
        }

        $address = $order->getShippingAddress();
        $this->db->insertAsDict('amazon_order_shipping_address', [
            $order->getAmazonOrderId(),
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
