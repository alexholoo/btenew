<?php

class EbayOrderJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
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
        $ExtOrderID          = (string)$order->ExtendedOrderID;
        $Status              = (string)$order->OrderStatus;
        $BuyerUsername       = (string)$order->BuyerUserID;
        $DatePaid            = substr($order->PaidTime, 0, 10);

        $AmountPaid          = (string)$order->AmountPaid;
        $Currency            = (string)$order->AmountPaid['currencyID'];
        $SalesTaxAmount      = (string)$order->ShippingDetails->SalesTax->SalesTaxAmount;
        $ShippingService     = (string)$order->ShippingServiceSelected->ShippingService;
        $ShippingServiceCost = (string)$order->ShippingServiceSelected->ShippingCost;

        $shippingAddress     = $order->ShippingAddress;
        $Name                = (string)$shippingAddress->Name;
        $Address             = (string)$shippingAddress->Street1;
        $Address2            = (string)$shippingAddress->Street2;
        $City                = (string)$shippingAddress->CityName;
        $Province            = (string)$shippingAddress->StateOrProvince;
        $PostalCode          = (string)$shippingAddress->PostalCode;
        $Country             = (string)$shippingAddress->Country;
        $Phone               = (string)$shippingAddress->Phone;

        $transactions = $order->TransactionArray;
        foreach ($transactions->Transaction as $transaction) {
            $Email             = (string)$transaction->Buyer->Email;
            $SKU               = (string)$transaction->Item->SKU;
            $TransactionID     = (string)$transaction->TransactionID;
            $TransactionPrice  = (string)$transaction->TransactionPrice;
            $QuantityPurchased = (string)$transaction->QuantityPurchased;
            $Tracking          = (string)$transaction->ShippingDetails->ShipmentTrackingDetails->ShipmentTrackingNumber;
            $ItemID            = (string)$transaction->Item->ItemID;
            $RecordNumber      = (string)$transaction->ShippingDetails->SellingManagerSalesRecordNumber;

            if (!$Tracking) {
                $Tracking = 'NA';
            }

            try {
                $success = $this->db->insertAsDict($table,
                    array(
                        'ExtOrderID'          => $ExtOrderID, // Unique ID, Primary Key
                        'OrderID'             => $OrderID,
                        'Status'              => $Status,
                        'BuyerUsername'       => $BuyerUsername,
                        'DatePaid'            => $DatePaid,
                        'Currency'            => $Currency,
                        'AmountPaid'          => $AmountPaid,
                        'SalesTaxAmount'      => $SalesTaxAmount,
                        'ShippingService'     => $ShippingService,
                        'ShippingServiceCost' => $ShippingServiceCost,
                        'Name'                => $Name,
                        'Address'             => $Address,
                        'Address2'            => $Address2,
                        'City'                => $City,
                        'Province'            => $Province,
                        'PostalCode'          => $PostalCode,
                        'Country'             => $Country,
                        'Phone'               => $Phone,
                        'QuantityPurchased'   => $QuantityPurchased,
                        'Email'               => $Email,
                        'SKU'                 => $SKU,
                        'TransactionID'       => $TransactionID,
                        'TransactionPrice'    => $TransactionPrice,
                        'Tracking'            => $Tracking,
                        'ItemID'              => $ItemID,
                        'RecordNumber'        => $RecordNumber,
                    )
                );
            } catch (Exception $e) {
                // echo $e->getMessage(), EOL;
            }
        }
    }
}

include __DIR__ . '/../public/init.php';

$job = new EbayOrderJob();
$job->run($argv);
