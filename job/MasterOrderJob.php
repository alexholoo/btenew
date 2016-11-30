<?php

class MasterOrderJob
{
    protected $orders;
    protected $newOrders = [];

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        $this->getOrders();
        $this->importMaster();

        if (!in_array('notrigger', $argv)) {
            $this->newOrderTrigger();
        }
    }

    protected function getOrders()
    {
        $this->orders = [];

        $filename = 'w:/out/shipping/all_mgn_orders.csv';
        if (gethostname() == 'BTELENOVO') {
            $filename = 'E:/BTE/import/all_mgn_orders.csv';
        }

        if (!($handle = @fopen($filename, 'rb'))) {
            echo "Failed to open file: $filename\n";
            return;
        }

        echo "Loading $filename\n";

        $title = fgetcsv($handle);

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
             'sku',
             'price',
             'qty',
             'shipping',
             'product_name',
        ];

        while (($fields = fgetcsv($handle))) {
            $orderId = $fields[2];
            if (count($columns) != count($fields)) {
                echo 'Error: ', $orderId, EOL;
            }
            $order = array_combine($columns, $fields);
            $this->orders[$orderId][] = $order;
        }

        fclose($handle);
    }

    protected function importMaster()
    {
        foreach ($this->orders as $orderId => $orders) {
            if (!$this->orderExists($orderId)) {
                echo "Importing order $orderId\n";
                $this->importOrder($orders[0]);
                $this->importOrderShippingAddress($orders[0]);
                $this->importOrderItems($orders);
            }
        }
    }

    protected function importOrder($order)
    {
        try {
            $this->db->insertAsDict('master_order', [
                'channel'   => $order['channel'],
                'date'      => $order['date'],
                'order_id'  => $order['order_id'],
                'express'   => $order['express'],
                'shipping'  => $order['shipping'],
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    protected function importOrderItems($orders)
    {
        foreach ($orders as $order) {
            try {
                $this->db->insertAsDict('master_order_item', [
                    'order_id'      => $order['order_id'],
                    'sku'           => $order['sku'],
                    'price'         => $order['price'],
                    'qty'           => $order['qty'],
                    'product_name'  => $order['product_name'],
                ]);

                $order['order_item_id'] = $this->db->lastInsertId();
                $this->importOrderStatus($order);

                $this->newOrders[] = $order;
            } catch (\Exception $e) {
                //echo $e->getMessage(), EOL;
            }
        }
    }

    protected function importOrderShippingAddress($order)
    {
        try {
            $this->db->insertAsDict('master_order_shipping_address', [
                'date'       => $order['date'],
                'order_id'   => $order['order_id'],
                'buyer'      => $order['buyer'],
                'address'    => $order['address'],
                'city'       => $order['city'],
                'province'   => $order['province'],
                'postalcode' => $order['postalcode'],
                'country'    => $order['country'],
                'phone'      => $order['phone'],
                'email'      => $order['email'],
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    protected function importOrderStatus($order)
    {
        try {
            $this->db->insertAsDict('master_order_status', [
                'order_item_id' => $order['order_item_id'],
                'date'          => $order['date'],
                'channel'       => $order['channel'],
                'order_id'      => $order['order_id'],
                'stock_status'  => '',
                'supplier'      => '',
                'supplier_sku'  => '',
                'mfrpn'         => '',
                'ponum'         => '',
                'invoice'       => '',
                'ship_method'   => '',
                'trackingnum'   => '',
                'remarks'       => '',
                'flag'          => '',
                'related_sku'   => '',
                'dimension'     => '',
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    protected function newOrderTrigger()
    {
        echo count($this->newOrders), ' new orders', EOL;

        if (count($this->newOrders) > 0) {
            $accdb = $this->openAccessDB();
            foreach ($this->newOrders as $order) {
                $date    = $order['date'];
                $channel = $order['channel'];
                $orderId = $order['order_id'];
                $sku     = $order['sku'];
                $qty     = $order['qty'];

                $parts = explode('-', $sku);
                $supplier = $parts[0];

                if ($supplier == 'BTE') {
                    echo "$date $channel $orderId $sku $qty", EOL;

                    $sql = "SELECT QtyOnHand FROM [bte-inventory] WHERE [Part Number]='$sku'";
                    $row = $accdb->query($sql)->fetch();

                    $qtyOnHand = 0;
                    if ($row) {
                        $qtyOnHand = $row['QtyOnHand'];
                    }

                    $x = 0;
                    $change = 'No change';
                    if ($qtyOnHand > 0) {
                        $change = "Reduced $qty";
                        $x = $qtyOnHand - $qty;
                        if ($x < 0) {
                            $x = 0;
                            $note = "Reduced $qtyOnHand";
                        }
                    }

                    $sql = "UPDATE [bte-inventory] SET [QtyOnHand]=$x WHERE [Part Number]='$sku'";

                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        print_r($accdb->errorInfo());
                    }

                    $sql = "INSERT INTO [bte-inventory-change] (
                                [OrderDate],
                                [Channel],
                                [OrderNo],
                                [SKU],
                                [Qty],
                                [Change]
                            )
                            VALUES (
                                '$date',
                                '$channel',
                                '$orderId',
                                '$sku',
                                $qty,
                                '$change'
                            )";

                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        print_r($accdb->errorInfo());
                    }
                }
            }
        }
    }

    private function orderExists($orderId)
    {
        $sql = "SELECT order_id FROM master_order WHERE order_id='$orderId'";
        return $this->db->fetchOne($sql);
    }

    protected function openAccessDB()
    {
        $dbname = "z:/BTE-Price-List/bte-inventory.accdb";

        if (gethostname() != 'BTELENOVO') {
            $dbname = "C:/Users/BTE/Desktop/bte-inventory.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }
}

include __DIR__ . '/../public/init.php';

$job = new MasterOrderJob();
$job->run($argv);
