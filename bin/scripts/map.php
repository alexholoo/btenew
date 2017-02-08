<?php

require 'database.php';

if (!($fh = @fopen('w:/data/MAP.csv', 'rb'))) {
    echo 'Failed to open file: MAP.csv';
    exit;
}

echo "loading MAP.csv\n";

fgetcsv($fh); // skip the first line

$columns = [
    'partnum',
    'mpn',
    'title',
    'map_us',
    'map_ca',
    'note1',
    'note2',
    'refnum',
];

$count = 0;
while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('vendor_map', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
