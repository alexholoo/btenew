<?php

require 'database.php';

$file = 'w:/out/shipping/all_mgn_orders.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $channel    = $fields[0];
    $date       = $fields[1];
    $order_id   = $fields[2];
    $mgn_order_id = $fields[3];
    $express    = $fields[4];
    $buyer      = $fields[5];
    $address    = $fields[6];
    $city       = $fields[7];
    $province   = $fields[8];
    $postalcode = $fields[9];
    $country    = $fields[10];
    $phone      = $fields[11];
    $email      = $fields[12];
    $skus_sold  = $fields[13];
    $sku_price  = $fields[14];
    $skus_qty   = $fields[15];
    $shipping   = $fields[16];
    $mgn_invoice_id = $fields[17];

    try {
        $success = $db->insertAsDict('all_mgn_orders',
            array(
                'channel'   => $channel,
                'date'      => $date,
                'order_id'  => $order_id,
                'mgn_order_id' => $mgn_order_id,
                'express'   => $express,
                'buyer'     => $buyer,
                'address'   => $address,
                'city'      => $city,
                'province'  => $province,
                'postalcode'=> $postalcode,
                'country'   => $country,
                'phone'     => $phone,
                'email'     => $email,
                'skus_sold' => $skus_sold,
                'sku_price' => $sku_price,
                'skus_qty'  => $skus_qty,
                'shipping'  => $shipping,
                'mgn_invoice_id' => $mgn_invoice_id,
            )
        );

        if (!$success) {
            echo $order_id, EOL;
        }

        $count++;

    } catch (Exception $e) {
        //echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
