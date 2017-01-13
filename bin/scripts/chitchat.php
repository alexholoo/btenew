<?php

require 'database.php';

$file = 'w:/out/chitchat.csv';
if (!($fh = @fopen($file, 'rb'))) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {
    try {
        $db->insertAsDict('chitchat',
            array(
                'order_id'    => $fields[0],
                'carrier'     => $fields[0],
                'trackingnum' => $fields[0],
                'site'        => $fields[0],
                'date'        => $fields[0],
                'source'      => $fields[0],
                'contact'     => $fields[0],
                'address'     => $fields[0],
                'address2'    => $fields[0],
                'city'        => $fields[0],
                'province'    => $fields[0],
                'postalcode'  => $fields[0],
                'country'     => $fields[0],
                'class'       => $fields[0],
                'item'        => $fields[0],
                'lbs'         => $fields[0],
                'value'       => $fields[0],
                'handling'    => $fields[0],
                'sku'         => $fields[0],
            )
        );

        $count++;

    } catch (Exception $e) {
        //echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
