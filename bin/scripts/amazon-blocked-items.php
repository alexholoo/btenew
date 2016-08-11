<?php

require 'database.php';

if (!($fh = @fopen('w:/data/amazon_blocked_items.csv', 'rb'))) {
    echo 'Failed to open file: amazon_blocked_items.csv';
    exit;
}

echo "loading amazon_blocked_items.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $sku_us  = $fields[0];
    $upc     = $fields[1];
    $mpn     = $fields[2];
    $sku_ca  = $fields[3];
    $refnum  = $fields[4];
    $notes   = $fields[5];
    $field   = $fields[6];

    try {
        $success = $db->insertAsDict('amazon_blocked_items',
            array(
                'sku_us'  => $sku_us,
                'upc'     => $upc,
                'mpn'     => $mpn,
                'sku_ca'  => $sku_ca,
                'refnum'  => $refnum,
                'notes'   => $notes,
                'field'   => $field,
            )
            // this is better
            #compact('sku_us', 'upc', 'mpn', 'sku_ca', 'refnum', 'notes', 'field')
        );

        if (!$success) {
            echo $sku_us, EOL;
        }

        $count++;

    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";

