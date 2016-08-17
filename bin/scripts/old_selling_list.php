<?php

require 'database.php';

$starttime = microtime(true);

if (!($fh = @fopen('w:/data/selling_list.csv', 'rb'))) {
    echo 'Failed to open file: selling_list.csv';
    exit;
}

echo "loading selling_list.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $sku = $fields[0];
    $pn_used = $fields[1];
    $cost = $fields[2];
    $qty = $fields[3];
    $syn_pn = $fields[4];
    $td_pn = $fields[5];
    $ing_pn = $fields[6];
    $dh_pn = $fields[7];
    $as_pn = $fields[8];
    $tak_pn = $fields[9];
    $ep_pn = $fields[10];
    $bte_pn = $fields[11];
    $note = $fields[12];
    $ebay_price = $fields[13];
    $newegg_price = $fields[14];
    $mpn = $fields[15];
    $length = $fields[16];
    $width = $fields[17];
    $depth = $fields[18];
    $weight = $fields[19];

    try {
        $success = $db->insertAsDict('selling_list',
            compact(
                'sku',
                'pn_used',
                'cost',
                'qty',
                'syn_pn',
                'td_pn',
                'ing_pn',
                'dh_pn',
                'as_pn',
                'tak_pn',
                'ep_pn',
                'bte_pn',
                'note',
                'ebay_price',
                'newegg_price',
                'mpn',
                'length',
                'width',
                'depth',
                'weight'
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

echo "$count DONE in ", number_format(microtime(true) - $starttime, 4), " seconds\n";
