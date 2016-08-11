<?php

require 'database.php';

$columns = [
    'sku',
    'recommended_pn',
    'syn_pn',
    'syn_cost',
    'syn_qty',
    'td_pn',
    'td_cost',
    'td_qty',
    'ing_pn',
    'ing_cost',
    'ing_qty',
    'dh_pn',
    'dh_cost',
    'dh_qty',
    'asi_pn',
    'asi_cost',
    'asi_qty',
    'tak_pn',
    'tak_cost',
    'tak_qty',
    'ep_pn',
    'ep_cost',
    'ep_qty',
    'bte_pn',
    'bte_cost',
    'bte_qty',
    'manufacturer',
    'upc',
    'mpn',
    'map_usd',
    'map_cad',
    'width',
    'length',
    'depth',
    'weight',
    'ca_ebay_blocked',
    'us_ebay_blocked',
    'ca_newegg_blocked',
    'us_newegg_blocked',
    'us_amazon_blocked',
    'ca_amazon_blocked',
    'uk_amazon_blocked',
    'jp_amazon_blocked',
    'mx_amazon_blocked',
    'note',
    'name',
    'best_cost',
    'overall_qty'
];

$starttime = microtime(true);

if (!($fh = @fopen('w:/data/master_sku_list.csv', 'rb'))) {
    echo 'Failed to open file: master_sku_list.csv';
    exit;
}

echo "loading master_sku_list.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
$data = [];

while(($fields = fgetcsv($fh))) {
    $data[] = $fields;
    if (count($data) == 1000) {
        try {
            $sql = genInsertSql('master_sku_list', $columns, $data);
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
        $sql = genInsertSql('master_sku_list', $columns, $data);
        $db->execute($sql);
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE in ", number_format(microtime(true) - $starttime, 4), " seconds\n";
