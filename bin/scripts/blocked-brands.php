<?php

require 'database.php';

if (!($fh = @fopen('w:/data/blocked_brands.csv', 'rb'))) {
    echo 'Failed to open file: blocked_brands.csv';
    exit;
}

echo "loading block_brands.csv\n";

fgetcsv($fh); // skip the first line

$columns = [
    'brand',
    'amazon_us',
    'amazon_ca',
    'ebay_ca',
    'ebay_us',
    'amazon_uk',
    'refnum',
];

$count = 0;
while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('blocked_brands', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
