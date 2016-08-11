<?php

require 'database.php';

if (!($fh = @fopen('w:/out/shipping/master_shipment.txt', 'rb'))) {
    echo 'Failed to open file: master_shipment.txt';
    exit;
}

echo "loading master_shipment.txt\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh, 0, "\t"))) {

    $order_id = $fields[0];
    $order_item_id = $fields[1];
    $qty = $fields[2];
    $ship_date = $fields[3];
    $carrier_code = $fields[4];
    $carrier_name = $fields[5];
    $tracking_number = $fields[6];
    $ship_method = $fields[7];
    $shipping_address = $fields[8];
    $site = $fields[9];

    try {
        $success = $db->insertAsDict('master_shipment',
            array(
                'order_id' => $order_id,
                #search_key' => $order_id,
                'order_item_id' => $order_item_id,
                'qty' => $qty,
                'ship_date' => $ship_date,
                'carrier_code' => $carrier_code,
                'carrier_name' => $carrier_name,
                'tracking_number' => $tracking_number,
                'ship_method' => $ship_method,
                'shipping_address' => $shipping_address,
                'site' => $site,
            )
        );

        if (!$success) {
            echo $order_id, EOL;
        }

        $count++;

    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
