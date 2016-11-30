<?php

class BTEInventoryUpdate
{
    protected $priority = 0;  // 0 to disable
    protected $orders;

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function run($argv = [])
    {
        echo '>> ', __CLASS__, EOL;
        $this->updateInventory();
    }

    protected function updateInventory()
    {
        echo count($this->orders), ' new orders', EOL;

        if (count($this->orders) > 0) {

            $accdb = $this->openAccessDB();

            foreach ($this->orders as $order) {
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
