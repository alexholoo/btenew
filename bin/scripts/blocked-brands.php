<?php

require 'database.php';

if (!($fh = @fopen('w:/data/blocked_brands.csv', 'rb'))) {
    echo 'Failed to open file: blocked_brands.csv';
    exit;
}

echo "loading block_brands.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $brand     = $fields[0];
    $amazon_us = $fields[1];
    $amazon_ca = $fields[2];
    $ebay_ca   = $fields[3];
    $ebay_us   = $fields[4];
    $amazon_uk = $fields[5];
    $refnum    = $fields[6];

    try {
        $success = $db->insertAsDict('blocked_brands',
            array(
                'brand'      => $brand,
                'amazon_us'  => $amazon_us,
                'amazon_ca'  => $amazon_ca,
                'ebay_ca'    => $ebay_ca,
                'ebay_us'    => $ebay_us,
                'amazon_uk'  => $amazon_uk,
                'refnum'     => $refnum,
            )
        );

        if (!$success) {
            echo $brand, EOL;
        }

        $count++;

    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
