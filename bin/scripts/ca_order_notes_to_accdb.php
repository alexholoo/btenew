<?php

$file = 'w:/out/purchasing/ca_order_notes.csv';
if (($fh = @fopen($file, 'rb')) === false) {
    echo "Failed to open file: $file\n";
    exit;
}

$title = fgetcsv($fh);

$dbname = "C:/Users/BTE/Desktop/ca_order_notes.accdb";
$db = openAccessDB($dbname);

$lastOrderId = '';
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

    $sql = "SELECT * FROM ca_order_notes WHERE [order_id]='$order_id'";
    $result = $db->query($sql)->fetch();
    if ($result && $order_id != $lastOrderId) {
        echo "Skip $order_id\n";
        continue;
    }

    echo "Importing $order_id\n";

    $sql = "INSERT INTO ca_order_notes (
                [date],
                [order_id],
                [Stock Status],
                [Xpress],
                [qty],
                [supplier],
                [supplier_sku],
                [MPN],
                [Supplier#],
                [notes],
                [related_sku],
                [dimension]
            )
            VALUES (
                '$date',
                '$order_id',
                '$stock_status',
                $express,
                $qty,
                '$supplier',
                '$supplier_sku',
                '$mpn',
                '$supplier_no',
                '$notes',
                '$related_sku',
                '$dimension'
            )";

    $ret = $db->exec($sql);

    if (!$ret) {
        print_r($db->errorInfo());
    }

    $lastOrderId = $order_id;
}

fclose($fh);

function openAccessDB($dbname)
{
    $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
    $db = new PDO($dsn);
    return $db;
}
