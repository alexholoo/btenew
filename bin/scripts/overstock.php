<?php

require 'database.php';

$file = 'w:/data/overstock.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$db->execute('TRUNCATE TABLE overstock');

$count = 0;
while(($fields = fgetcsv($fh))) {

    $sku        = $fields[0];
    $title      = $fields[1];
    $cost       = $fields[2];
    $condition  = $fields[3];
    $allocation = $fields[4];
    $qty        = $fields[5];
    $mpn        = $fields[6];
    $note       = $fields[7];
    $upc        = $fields[8];
    $weight     = $fields[9];
    $reserved   = $fields[10];
    $row_num    = $fields[11];

    try {
        $db->insertAsDict('overstock', [
            'sku'        => $sku,
            'title'      => $title,
            'cost'       => $cost,
            'condition'  => $condition,
            'allocation' => $allocation,
            'qty'        => $qty,
            'mpn'        => $mpn,
            'note'       => $note,
            'upc'        => $upc,
            'weight'     => $weight,
            'reserved'   => $reserved,
            'row_num'    => $row_num,
        ]);

        $count++;

    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count overstock items imported\n";
