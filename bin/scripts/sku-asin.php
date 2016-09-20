<?php

require 'database.php';

$file = 'w:/data/csv/amazon/amazonCAD_listings.csv';
importFile($file, $db);

$file = 'w:/data/csv/amazon/amazon_US_listings.csv';
importFile($file, $db);

function importFile($file, $db, $skipFirstLine=false)
{
    if (!($fh = @fopen($file, 'rb'))) {
        echo "Failed to open file: $file";
        return;
    }

    $starttime = microtime(true);

    echo "importing $file\n";

    if ($skipFirstLine) {
        fgetcsv($fh); // skip the first line
    }

    $count = 0;
    $data = [];

    while(($fields = fgetcsv($fh))) {
        $count++;

        $sku = $fields[3];
        $asin = $fields[16];

        $data[] = [$sku, $asin];

        if (count($data) > 1000) {
            saveToDb($db, $data);
            $data = [];
        }
    }

    if (count($data)) {
        saveToDb($db, $data);
    }

    fclose($fh);

    echo "$count rows imported in ", number_format(microtime(true) - $starttime, 4), " seconds\n";
}

function saveToDb($db, $data)
{
    $columns = [ 'sku', 'asin' ];
    try {
        $sql = genInsertSql('sku_asin_map', $columns, $data);
        $db->execute($sql);
    } catch (Exception $e) {
        echo $e->getMessage(), EOL;
    }
}
