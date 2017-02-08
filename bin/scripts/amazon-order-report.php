<?php

require __DIR__ . '/database.php';

function importAmazonOrders($db, $name)
{
    $file = "e:/BTE/orders/amazon/$name.csv";
    if (!($fh = @fopen($file, 'rb'))) {
        echo "Failed to open file: $file\n";
        exit;
    }

    echo "loading $file\n";

    fgetcsv($fh); // skip the first line

    $columns = [
        'order_id',
        'order_item_id',
        'purchase_date',
        'payments_date',
        'buyer_email',
        'buyer_name',
        'buyer_phone_number',
        'sku',
        'product_name',
        'quantity_purchased',
        'currency',
        'item_price',
        'item_tax',
        'shipping_price',
        'shipping_tax',
        'ship_service_level',
        'recipient_name',
        'ship_address_1',
        'ship_address_2',
        'ship_address_3',
        'ship_city',
        'ship_state',
        'ship_postal_code',
        'ship_country',
        'ship_phone_number',
        'item_promotion_discount',
        'item_promotion_id',
        'ship_promotion_discount',
        'ship_promotion_id',
        'delivery_start_date',
        'delivery_end_date',
        'delivery_time_zone',
        'delivery_instructions',
        'sales_channel',
    ];

    $count = 0;
    while(($fields = fgetcsv($fh))) {
        try {
            $data = array_combine($columns, $fields);
            $db->insertAsDict($name, $data);
            $count++;
        } catch (Exception $e) {
            // multi-items-order issue
            echo $e->getMessage(), EOL;
        }
    }

    fclose($fh);

    echo "$count orders imported\n";
}

importAmazonOrders($db, 'amazon_ca_order_report');
importAmazonOrders($db, 'amazon_us_order_report');
