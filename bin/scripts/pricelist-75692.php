<?php

require 'database.php';

$file = 'w:/data/csv/75692.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$columns = [
    'sku',
    'itemid',
    'description',
    'vendor',
    'cat',
    'toronto',
    'vancouver',
    'price',
    'weight',
    'size',
    'unit',
    'subcategory',
    'status',
    'upc',
    'ir',
    'ir_expiration',
];

$count = 0;

$items = [];
while(($fields = fgetcsv($fh))) {
    try {
        $data = array_combine($columns, $fields);
        $items[] = $data;
		if (count($items) >= 2000) {
			$sql = genInsertSql('pricelist_75692', $columns, $items);
			$db->execute($sql);
            $count += count($items);
			$items = [];
		}
    } catch (Exception $e) {
        //echo $e->getMessage(), EOL;
    }
}

if (count($items) > 0) {
    $sql = genInsertSql('pricelist_75692', $columns, $items);
    $db->execute($sql);
    $count += count($items);
    $items = [];
}

fclose($fh);

echo "$count DONE\n";
