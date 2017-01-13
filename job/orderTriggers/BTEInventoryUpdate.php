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
        if (time() < strtotime('2017-01-10 00:00:00')) {
            $this->log('Coming soon '.__CLASS__);
            return;
        }

        $this->log('>> '. __CLASS__);
        $this->updateInventory();
    }

    protected function updateInventory()
    {
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
                    $sql = "SELECT * FROM [bte-inventory] WHERE [Part Number]='$sku'";
                    $row = $accdb->query($sql)->fetch();
                    if (!$row) {
                        $this->log("$date $channel $orderId $sku $qty Not Found");
                        continue; // if not found, skip it
                    }

                    if (strtolower(trim($row['Type'])) != 'self') {
                        $this->log("$date $channel $orderId $sku $qty Type Not 'self'");
                        continue;
                    }

                    $qtyOnHand = $row['QtyOnHand'];

                    $x = 0;
                    $change = 'No change';
                    if ($qtyOnHand > 0) {
                        $change = "-$qty";
                        $x = $qtyOnHand - $qty;
                        if ($x < 0) {
                            $x = 0;
                            $change = "-$qtyOnHand oversold";
                        }
                    }

                    $this->log("$date $channel $orderId $sku $qty => $x");

                    $sql = "UPDATE [bte-inventory] SET [QtyOnHand]=$x WHERE [Part Number]='$sku'";

                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        $this->log(print_r($accdb->errorInfo(), true));
                    }

                    // Mark the item as 'out of stock' by prefixing *** the part number
                    if ($x == 0) {
                        $sql = "UPDATE [bte-inventory] SET [Part Number]='***$sku' WHERE [Part Number]='$sku'";
                        $ret = $accdb->exec($sql);
                        if (!$ret && $accdb->errorCode() != '00000') {
                            $this->log(print_r($accdb->errorInfo(), true));
                        }
                        $this->log("***$sku");
                    }

                    $PartNumber    = $row['Part Number'];
                    $Title         = $row['Title ']; // notice the trailing space
                    $SellingCost   = $row['Selling Cost'];
                    $Type          = $row['Type'];
                    $FbaAllocation = $row['FBA_allocation'];
                    $Notes         = $row['Notes'];
                    $Condition     = $row['Condition'];
                    $QtyOnHand     = $x; //$row['QtyOnHand'];
                    $Weight        = $row['Weight(lbs)'] ?: 'NULL';
                    $UPCCode       = $row['UPC Code'];
                    $MfrName       = $row['Mfr_Name'];
                    $MPN           = $row['MPN'];
                    $Length        = $row['Length(inch)'] ?: 'NULL';
                    $Width         = $row['Width(inch)'] ?: 'NULL';
                    $Depth         = $row['Depth(inch)'] ?: 'NULL';
                    $PurchasePrice = $row['PurchasePrice'];
                    $total         = $row['total'];

                    // log the change
                    $sql = "INSERT INTO [bte-inventory-change] (
                                [OrderDate],
                                [Channel],
                                [OrderNo],
                                [Change],
                                [Part Number],
                                [Title],
                                [Selling Cost],
                                [Type],
                                [FBA_allocation],
                                [Notes],
                                [Condition],
                                [QtyOnHand],
                                [Weight(lbs)],
                                [UPC Code],
                                [Mfr_Name],
                                [MPN],
                                [Length(inch)],
                                [Width(inch)],
                                [Depth(inch)],
                                [PurchasePrice],
                                [total]
                            )
                            VALUES (
                                '$date',
                                '$channel',
                                '$orderId',
                                '$change',
                                '$PartNumber',
                                '$Title',
                                 $SellingCost,
                                '$Type',
                                '$FbaAllocation',
                                '$Notes',
                                '$Condition',
                                 $QtyOnHand,
                                 $Weight,
                                '$UPCCode',
                                '$MfrName',
                                '$MPN',
                                 $Length,
                                 $Width,
                                 $Depth,
                                '$PurchasePrice',
                                '$total'
                            )";

                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        $this->log(print_r($accdb->errorInfo(), true));
                    }
                }
            }
        }

        $this->log(count($this->orders). ' new orders');
    }

    protected function openAccessDB()
    {
        $dbname = "z:/BTE-Price-List/bte-inventory.accdb";

        if (!IS_PROD) {
            $dbname = "C:/Users/BTE/Desktop/bte-inventory.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }
}
