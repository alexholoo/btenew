<?php

include 'classes/Job.php';

class OrderImportJob extends Job
{
    public function run($args = [])
    {
        $this->importOrders();
        $this->importDropship();
    }

    public function importOrders()
    {
        $file = 'all_mgn_orders.csv';
        $table = 'all_mgn_orders';

        if (!($fh = @fopen("E:/BTE/import/$file", 'rb'))) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        // Table columns
        $columns = [
            'channel',
            'date',
            'order_id',
            'mgn_order_id',
            'express',
            'buyer',
            'address',
            'city',
            'province',
            'postalcode',
            'country',
            'phone',
            'email',
            'skus_sold',
            'sku_price',
            'skus_qty',
            'shipping',
            'product_name',
        ];

        $data = [];
        while(($fields = fgetcsv($fh))) {
            $fields[5] = utf8_encode($fields[5]); // buyer
            $fields[6] = utf8_encode($fields[6]); // address
            $fields[7] = utf8_encode($fields[7]); // city
            $fields[8] = utf8_encode($fields[8]); // province

            $data[] = $fields;
        }

        fclose($fh);

        if (!$this->haveNewOrders($data)) {
            echo "No new orders, nothing imported.\n";
            return;
        }

        try {
            $sql = $this->genInsertSql($table, $columns, $data);
            $this->db->execute("TRUNCATE TABLE $table");
            $this->db->execute($sql);
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }

        $count = count($data);
        echo "$count orders imported\n";
    }

    public function importDropship()
    {
        $file  = 'ca_order_notes.csv';
        $table = 'ca_order_notes';

        if (($fh = @fopen("E:/BTE/import/$file", 'rb')) === false) {
            echo "Failed to open file: $file\n";
            return;
        }

        echo "Importing $file\n";

        fgetcsv($fh); // skip the first line

        // Table columns
        $columns = [
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
        ];

        $data = [];
        while(($fields = fgetcsv($fh))) {
            $data[] = $fields;
        }

        fclose($fh);

        if (!$this->haveNewDropshipOrders($data)) {
            echo "No new dropship orders, nothing imported.\n";
            return;
        }

        try {
            $sql = $this->genInsertSql($table, $columns, $data);
            $this->db->execute("TRUNCATE TABLE $table");
            $this->db->execute($sql);
        } catch (Exception $e) {
            echo $e->getMessage(), EOL;
        }

        $count = count($data);
        echo "$count dropship orders imported\n";
    }

    public function haveNewOrders($data)
    {
        foreach ($data as $order) {
            $orderId = $order[2];
            $sql = "SELECT order_id FROM all_mgn_orders WHERE order_id='$orderId'";
            $result = $this->db->fetchOne($sql);
            if (!$result) {
                return true;
            }
        }
        return false;
    }

    public function haveNewDropshipOrders($data)
    {
        foreach ($data as $order) {
            $orderId = $order[1];
            $sql = "SELECT order_id FROM ca_order_notes WHERE order_id='$orderId'";
            $result = $this->db->fetchOne($sql);
            if (!$result) {
                return true;
            }
        }
        return false;
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderImportJob();
$job->run($argv);
