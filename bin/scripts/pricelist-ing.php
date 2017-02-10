<?php

require 'database.php';

$file = 'w:/data/csv/PRICE.TXT';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$columns = [
	'action_indicator',
	'sku',
	'vendor_number',
	'vendor_name',
	'part_desc_1',
	'part_desc_2',
	'retail_price',
	'vendor_part_number',
	'weight',
	'upc',
	'length',
	'width',
	'height',
	'price_change_flag',
	'customer_price',
	'special_price_flag',
	'availability_flag',
	'sku_status',
	'cpu_code',
	'media_type',
	'category',
	'new_item_receipt_flag',
	'substitute_part',
	'a', // unknown ??
	'instant_rebate_flag',
	'b', // unknown ??
];

$count = 0;

$items = [];
while(($fields = fgetcsv($fh))) {
	$fields = array_map(function($s) { return trim($s); }, $fields);
	$data = array_combine($columns, $fields);

    try {
        $data = array_combine($columns, $fields);

		// trim leading zeros
		$data['retail_price'] = round($data['retail_price'], 2);
		$data['weight'] = round($data['weight'], 2);
		$data['length'] = round($data['length'], 2);
		$data['width']  = round($data['width'], 2);
		$data['height'] = round($data['height'], 2);
		$data['customer_price'] = round($data['customer_price'], 2);
		$data['a'] = round($data['a'], 2);

        $items[] = $data;
		if (count($items) >= 2000) {
			$sql = genInsertSql('pricelist_ing', $columns, $items);
			$db->execute($sql);
            $count += count($items);
			$items = [];
		}
    } catch (Exception $e) {
        //echo $e->getMessage(), EOL;
    }
}

if (count($items) > 0) {
    $sql = genInsertSql('pricelist_ing', $columns, $items);
    $db->execute($sql);
    $count += count($items);
    $items = [];
}

fclose($fh);

echo "$count DONE\n";
