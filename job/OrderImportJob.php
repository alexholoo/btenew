<?php

use App\Models\Orders;
#se App\Models\OrderNotes;

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
            $order = Orders::findFirst("orderId='$order_id'");
            if (!$order) {
                $order = new Orders();
                $order->channel    = $fields[0];
                $order->date       = $fields[1];
                $order->orderId    = $fields[2];
                $order->mgnOrderId = $fields[3];
                $order->express    = $fields[4];
                $order->buyer      = $fields[5];
                $order->address    = $fields[6];
                $order->city       = $fields[7];
                $order->province   = $fields[8];
                $order->postalcode = $fields[9];
                $order->country    = $fields[10];
                $order->phone      = $fields[11];
                $order->email      = $fields[12];
                $order->sku        = $fields[13];
                $order->price      = $fields[14];
                $order->qty        = $fields[15];
                $order->shipping   = $fields[16];
                $order->mgnInvoiceId = $fields[17];

                if ($order->save() === false) {
                    $messages = $order->getMessages();
                    foreach ($messages as $message) {
                        echo $order_id, ': ', $message, "\n";
                    }
                } else {
                    $count++;
                }
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
