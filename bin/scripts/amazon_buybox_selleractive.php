<?php

require 'database.php';

$file = 'E:/BTE/import/amazon-ca-buybox-report-selleractive.csv';
$table = 'amazon_ca_buybox_report_selleractive';
importFile($db, $file, $table);

$file = 'E:/BTE/import/amazon-us-buybox-report-selleractive.csv';
$table = 'amazon_us_buybox_report_selleractive';
importFile($db, $file, $table);

function importFile($db, $file, $table)
{
    if (!($fh = @fopen($file, 'rb'))) {
        echo "Failed to open file: $file\n";
        return;
    }

    echo "loading $file\n";

    fgetcsv($fh); // skip the first line

    $columns = [
       #'site',
       #'siteid',
        'condition',
        'buybox_owned',
        'buybox_price',
        'low_new_price',
        'low_used_price',
        'current_price',
        'shipping_price',
        'price_rank',
        'sales_rank',
        'sku',
        'title',
        'qty',
        'fulfilled_by'
    ];

    $db->execute("TRUNCATE TABLE $table");

    $count = 0;
    $items = [];

    while(($fields = fgetcsv($fh))) {
        try {
            unset($fields[0]);
            unset($fields[1]);

            $data = array_combine($columns, $fields);

           #unset($data['site']);
           #unset($data['siteid']);

            $items[] = $data;

            if (count($items) >= 2000) {
                $sql = genInsertSql($table, $columns, $items);
                $db->execute($sql);
                $items = [];
            }
            $count++;
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    if (count($items) > 0) {
        $sql = genInsertSql($table, $columns, $items);
        $db->execute($sql);
        $items = [];
    }

    fclose($fh);

    echo "$count items imported\n";
}
