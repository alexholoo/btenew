<?php

require __DIR__ . '/database.php';

function importEbayOrders($db, $file, $table)
{
    $file = "e:/BTE/orders/ebay/$file.csv";
    if (!($fh = @fopen($file, 'rb'))) {
        echo "Failed to open file: $file\n";
        exit;
    }

    echo "loading $file\n";

    fgetcsv($fh); // skip the first line

    $count = 0;
    while(($fields = fgetcsv($fh))) {

        $OrderID             = $fields[0];
        $Status              = $fields[1];
        $BuyerUsername       = $fields[2];
        $DatePaid            = $fields[3];
        $AmountPaid          = $fields[4];
        $SalesTaxAmount      = $fields[5];
        $ShippingService     = $fields[6];
        $ShippingServiceCost = $fields[7];
        $Name                = $fields[8];
        $Address             = $fields[9];
        $Address2            = $fields[10];
        $City                = $fields[11];
        $Province            = $fields[12];
        $PostalCode          = $fields[13];
        $Country             = $fields[14];
        $Phone               = $fields[15];
        $QuantityPurchased   = $fields[16];
        $Email               = $fields[17];
        $SKU                 = $fields[18];
        $TransactionID       = $fields[19];
        $TransactionPrice    = $fields[20];
        $Tracking            = $fields[21];
        $ItemID              = $fields[22];
        $RecordNumber        = isset($fields[23]) ? $fields[23] : '';

        try {
            $success = $db->insertAsDict($table,
                array(
                    'OrderID'             => $OrderID,
                    'Status'              => $Status,
                    'BuyerUsername'       => $BuyerUsername,
                    'DatePaid'            => $DatePaid,
                    'AmountPaid'          => str_replace(['CAD', 'USD'], '', $AmountPaid),
                    'SalesTaxAmount'      => str_replace(['CAD', 'USD'], '', $SalesTaxAmount),
                    'ShippingService'     => $ShippingService,
                    'ShippingServiceCost' => str_replace(['CAD', 'USD'], '', $ShippingServiceCost),
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
                    'TransactionPrice'    => str_replace(['CAD', 'USD'], '', $TransactionPrice),
                    'Tracking'            => $Tracking,
                    'ItemID'              => $ItemID,
                    'RecordNumber'        => $RecordNumber,
                )
            );

            $count++;

        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    fclose($fh);

    echo "$count orders imported\n";
}

importEbayOrders($db, 'ebay_orders_bte', 'ebay_order_report_bte');
importEbayOrders($db, 'ebay_orders_odo', 'ebay_order_report_odo');
