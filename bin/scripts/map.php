<?php

require 'database.php';

if (!($fh = @fopen('w:/data/MAP.csv', 'rb'))) {
    echo 'Failed to open file: MAP.csv';
    exit;
}

echo "loading MAP.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $partnum = $fields[0];
    $mpn     = $fields[1];
    $title   = $fields[2];
    $map_us  = $fields[3];
    $map_ca  = $fields[4];
    $note1   = $fields[5];
    $note2   = $fields[6];
    $refnum  = $fields[7];

    try {
        $success = $db->insertAsDict('vendor_map',
            array(
                'partnum' => $partnum,
                'mpn'     => $mpn,
                'title'   => $title,
                'map_us'  => $map_us,
                'map_ca'  => $map_ca,
                'note1'   => $note1,
                'note2'   => $note2,
                'refnum'  => $refnum,
            )
            // this is better
            // compact('partnum', 'mpn', 'title', 'map_us', 'map_ca', 'note1', 'note2', 'refnum')
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
