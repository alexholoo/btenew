<?php

require 'database.php';

if (!($fh = @fopen('w:/out/inventory.csv', 'rb'))) {
    echo 'Failed to open file: inventory.csv';
    exit;
}

echo "loading inventory.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $partnum    = $fields[0];
    $upc        = $fields[1];
    $location   = $fields[2];
    $qty        = $fields[3];
    $sn         = $fields[4];
    $note       = $fields[5];

    try {
        $success = $db->insertAsDict('inventory',
            array(
                'partnum'  => $partnum,
                'upc'      => $upc,
                'location' => $location,
                'qty'      => $qty,
                'sn'       => $sn,
                'note'     => $note,
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
