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
$db->execute('TRUNCATE TABLE inventory_location');
while(($fields = fgetcsv($fh))) {
    try {
        if (count($columns) == count($fields)) {
            $data = array_combine($columns, $fields);
        } else {
            if (count($fields) > count($columns)) {
                $data = array_combine($columns, array_slice($fields, 0, count($columns)));
            } else {
                print_r($fields);
                continue;
            }
        }
        $db->insertAsDict('inventory_location', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
