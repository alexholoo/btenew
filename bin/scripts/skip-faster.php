<?php

require 'database.php';

if (!($fh = @fopen('w:/data/skip.csv', 'rb'))) {
    echo 'Failed to open file: skip.csv';
    exit;
}

echo "loading skip.csv\n";

fgetcsv($fh); // skip the first line

$data = [];
while(($fields = fgetcsv($fh))) {
    $data[] = $fields;
}

$columns = array(
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
);

try {
    $sql = genInsertSql('skipped_items', $columns, $data);
    $success = $db->execute($sql);
} catch (Exception $e) {
    echo $e->getMessage(), EOL;
}

fclose($fh);

echo "DONE\n";
