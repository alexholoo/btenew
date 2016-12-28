<?php

include 'classes/Job.php';

class EbayOrderJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $config = include __DIR__ . '/../app/config/ebay.php';

        // BTE - CAD
        $client = new Marketplace\eBay\Client($config['bte']);
        $table = 'ebay_order_report_bte';

        $res = $client->getOrders();
        //print_r($res);
        if ($res->Ack == 'Success') {
            $orders = $res->OrderArray->Order;
            foreach ($orders as $order) {
                echo $order->OrderID, PHP_EOL;
                $this->saveOrder($order, $table);
            }
        }

        // ODO - USD
        $client = new Marketplace\eBay\Client($config['odo']);
        $table = 'ebay_order_report_odo';

        $res = $client->getOrders();
        //print_r($res);
        if ($res->Ack == 'Success') {
            $orders = $res->OrderArray->Order;
            foreach ($orders as $order) {
                echo $order->OrderID, PHP_EOL;
                $this->saveOrder($order, $table);
            }
        }
    }

    public function saveOrder($order, $table)
    {
        if ($order->OrderStatus == "Cancelled") {
            return;
        }

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

        try {
            $success = $this->db->insertAsDict($table,
                array(
                    'OrderID'             => $OrderID,
                    'Status'              => $Status,
                    'BuyerUsername'       => $BuyerUsername,
                    'DatePaid'            => $DatePaid,
                    'Currency'            => $Currency,
                    'AmountPaid'          => $AmountPaid,
                    'SalesTaxAmount'      => $SalesTaxAmount,
                    'ShippingService'     => $ShippingService,
                    'ShippingServiceCost' => $ShippingServiceCost,
                )
            );

            $this->saveOrderItem($order);
            $this->saveShippingAddress($order);

        } catch (Exception $e) {
            // echo $e->getMessage(), EOL;
        }
    }

    private function saveOrderItem($order)
    {
        $OrderID = (string)$order->OrderID;

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

            if (!$Tracking) {
                $Tracking = 'NA';
            }

            try {
                $success = $this->db->insertAsDict('ebay_order_item',
                    array(
                        'OrderID'           => $OrderID,
                        'SKU'               => $SKU,
                        'QuantityPurchased' => $QuantityPurchased,
                        'TransactionID'     => $TransactionID,
                        'TransactionPrice'  => $TransactionPrice,
                        'Tracking'          => $Tracking,
                        'ItemID'            => $ItemID,
                        'Email'             => $Email,
                        'RecordNumber'      => $RecordNumber,
                    )
                );
            } catch (Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }
    }

    private function saveShippingAddress($order)
    {
        $OrderID         = (string)$order->OrderID;
        $shippingAddress = $order->ShippingAddress;
        $Name            = (string)$shippingAddress->Name;
        $Address         = (string)$shippingAddress->Street1;
        $Address2        = (string)$shippingAddress->Street2;
        $City            = (string)$shippingAddress->CityName;
        $Province        = (string)$shippingAddress->StateOrProvince;
        $PostalCode      = (string)$shippingAddress->PostalCode;
        $Country         = (string)$shippingAddress->Country;
        $Phone           = (string)$shippingAddress->Phone;

        try {
            $success = $this->db->insertAsDict('ebay_order_shipping_address',
                array(
                    'OrderID'    => $OrderID,
                    'Name'       => $Name,
                    'Address'    => $Address,
                    'Address2'   => $Address2,
                    'City'       => $City,
                    'Province'   => $Province,
                    'PostalCode' => $PostalCode,
                    'Country'    => $Country,
                    'Phone'      => $Phone,
                )
            );
        } catch (Exception $e) {
            // echo $e->getMessage(), EOL;
        }
    }
}

include __DIR__ . '/../public/init.php';

$job = new EbayOrderJob();
$job->run($argv);
