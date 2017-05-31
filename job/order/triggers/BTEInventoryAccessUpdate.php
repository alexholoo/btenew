<?php

class BTEInventoryAccessUpdate extends OrderTrigger
{
    protected $priority = 10;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->updateInventory();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
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
                        $this->error(__METHOD__);
                        $this->error(print_r($accdb->errorInfo(), true));
                        $this->error($sql);
                    }

                    // Mark the item as 'out of stock' by prefixing *** the part number
                    if ($x == 0) {
                        $sql = "UPDATE [bte-inventory] SET [Part Number]='***$sku' WHERE [Part Number]='$sku'";
                        $ret = $accdb->exec($sql);
                        if (!$ret && $accdb->errorCode() != '00000') {
                            $this->error(__METHOD__);
                            $this->error(print_r($accdb->errorInfo(), true));
                            $this->error($sql);
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
                    $Weight        = $row['Weight(lbs)'] ?: NULL;
                    $UPCCode       = $row['UPC Code'];
                    $MfrName       = $row['Mfr_Name'];
                    $MPN           = $row['MPN'];
                    $Length        = $row['Length(inch)'] ?: NULL;
                    $Width         = $row['Width(inch)'] ?: NULL;
                    $Depth         = $row['Depth(inch)'] ?: NULL;
                    $PurchasePrice = $row['PurchasePrice'];
                    $total         = $row['total'];

                    // log the change
                    $sql = $this->insertMssql("bte-inventory-change", [
                        'OrderDate'      => $date,
                        'Channel'        => $channel,
                        'OrderNo'        => $orderId,
                        'Change'         => $change,
                        'Part Number'    => $PartNumber,
                        'Title'          => $Title,
                        'Selling Cost'   => $SellingCost,
                        'Type'           => $Type,
                        'FBA_allocation' => $FbaAllocation,
                        'Notes'          => $Notes,
                        'Condition'      => $Condition,
                        'QtyOnHand'      => $QtyOnHand,
                        'Weight(lbs)'    => $Weight,
                        'UPC Code'       => $UPCCode,
                        'Mfr_Name'       => $MfrName,
                        'MPN'            => $MPN,
                        'Length(inch)'   => $Length,
                        'Width(inch)'    => $Width,
                        'Depth(inch)'    => $Depth,
                        'PurchasePrice'  => $PurchasePrice,
                        'total'          => $total,
                    ]);

                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        $this->error(__METHOD__);
                        $this->error(print_r($accdb->errorInfo(), true));
                        $this->error($sql);
                    }
                }
            }
        }

       #$this->log(count($this->orders). ' new orders');
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
