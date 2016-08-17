<?php

require 'database.php';

$starttime = microtime(true);

if (!($fh = @fopen('w:/data/master_sku_list.csv', 'rb'))) {
    echo 'Failed to open file: master_sku_list.csv';
    exit;
}

echo "loading master_sku_list.csv\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $sku  = $fields[0];
    $recommended_pn  = $fields[1];
    $syn_pn  = $fields[2];
    $syn_cost  = $fields[3];
    $syn_qty  = $fields[4];
    $td_pn  = $fields[5];
    $td_cost  = $fields[6];
    $td_qty  = $fields[7];
    $ing_pn  = $fields[8];
    $ing_cost  = $fields[9];
    $ing_qty  = $fields[10];
    $dh_pn  = $fields[11];
    $dh_cost  = $fields[12];
    $dh_qty  = $fields[13];
    $asi_pn  = $fields[14];
    $asi_cost  = $fields[15];
    $asi_qty  = $fields[16];
    $tak_pn  = $fields[17];
    $tak_cost  = $fields[18];
    $tak_qty  = $fields[19];
    $ep_pn  = $fields[20];
    $ep_cost  = $fields[21];
    $ep_qty  = $fields[22];
    $bte_pn  = $fields[23];
    $bte_cost  = $fields[24];
    $bte_qty  = $fields[25];
    $manufacturer  = $fields[26];
    $upc  = $fields[27];
    $mpn  = $fields[28];
    $map_usd  = $fields[29];
    $map_cad  = $fields[30];
    $width  = $fields[31];
    $length  = $fields[32];
    $depth  = $fields[33];
    $weight  = $fields[34];
    $ca_ebay_blocked  = $fields[35];
    $us_ebay_blocked  = $fields[36];
    $ca_newegg_blocked  = $fields[37];
    $us_newegg_blocked  = $fields[38];
    $us_amazon_blocked  = $fields[39];
    $ca_amazon_blocked  = $fields[40];
    $uk_amazon_blocked  = $fields[41];
    $jp_amazon_blocked  = $fields[42];
    $mx_amazon_blocked  = $fields[43];
    $note  = $fields[44];
    $name  = $fields[45];
    $best_cost  = $fields[46];
    $overall_qty  = $fields[47];

    try {
        $success = $db->insertAsDict('master_sku_list',
            compact(
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
            )
        );

        if (!$success) {
            echo $sku, EOL;
        }

        $count++;

    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE in ", number_format(microtime(true) - $starttime, 4), " seconds\n";
