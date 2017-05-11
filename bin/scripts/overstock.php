<?php

require 'database.php';

$file = 'w:/data/overstock.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$columns = [
    'sku',
    'title',
    'cost',
    'condition',
    'allocation',
    'qty',
    'mpn',
    'note',
    'upc',
    'weight',
    'reserved',
    'row_num',
];

$db->execute('TRUNCATE TABLE overstock');

$count = 0;
while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        unset($data['row_num']);
        $db->insertAsDict('overstock', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count overstock items imported\n";
