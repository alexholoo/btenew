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

    $count = 0;
    while(($fields = fgetcsv($fh))) {

        $order_id = $fields[0];
        $order_item_id = $fields[1];
        $purchase_date = $fields[2];
        $payments_date = $fields[3];
        $buyer_email = $fields[4];
        $buyer_name = $fields[5];
        $buyer_phone_number = $fields[6];
        $sku = $fields[7];
        $product_name = $fields[8];
        $quantity_purchased = $fields[9];
        $currency = $fields[10];
        $item_price = $fields[11];
        $item_tax = $fields[12];
        $shipping_price = $fields[13];
        $shipping_tax = $fields[14];
        $ship_service_level = $fields[15];
        $recipient_name = $fields[16];
        $ship_address_1 = $fields[17];
        $ship_address_2 = $fields[18];
        $ship_address_3 = $fields[19];
        $ship_city = $fields[20];
        $ship_state = $fields[21];
        $ship_postal_code = $fields[22];
        $ship_country = $fields[23];
        $ship_phone_number = $fields[24];
        $item_promotion_discount = $fields[25];
        $item_promotion_id = $fields[26];
        $ship_promotion_discount = $fields[27];
        $ship_promotion_id = $fields[28];
        $delivery_start_date = $fields[29];
        $delivery_end_date = $fields[30];
        $delivery_time_zone = $fields[31];
        $delivery_instructions = $fields[32];
        $sales_channel = $fields[33];

        try {
            $success = $db->insertAsDict($name,
                array(
                    'order_id' => $order_id,
                    'order_item_id' => $order_item_id,
                    'purchase_date' => $purchase_date,
                    'payments_date' => $payments_date,
                    'buyer_email' => $buyer_email,
                    'buyer_name' => $buyer_name,
                    'buyer_phone_number' => $buyer_phone_number,
                    'sku' => $sku,
                    'product_name' => $product_name,
                    'quantity_purchased' => $quantity_purchased,
                    'currency' => $currency,
                    'item_price' => $item_price,
                    'item_tax' => $item_tax,
                    'shipping_price' => $shipping_price,
                    'shipping_tax' => $shipping_tax,
                    'ship_service_level' => $ship_service_level,
                    'recipient_name' => $recipient_name,
                    'ship_address_1' => $ship_address_1,
                    'ship_address_2' => $ship_address_2,
                    'ship_address_3' => $ship_address_3,
                    'ship_city' => $ship_city,
                    'ship_state' => $ship_state,
                    'ship_postal_code' => $ship_postal_code,
                    'ship_country' => $ship_country,
                    'ship_phone_number' => $ship_phone_number,
                    'item_promotion_discount' => $item_promotion_discount,
                    'item_promotion_id' => $item_promotion_id,
                    'ship_promotion_discount' => $ship_promotion_discount,
                    'ship_promotion_id' => $ship_promotion_id,
                    'delivery_start_date' => $delivery_start_date,
                    'delivery_end_date' => $delivery_end_date,
                    'delivery_time_zone' => $delivery_time_zone,
                    'delivery_instructions' => $delivery_instructions,
                    'sales_channel' => $sales_channel,
                )
            );

            $count++;

        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    fclose($fh);

    echo "$count orders imported\n";
}

importAmazonOrders($db, 'amazon_ca_order_report');
importAmazonOrders($db, 'amazon_us_order_report');
