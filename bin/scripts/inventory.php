<?php

require 'database.php';

if (!($fh = @fopen('w:/out/inventory.csv', 'rb'))) {
    echo 'Failed to open file: inventory.csv';
    exit;
}

echo "loading inventory.csv\n";

fgetcsv($fh); // skip the first line

$columns = [
    'partnum',
    'upc',
    'location',
    'qty',
    'sn',
    'note',
];

$count = 0;
while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('inventory', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
