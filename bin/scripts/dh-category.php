<?php

require 'database.php';

$fname = '../../doc/dh/DH-CATLIST';
if (!($fh = @fopen($fname, 'rb'))) {
    echo "Failed to open file: $fname";
    exit;
}

echo "loading skip.csv\n";

fgetcsv($fh); // skip the first line

while (($fields = fgetcsv($fh, 0, '|'))) {
    $pid   = $fields[0];
    $pname = $fields[1];

    $cid   = $fields[2];
    $cname = $fields[3];

    try {
        $db->insertAsDict('dh_category', [
            'category_id'   => $pid,
            'category_name' => $pname,
            'category_desc' => $pname,
            'parent_id'     => '',
        ]);
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }

    try {
        $db->insertAsDict('dh_category', [
            'category_id'   => $cid,
            'category_name' => $cname,
            'category_desc' => $pname. ' > ' .$cname,
            'parent_id'     => $pid,
        ]);
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "DONE\n";
