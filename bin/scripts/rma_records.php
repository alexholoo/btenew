<?php

require 'database.php';

$file = 'e:/RMA_current_records.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$columns = [
	'date_in',
	'product_desc',
	'partnum',
	'our_recnum',
	'status',
	'supplier',
	'order_id',
	'after_checked',
	'date_shipout',
	'ship_method',
	'supplier_recv_recnum',
	'done',
];

$db->execute('TRUNCATE TABLE rma_records');

$count = 0;
while(($fields = fgetcsv($fh))) {
    $fields = array_slice($fields, 0, count($columns));

    try {
        $data = array_combine($columns, $fields);

        if (empty($data['date_in'])) continue;
       #if (empty($data['date_shipout'])) continue;

        $data['date_in'] = date('Y-m-d', strtotime($data['date_in']));
       #$data['date_shipout'] = date('Y-m-d', strtotime($data['date_shipout']));

        $db->insertAsDict('rma_records', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count RMA records imported\n";
