<?php

class Ebay_Order extends OrderImporter
{
    public function import()
    {
        // BTE - CAD
        $client = new Marketplace\eBay\Client('bte');

        $res = $client->getOrders();
        //print_r($res);
        if ($res->Ack == 'Success') {
            $orders = $res->OrderArray->Order;
            foreach ($orders as $order) {
                echo $order->OrderID, PHP_EOL;
                $this->saveOrder($order);
            }
        }

        // ODO - USD
        $client = new Marketplace\eBay\Client('odo');

        $res = $client->getOrders();
        //print_r($res);
        if ($res->Ack == 'Success') {
            $orders = $res->OrderArray->Order;
            foreach ($orders as $order) {
                echo $order->OrderID, PHP_EOL;
                $this->saveOrder($order);
            }
        }
    }

    public function saveOrder($order)
    {
        if ($order->OrderStatus == "Cancelled") { return; }

        $OrderID             = (string)$order->OrderID;
        $ExtOrderID          = (string)$order->ExtendedOrderID;
        $Status              = (string)$order->OrderStatus;
        $BuyerUsername       = (string)$order->BuyerUserID;
        $DatePaid            = substr($order->PaidTime, 0, 10);
        $Currency            = (string)$order->AmountPaid['currencyID'];
        $AmountPaid          = (string)$order->AmountPaid;
        $SalesTaxAmount      = (string)$order->ShippingDetails->SalesTax->SalesTaxAmount;
        $ShippingService     = (string)$order->ShippingServiceSelected->ShippingService;
        $ShippingServiceCost = (string)$order->ShippingServiceSelected->ShippingCost;

        $transactions = $order->TransactionArray;

        foreach ($transactions->Transaction as $transaction) {
            $SKU               = (string)$transaction->Item->SKU;
            $QuantityPurchased = (string)$transaction->QuantityPurchased;
            $TransactionID     = (string)$transaction->TransactionID;
            $TransactionPrice  = (string)$transaction->TransactionPrice;
            $Tracking          = (string)$transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;
            $ItemID            = (string)$transaction->Item->ItemID;
            $Email             = (string)$transaction->Buyer->Email;
            $RecordNumber      = (string)$transaction->ShippingDetails->SellingManagerSalesRecordNumber;

            if (!$Tracking) { $Tracking = 'NA'; }
        }

        $shippingAddress = $order->ShippingAddress;
        $Name            = (string)$shippingAddress->Name;
        $Address         = (string)$shippingAddress->Street1;
        $Address2        = (string)$shippingAddress->Street2;
        $City            = (string)$shippingAddress->CityName;
        $Province        = (string)$shippingAddress->StateOrProvince;
        $PostalCode      = (string)$shippingAddress->PostalCode;
        $Country         = (string)$shippingAddress->Country;
        $Phone           = (string)$shippingAddress->Phone;
    }

    private function toStdOrder($order)
    {
        $express = $this->isExpress($order);

        return [
             'orderId'      => $order[''],
             'date'         => $order[''],
             'orderItemId'  => $order[''],
             'channel'      => 'eBay',
             'express'      => $express,
             'buyer'        => $order[''],
             'address'      => $order[''],
             'city'         => $order[''],
             'province'     => $order[''],
             'country'      => $order[''],
             'postalcode'   => $order[''],
             'email'        => $order[''],
             'phone'        => $order[''],
             'sku'          => $order[''],
             'qty'          => $order[''],
             'price'        => $order[''],
             'shipping'     => $order[''],
             'productName'  => $order[''],
        ];
    }

    private function isExpress($order)
    {
        return 0;
    }
}
