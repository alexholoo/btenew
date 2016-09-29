<?php

class OrderImportJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($arg = '')
    {
        $this->importOrders();
        $this->importDropship();
    }

    public function importOrders()
    {
        $file = 'E:/BTE/order/all_mgn_orders.csv';
        if (!($fh = @fopen($file, 'rb'))) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        $count = 0;
        while(($fields = fgetcsv($fh))) {

            $channel    = $fields[0];
            $date       = $fields[1];
            $order_id   = $fields[2];
            $mgn_order_id = $fields[3];
            $express    = $fields[4];
            $buyer      = $fields[5];
            $address    = $fields[6];
            $city       = $fields[7];
            $province   = $fields[8];
            $postalcode = $fields[9];
            $country    = $fields[10];
            $phone      = $fields[11];
            $email      = $fields[12];
            $skus_sold  = $fields[13];
            $sku_price  = $fields[14];
            $skus_qty   = $fields[15];
            $shipping   = $fields[16];
            $mgn_invoice_id = $fields[17];

            try {
                $this->db->insertAsDict('all_mgn_orders',
                    array(
                        'channel'   => $channel,
                        'date'      => $date,
                        'order_id'  => $order_id,
                        'mgn_order_id' => $mgn_order_id,
                        'express'   => $express,
                        'buyer'     => $buyer,
                        'address'   => $address,
                        'city'      => $city,
                        'province'  => $province,
                        'postalcode'=> $postalcode,
                        'country'   => $country,
                        'phone'     => $phone,
                        'email'     => $email,
                        'skus_sold' => $skus_sold,
                        'sku_price' => $sku_price,
                        'skus_qty'  => $skus_qty,
                        'shipping'  => $shipping,
                        'mgn_invoice_id' => $mgn_invoice_id,
                    )
                );

                $count++;

            } catch (Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }

        fclose($fh);

        echo "$count orders imported\n";
    }

    public function importDropship()
    {
        $file = 'E:/BTE/order/ca_order_notes.csv';
        if (($fh = @fopen($file, 'rb')) === false) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

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
                $this->db->insertAsDict('ca_order_notes',
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

                $count++;

            } catch (Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }

        fclose($fh);

        echo "$count dropship orders imported\n";
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderImportJob();
$job->run();
