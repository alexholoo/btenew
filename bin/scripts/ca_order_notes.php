<?php

require 'database.php';

$file = 'w:/out/purchasing/ca_order_notes.csv';
if (($fh = @fopen($file, 'rb')) === false) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$columns = [
    'date',
    'order_id',
    'stock_status',
    'express',
    'qty',
    'supplier',
    'supplier_sku',
    'mpn',
    'supplier_no',
    'notes',
    'related_sku',
    'dimension',
];

$count = 0;
while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('ca_order_notes', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
