<?php

require 'database.php';

$filename = 'w:/data/csv/bte-inventory.csv';
if (!($fh = @fopen($filename, 'rb'))) {
    echo "Failed to open file: $filename";
    exit;
}

echo "loading $filename\n";

fgetcsv($fh); // skip the first line

$columns = [
    'partnum',
    'title',
    'selling_cost',
    'type',
    'fba_allocation',
    'notes',
    'condition',
    'qty',
    'weight',
    'upc',
    'mfr',
    'mpn',
    'length',
    'width',
    'depth',
    'purchase_price',
    'total',
];

$count = 0;
$db->execute('TRUNCATE TABLE bte_inventory');
while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, array_slice($fields, 0, count($columns)));
        $db->insertAsDict('bte_inventory', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
