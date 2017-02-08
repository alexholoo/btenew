<?php

require 'database.php';

if (!($fh = @fopen('w:/data/amazon_blocked_items.csv', 'rb'))) {
    echo 'Failed to open file: amazon_blocked_items.csv';
    exit;
}

echo "loading amazon_blocked_items.csv\n";

fgetcsv($fh); // skip the first line

$columns = [
    'sku_us',
    'upc',
    'mpn',
    'sku_ca',
    'refnum',
    'notes',
    'field',
];

$count = 0;
while(($fields = fgetcsv($fh))) {

    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('amazon_blocked_items', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
