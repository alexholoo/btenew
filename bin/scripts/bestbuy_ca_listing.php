<?php

require 'database.php';

$file = 'e:/download/offers.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$columns = [ // columns of table bestbuy_ca_listing
    'SKU',
    'Product_ID',
    'Category_Code',
    'Category_Label',
    'Product_Name',
    'Condition',
    'Price',
    'Qty',
    'Alert_Threshold',
    'Logistic_Class',
    'Activated',
    'Available_Start_Date',
    'Available_End_Date',
];

$count = 0;
while(($fields = fgetcsv($fh, 0, ';'))) {
    try {
        $data = array_combine($columns, $fields);
        $db->insertAsDict('bestbuy_ca_listing', $data);
        $count++;
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
