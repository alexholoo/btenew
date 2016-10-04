<?php

require __DIR__ . '/database.php';

function importNeweggOrders($db, $file, $table)
{
    if (!($fh = @fopen($file, 'rb'))) {
        echo "Failed to open file: $file\n";
        exit;
    }

    echo "loading $file\n";

    fgetcsv($fh); // skip the first line

    $count = 0;
    while(($fields = fgetcsv($fh))) {

        $OrderNumber = $fields[0];
        $OrderDateTime = $fields[1];
        $SalesChannel = $fields[2];
        $FulfillmentOption = $fields[3];
        $ShipToAddressLine1 = $fields[4];
        $ShipToAddressLine2 = $fields[5];
        $ShipToCity = $fields[6];
        $ShipToState = $fields[7];
        $ShipToZipCode = $fields[8];
        $ShipToCountry = $fields[9];
        $ShipToFirstName = $fields[10];
        $ShipToLastName = $fields[11];
        $ShipToCompany = $fields[12];
        $ShipToPhoneNumber = $fields[13];
        $OrderCustomerEmail = $fields[14];
        $OrderShippingMethod = $fields[15];
        $ItemSellerPartNo = $fields[16];
        $ItemNeweggNo = $fields[17];
        $ItemUnitPrice = $fields[18];
        $ExtendUnitPrice = $fields[19];
        $ItemUnitShippingCharge = $fields[20];
        $ExtendShippingCharge = $fields[21];
        $OrderShippingTotal = $fields[22];
        $GSTorHSTTotal = $fields[23];
        $PSTorQSTTotal = $fields[24];
        $OrderTotal = $fields[25];
        $QuantityOrdered = $fields[26];
        $QuantityShipped = $fields[27];
        $ShipDate = $fields[28];
        $ActualShippingCarrier = $fields[29];
        $ActualShippingMethod = $fields[30];
        $TrackingNumber = $fields[31];
        $ShipFromAddress = $fields[32];
        $ShipFromCity = $fields[33];
        $ShipFromState = $fields[34];
        $ShipFromZipcode = $fields[35];
        $ShipFromName = $fields[36];
 
        try {
            $success = $db->insertAsDict($table,
                array(
                    'OrderNumber' => $OrderNumber,
                    'OrderDateTime' => $OrderDateTime,
                    'SalesChannel' => $SalesChannel,
                    'FulfillmentOption' => $FulfillmentOption,
                    'ShipToAddressLine1' => $ShipToAddressLine1,
                    'ShipToAddressLine2' => $ShipToAddressLine2,
                    'ShipToCity' => $ShipToCity,
                    'ShipToState' => $ShipToState,
                    'ShipToZipCode' => $ShipToZipCode,
                    'ShipToCountry' => $ShipToCountry,
                    'ShipToFirstName' => $ShipToFirstName,
                    'ShipToLastName' => $ShipToLastName,
                    'ShipToCompany' => $ShipToCompany,
                    'ShipToPhoneNumber' => $ShipToPhoneNumber,
                    'OrderCustomerEmail' => $OrderCustomerEmail,
                    'OrderShippingMethod' => $OrderShippingMethod,
                    'ItemSellerPartNo' => $ItemSellerPartNo,
                    'ItemNeweggNo' => $ItemNeweggNo,
                    'ItemUnitPrice' => $ItemUnitPrice,
                    'ExtendUnitPrice' => $ExtendUnitPrice,
                    'ItemUnitShippingCharge' => $ItemUnitShippingCharge,
                    'ExtendShippingCharge' => $ExtendShippingCharge,
                    'OrderShippingTotal' => $OrderShippingTotal,
                    'GSTorHSTTotal' => $GSTorHSTTotal,
                    'PSTorQSTTotal' => $PSTorQSTTotal,
                    'OrderTotal' => $OrderTotal,
                    'QuantityOrdered' => $QuantityOrdered,
                    'QuantityShipped' => $QuantityShipped,
                    'ShipDate' => $ShipDate,
                    'ActualShippingCarrier' => $ActualShippingCarrier,
                    'ActualShippingMethod' => $ActualShippingMethod,
                    'TrackingNumber' => $TrackingNumber,
                    'ShipFromAddress' => $ShipFromAddress,
                    'ShipFromCity' => $ShipFromCity,
                    'ShipFromState' => $ShipFromState,
                    'ShipFromZipcode' => $ShipFromZipcode,
                    'ShipFromName' => $ShipFromName,
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

$files = glob('E:/BTE/orders/newegg/orders_ca/OrderList_*.*');
foreach ($files as $file) {
    importNeweggOrders($db, $file, 'newegg_order_report');
}
