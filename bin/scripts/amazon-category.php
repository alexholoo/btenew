<?php

require 'database.php';

$fname = 'e:/Download/amazon/Amazon_US_btg.csv';
if (!($fh = @fopen($fname, 'rb'))) {
    echo "Failed to open file: $fname";
    exit;
}

echo "loading $fname\n";

fgetcsv($fh); // skip the first line
fgetcsv($fh); // skip the first line

while (($fields = fgetcsv($fh))) {
    $cname = $fields[0];
    $cid   = $fields[1];
    $cpath = $fields[2];
    $croot = $fields[3];

    try {
        $db->insertAsDict('amazon_category', [
            'category_id'   => $cid,
            'category_name' => $cname,
            'category_path' => $cpath,
            'category_root' => $croot,
            'parent_id'     => '',
        ]);
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "DONE\n";
