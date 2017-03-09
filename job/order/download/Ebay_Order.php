<?php

use Marketplace\eBay\OrderReportFile;

class Ebay_Order extends OrderDownloader
{
    public function download()
    {
        // BTE - CAD
        $client = new Marketplace\eBay\Client('bte');
        $filename = Filenames::get('ebay.bte.order');
        $orderFile = new OrderReportFile($filename);

        $res = $client->getOrders();
        //print_r($res);
        if ($res->Ack == 'Success') {
            $orders = $res->OrderArray->Order;
            foreach ($orders as $order) {
                echo $order->OrderID, PHP_EOL;
                $this->saveOrder($order, $orderFile);
            }
        }

        // ODO - USD
        $client = new Marketplace\eBay\Client('odo');
        $filename = Filenames::get('ebay.odo.order');
        $orderFile = new OrderReportFile($filename);

        $res = $client->getOrders();
        //print_r($res);
        if ($res->Ack == 'Success') {
            $orders = $res->OrderArray->Order;
            foreach ($orders as $order) {
                echo $order->OrderID, PHP_EOL;
                $this->saveOrder($order, $orderFile);
            }
        }
    }

    public function saveOrder($order, $orderFile)
    {
        if ($order->OrderStatus == "Cancelled") {
            return;
        }

        // Order General
        $OrderID             = (string)$order->OrderID;
       #$ExtOrderID          = (string)$order->ExtendedOrderID;
        $Status              = (string)$order->OrderStatus;
        $BuyerUsername       = (string)$order->BuyerUserID;
        $DatePaid            = substr($order->PaidTime, 0, 10);
        $Currency            = (string)$order->AmountPaid['currencyID'];
        $AmountPaid          = (string)$order->AmountPaid;
        $SalesTaxAmount      = (string)$order->ShippingDetails->SalesTax->SalesTaxAmount;
        $ShippingService     = (string)$order->ShippingServiceSelected->ShippingService;
        $ShippingServiceCost = (string)$order->ShippingServiceSelected->ShippingCost;

        // Shipping Address
        $shippingAddress     = $order->ShippingAddress;
        $Name                = (string)$shippingAddress->Name;
        $Address             = (string)$shippingAddress->Street1;
        $Address2            = (string)$shippingAddress->Street2;
        $City                = (string)$shippingAddress->CityName;
        $Province            = (string)$shippingAddress->StateOrProvince;
        $PostalCode          = (string)$shippingAddress->PostalCode;
        $Country             = (string)$shippingAddress->Country;
        $Phone               = (string)$shippingAddress->Phone;

        // Order Items
        $transactions = $order->TransactionArray;

        foreach ($transactions->Transaction as $transaction) {
            $SKU               = (string)$transaction->Item->SKU;
            $ProductName       = (string)$transaction->Item->Title;
            $QuantityPurchased = (string)$transaction->QuantityPurchased;
            $TransactionID     = (string)$transaction->TransactionID;
            $TransactionPrice  = (string)$transaction->TransactionPrice;
           #$Tracking          = (string)$transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;
            $ItemID            = (string)$transaction->Item->ItemID;
            $Email             = (string)$transaction->Buyer->Email;
           #$RecordNumber      = (string)$transaction->ShippingDetails->SellingManagerSalesRecordNumber;

           #if (!$Tracking) {
           #    $Tracking = 'NA';
           #}

            $orderFile->write(compact(
                // Order
                'OrderID',
                'Status',
                'BuyerUsername',
                'DatePaid',
                'Currency',
                'AmountPaid',
                'SalesTaxAmount',
                'ShippingService',
                'ShippingServiceCost',
                // Item
                'SKU',
                'QuantityPurchased',
                'TransactionID',
                'TransactionPrice',
               #'Tracking',
                'ItemID',
                'Email',
               #'RecordNumber',
                'ProductName',
                // Address
                'Name',
                'Address',
                'Address2',
                'City',
                'Province',
                'PostalCode',
                'Country',
                'Phone'
            ));
        }
    }
}
