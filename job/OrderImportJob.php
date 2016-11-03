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
        $file = 'E:/BTE/orders/all_mgn_orders.csv';
        if (!($fh = @fopen($file, 'rb'))) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        $count = 0;
        while(($fields = fgetcsv($fh))) {

            $order_id = $fields[2];

            if ($this->orderExists($order_id)) {
                continue;
            }

            try {
                $this->db->insertAsDict('all_mgn_orders',
                    array(
                        'channel'        => $fields[0],
                        'date'           => $fields[1],
                        'order_id'       => $fields[2],
                        'mgn_order_id'   => $fields[3],
                        'express'        => $fields[4],
                        'buyer'          => $fields[5],
                        'address'        => $fields[6],
                        'city'           => $fields[7],
                        'province'       => $fields[8],
                        'postalcode'     => $fields[9],
                        'country'        => $fields[10],
                        'phone'          => $fields[11],
                        'email'          => $fields[12],
                        'skus_sold'      => $fields[13],
                        'sku_price'      => $fields[14],
                        'skus_qty'       => $fields[15],
                        'shipping'       => $fields[16],
                        'mgn_invoice_id' => $fields[17],
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

    public function importDropship()
    {
        $file = 'E:/BTE/orders/ca_order_notes.csv';
        if (($fh = @fopen($file, 'rb')) === false) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        $count = 0;
        while(($fields = fgetcsv($fh))) {
            $order_id = $fields[1];

            if ($this->dropshipExists($order_id)) {
                continue;
            }

            try {
                $this->db->insertAsDict('ca_order_notes',
                    array(
                        'date'          => $fields[0],
                        'order_id'      => $fields[1],
                        'stock_status'  => $fields[2],
                        'express'       => $fields[3],
                        'qty'           => $fields[4],
                        'supplier'      => $fields[5],
                        'supplier_sku'  => $fields[6],
                        'mpn'           => $fields[7],
                        'supplier_no'   => $fields[8],
                        'notes'         => $fields[9],
                        'related_sku'   => $fields[10],
                        'dimension'     => $fields[11],
                    )
                );

                $count++;

            } catch (Exception $e) {
                echo $e->getMessage(), EOL;
            }
        }

        fclose($fh);

        echo "$count dropship orders imported\n";
    }

    protected function orderExists($orderId)
    {
        $sql = "SELECT * FROM all_mgn_orders WHERE order_id='$orderId'";
        return (bool)$this->db->fetchOne($sql);
    }

    protected function dropshipExists($orderId)
    {
        $sql = "SELECT * FROM ca_order_notes WHERE order_id='$orderId'";
        return (bool)$this->db->fetchOne($sql);
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderImportJob();
$job->run();
