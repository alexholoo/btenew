<?php

class OverstockUpdate extends Job
{
    protected $priority = 20;  // 0 to disable
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

        $this->updateOverstock();
    }

    protected function updateOverstock()
    {
        if (count($this->orders) > 0) {

            $accdb = $this->openAccessDB();

            foreach ($this->orders as $order) {
                $date    = $order['date'];
                $channel = $order['channel'];
                $orderId = $order['order_id'];
                $sku     = $order['sku'];
                $qty     = $order['qty'];

                $sql = "SELECT [Actual Quantity] FROM [overstock-automated] WHERE [SKU Number]='$sku'";
                $row = $accdb->query($sql)->fetch();
                if (!$row) {
                    $this->log("$date $channel $orderId $sku $qty Not Found");
                    continue; // skip it if not found
                }

                $qtyOnHand = $row['Actual Quantity'];

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

                $sql = "UPDATE [overstock-automated] SET [Actual Quantity]=$x WHERE [SKU Number]='$sku'";

                $ret = $accdb->exec($sql);
                if (!$ret && $accdb->errorCode() != '00000') {
                    $this->log(print_r($accdb->errorInfo(), true));
                }

                // Mark the item as 'out of stock' by prefixing *** the part number
                if ($x == 0) {
                    $sql = "UPDATE [overstock-automated] SET [SKU Number]='***$sku' WHERE [SKU Number]='$sku'";
                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        $this->log(print_r($accdb->errorInfo(), true));
                    }
                    $this->log("***$sku");
                }

                // log the change
                $sql = "INSERT INTO [overstock-change] (
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

        $this->log(count($this->orders). ' new orders');
    }

    protected function openAccessDB()
    {
        $dbname = "z:/BTE-Price-List/bte-dataprocess-files.accdb";

        if (gethostname() != 'BTELENOVO') {
            $dbname = "C:/Users/BTE/Desktop/bte-dataprocess-files.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }
}
