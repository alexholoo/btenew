<?php

require 'database.php';

if (!($fh = @fopen('w:/data/skip.csv', 'rb'))) {
    echo 'Failed to open file: skip.csv';
    exit;
}

echo "loading skip.csv\n";

fgetcsv($fh); // skip the first line

$columns = [  // csvcols / tblcols
    'partnum',
    'notes',
    'us_floor',
    'ca_floor',
    'title',
    'cost',
    'condition',
    'mpn',
    'source',
    'source_sku',
    'refnum',
    'upc',
    'total',
    'mpn1',
    'id1',
];

$count = 0;
while (($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('skipped_items', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
