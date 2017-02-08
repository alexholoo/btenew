<?php

require 'database.php';

if (!($fh = @fopen('w:/out/shipping/master_shipment.txt', 'rb'))) {
    echo 'Failed to open file: master_shipment.txt';
    exit;
}

echo "loading master_shipment.txt\n";

fgetcsv($fh); // skip the first line

$columns = [
    'order_id',
    'order_item_id',
    'qty',
    'ship_date',
    'carrier_code',
    'carrier_name',
    'tracking_number',
    'ship_method',
    'shipping_address',
    'site',
];

$count = 0;
while(($fields = fgetcsv($fh, 0, "\t"))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('master_shipment', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
