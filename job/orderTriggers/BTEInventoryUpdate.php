<?php

class BTEInventoryUpdate extends Job
{
    protected $priority = 10;  // 0 to disable
    protected $orders;

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
        $this->log('>> '. __CLASS__);
        $this->updateInventory();
    }

    protected function updateInventory()
    {
        $this->log(count($this->orders). ' new orders');

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
                    $sql = "SELECT QtyOnHand FROM [bte-inventory-automated] WHERE [Part Number]='$sku'";
                    $row = $accdb->query($sql)->fetch();
                    if (!$row) {
                        $this->log("$date $channel $orderId $sku $qty Not Found");
                        continue; // if not found, skip it
                    }

                    $qtyOnHand = $row['QtyOnHand'];

                    $x = 0;
                    $change = 'No change';
                    if ($qtyOnHand > 0) {
                        $change = "-$qty";
                        $x = $qtyOnHand - $qty;
                        if ($x < 0) {
                            $x = 0;
                            $note = "-$qtyOnHand";
                        }
                    }

                    $this->log("$date $channel $orderId $sku $qty => $x");

                    $sql = "UPDATE [bte-inventory-automated] SET [QtyOnHand]=$x WHERE [Part Number]='$sku'";

                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        $this->log(print_r($accdb->errorInfo(), true));
                    }

                    // Mark the item as 'out of stock' by prefixing *** the part number
                    if ($x == 0) {
                        $sql = "UPDATE [bte-inventory-automated] SET [Part Number]='***$sku' WHERE [Part Number]='$sku'";
                        $ret = $accdb->exec($sql);
                        if (!$ret && $accdb->errorCode() != '00000') {
                            $this->log(print_r($accdb->errorInfo(), true));
                        }
                        $this->log("***$sku");
                    }

                    // log the change
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
                        $this->log(print_r($accdb->errorInfo(), true));
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
