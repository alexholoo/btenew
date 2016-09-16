<?php

require 'database.php';

$file = 'w:/out/purchasing/ca_order_notes.csv';
if (($fh = @fopen($file, 'rb')) === false) {
    echo "Failed to open file: $file\n";
    exit;
}

echo "loading $file\n";

fgetcsv($fh); // skip the first line

$count = 0;
while(($fields = fgetcsv($fh))) {

    $date         = $fields[0];
    $order_id     = $fields[1];
    $stock_status = $fields[2];
    $express      = $fields[3];
    $qty          = $fields[4];
    $supplier     = $fields[5];
    $supplier_sku = $fields[6];
    $mpn          = $fields[7];
    $supplier_no  = $fields[8];
    $notes        = $fields[9];
    $related_sku  = $fields[10];
    $dimension    = $fields[11];

    try {
        $success = $db->insertAsDict('ca_order_notes',
            array(
                'date'          => $date,
                'order_id'      => $order_id,
                'stock_status'  => $stock_status,
                'express'       => $express,
                'qty'           => $qty,
                'supplier'      => $supplier,
                'supplier_sku'  => $supplier_sku,
                'mpn'           => $mpn,
                'supplier_no'   => $supplier_no,
                'notes'         => $notes,
                'related_sku'   => $related_sku,
                'dimension'     => $dimension,
            )
        );

        if (!$success) {
            echo $order_id, EOL;
        }

        $count++;

    } catch (Exception $e) {
        //echo $e->getMessage(), EOL;
    }
}

fclose($fh);

echo "$count DONE\n";
