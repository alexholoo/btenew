<?php

require 'database.php';

if (!($fh = @fopen('w:/data/skip.csv', 'rb'))) {
    echo 'Failed to open file: skip.csv';
    exit;
}

echo "loading skip.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $partnum    = $fields[0];
    $notes      = $fields[1];
    $us_floor   = $fields[2];
    $ca_floor   = $fields[3];
    $title      = $fields[4];
    $cost       = $fields[5];
    $condition  = $fields[6];
    $mpn        = $fields[7];
    $source     = $fields[8];
    $source_sku = $fields[9];
    $refnum     = $fields[10];
    $upc        = $fields[11];
    $total      = $fields[12];
    $mpn1       = $fields[13];
    $id1        = $fields[14];

    try {
        $success = $db->insertAsDict('skipped_items',
            array(
                'partnum'    => $partnum,
                'notes'      => $notes,
                'us_floor'   => $us_floor,
                'ca_floor'   => $ca_floor,
                'title'      => $title,
                'cost'       => $cost,
                'condition'  => $condition,
                'mpn'        => $mpn,
                'source'     => $source,
                'source_sku' => $source_sku,
                'refnum'     => $refnum,
                'upc'        => $upc,
                'total'      => $total,
                'mpn1'       => $mpn1,
                'id1'        => $id1,
            )
        );

        if (!$success) {
            echo $partnum, EOL;
        }

        $count++;

    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
