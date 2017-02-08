<?php

require 'database.php';

$file = 'w:/out/shipping/all_mgn_orders.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$columns = [
    'channel',
    'date',
    'order_id',
    'mgn_order_id',
    'express',
    'buyer',
    'address',
    'city',
    'province',
    'postalcode',
    'country',
    'phone',
    'email',
    'skus_sold',
    'sku_price',
    'skus_qty',
    'shipping',
    'product_name',
];

$count = 0;

while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('all_mgn_orders', $data);
        $count++;
    } catch (Exception $e) {
        //echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
