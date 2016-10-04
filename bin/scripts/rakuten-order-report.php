<?php

require __DIR__ . '/database.php';

function importRakutenOrders($db, $file, $table)
{
    if (!($fh = @fopen($file, 'rb'))) {
        echo "Failed to open file: $file\n";
        exit;
    }

    echo "loading $file\n";

    fgetcsv($fh); // skip the first line

    $count = 0;
    while(($fields = fgetcsv($fh, 0, "\t"))) {
        if (count($fields) < 2) continue;

        $SellerShopperNumber = $fields[0];
        $Receipt_ID = $fields[1];
        $Receipt_Item_ID = $fields[2];
        $ListingID = $fields[3];
        $Date_Entered = date('Y-m-d H:i:s', strtotime($fields[4]));
        $Sku = $fields[5];
        $ReferenceId = $fields[6];
        $Quantity = $fields[7];
        $Qty_Shipped = $fields[8];
        $Qty_Cancelled = $fields[9];
        $Title = $fields[10];
        $Price = $fields[11];
        $Product_Rev = $fields[12];
        $Shipping_Cost = $fields[13];
        $ProductOwed = $fields[14];
        $ShippingOwed = $fields[15];
        $Commission = $fields[16];
        $ShippingFee = $fields[17];
        $PerItemFee = $fields[18];
        $Tax_Cost = $fields[19];
        $Bill_To_Company = $fields[20];
        $Bill_To_Phone = $fields[21];
        $Bill_To_Fname = $fields[22];
        $Bill_To_Lname = $fields[23];
        $Email = $fields[24];
        $Ship_To_Name = $fields[25];
        $Ship_To_Company = $fields[26];
        $Ship_To_Street1 = $fields[27];
        $Ship_To_Street2 = $fields[28];
        $Ship_To_City = $fields[29];
        $Ship_To_State = $fields[30];
        $Ship_To_Zip = $fields[31];
        $ShippingMethodId = $fields[32];

        try {
            $success = $db->insertAsDict($table,
                array(
                    'SellerShopperNumber' => $SellerShopperNumber,
                    'Receipt_ID' => $Receipt_ID,
                    'Receipt_Item_ID' => $Receipt_Item_ID,
                    'ListingID' => $ListingID,
                    'Date_Entered' => $Date_Entered,
                    'Sku' => $Sku,
                    'ReferenceId' => $ReferenceId,
                    'Quantity' => $Quantity,
                    'Qty_Shipped' => $Qty_Shipped,
                    'Qty_Cancelled' => $Qty_Cancelled,
                    'Title' => $Title,
                    'Price' => $Price,
                    'Product_Rev' => $Product_Rev,
                    'Shipping_Cost' => $Shipping_Cost,
                    'ProductOwed' => $ProductOwed,
                    'ShippingOwed' => $ShippingOwed,
                    'Commission' => $Commission,
                    'ShippingFee' => $ShippingFee,
                    'PerItemFee' => $PerItemFee,
                    'Tax_Cost' => $Tax_Cost,
                    'Bill_To_Company' => $Bill_To_Company,
                    'Bill_To_Phone' => $Bill_To_Phone,
                    'Bill_To_Fname' => $Bill_To_Fname,
                    'Bill_To_Lname' => $Bill_To_Lname,
                    'Email' => $Email,
                    'Ship_To_Name' => $Ship_To_Name,
                    'Ship_To_Company' => $Ship_To_Company,
                    'Ship_To_Street1' => $Ship_To_Street1,
                    'Ship_To_Street2' => $Ship_To_Street2,
                    'Ship_To_City' => $Ship_To_City,
                    'Ship_To_State' => $Ship_To_State,
                    'Ship_To_Zip' => $Ship_To_Zip,
                    'ShippingMethodId' => $ShippingMethodId,
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

$files = glob('E:/BTE/orders/rakuten/orders_us/23267604_*.*');
foreach ($files as $file) {
    importRakutenOrders($db, $file, 'rakuten_order_report');
}
