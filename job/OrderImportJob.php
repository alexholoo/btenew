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

            $channel    = $fields[0];
            $date       = $fields[1];
            $order_id   = $fields[2];
            $mgn_order_id = $fields[3];
            $express    = $fields[4];
            $buyer      = utf8_encode($fields[5]);
            $address    = utf8_encode($fields[6]);
            $city       = utf8_encode($fields[7]);
            $province   = utf8_encode($fields[8]);
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
        $file = 'E:/BTE/orders/ca_order_notes.csv';
        if (($fh = @fopen($file, 'rb')) === false) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        // Table columns
        $columns = array(
            'date',
            'order_id',
            'stock_status',
            'express',
            'qty',
            'supplier',
            'supplier_sku',
            'mpn',
            'supplier_no',
            'notes',
            'related_sku',
            'dimension',
        );

        $count = 0;
        while(($fields = fgetcsv($fh))) {
            $data = array_combine($columns, $fields);

            $sql = $this->genInsertSql('ca_order_notes', $data);

            try {
                $this->db->execute($sql);
                $count++;
            } catch (Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }

        fclose($fh);

        echo "$count dropship orders imported\n";
    }

    protected function genInsertSql($table, $data)
    {
        $columns = array_keys($data);
        $columnList = '`' . implode('`, `', $columns) . '`';

        $query = "INSERT INTO `$table` ($columnList) VALUES\n";

        $values = array();

        foreach($data as &$val) {
            $val = addslashes($val);
        }
        $values[] = "('" . implode("', '", $data). "')";

        $update = implode(', ',
            array_map(function($name) {
                return "`$name`=VALUES(`$name`)";
            }, $columns)
        );

        return $query . implode(",\n", $values) . "\nON DUPLICATE KEY UPDATE $update";
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderImportJob();
$job->run();
