<?php

require 'database.php';

$columns = array(
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
);

$starttime = microtime(true);

if (!($fh = @fopen('w:/data/selling_list.csv', 'rb'))) {
    echo 'Failed to open file: selling_list.csv';
    exit;
}

echo "loading selling_list.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
$data = [];

while(($fields = fgetcsv($fh))) {
    $data[] = $fields;
    if (count($data) == 1000) {
        try {
            $sql = genInsertSql('selling_list', $columns, $data);
            $db->execute($sql);
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }
        $data = [];
    }

    $count++;
}

if (count($data) > 0) {
    try {
        $sql = genInsertSql('selling_list', $columns, $data);
        $db->execute($sql);
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
    $data = [];
}

fclose($fh);

echo "$count DONE in ", number_format(microtime(true) - $starttime, 4), " seconds\n";
