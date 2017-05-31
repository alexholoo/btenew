<?php

class OverstockAccessUpdate extends OrderTrigger
{
    protected $priority = 20;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->updateOverstock();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function updateOverstock()
    {
        if (count($this->orders) > 0) {

            $accdb = $this->openAccessDB();

            foreach ($this->orders as $key => $order) {
                $date    = $order['date'];
                $channel = $order['channel'];
                $orderId = $order['order_id'];
                $skuOrig = $order['sku'];
                $qty     = $order['qty'];

                $sku = $this->findSku($accdb, $skuOrig);
                if (!$sku) {
                    $this->log("$channel $orderId $skuOrig $qty Not overstocked");
                    continue; // skip it if not found
                }

                if ($skuOrig != $sku) {
                    $this->log("$orderId $skuOrig ==>> $sku in overstock");
                    // let other orderTriggers know alternative sku used
                    $this->orders[$key]['altSku'] = $sku;
                }

                if ($qty >= 10) {
                    $this->error(__METHOD__ . " $orderId $qty, qty too big, please check");
                }

                $sql = "SELECT * FROM [overstock] WHERE [SKU Number]='$sku'";
                $row = $accdb->query($sql)->fetch();
                if (!$row) {
                    // This will not happen
                    $this->log("$channel $orderId $sku $qty Not overstocked");
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
                        $change = "-$qtyOnHand oversold";
                    }
                }

                $this->log("$channel $orderId $sku $qty => $x");

                $sql = "UPDATE [overstock] SET [Actual Quantity]=$x, [Reserved]='' WHERE [SKU Number]='$sku'";

                $ret = $accdb->exec($sql);
                if (!$ret && $accdb->errorCode() != '00000') {
                    $this->error(__METHOD__);
                    $this->error(print_r($accdb->errorInfo(), true));
                    $this->error($sql);
                }

                // let other orderTriggers know it's in-stock
                $this->orders[$key]['overstock-changed'] = true;

                // Mark the item as 'out of stock' by prefixing *** the part number
                if ($x == 0) {
                    $today = date('Y-m-d');
                    $soldout = "Sold out on $today";
                    $sql = "UPDATE [overstock] SET [SKU Number]='***$sku', [Reserved]='$soldout' WHERE [SKU Number]='$sku'";
                    $ret = $accdb->exec($sql);
                    if (!$ret && $accdb->errorCode() != '00000') {
                        $this->error(__METHOD__);
                        $this->error(print_r($accdb->errorInfo(), true));
                        $this->error($sql);
                    }
                    $this->log("***$sku");
                }

                $SKUNumber      = $row['SKU Number'];
                $Title          = $row['Title'];
                $cost           = $row['cost'] ? floatval($row['cost']) : NULL;
                $condition      = $row['condition'];
                $Allocation     = $row['Allocation'];
                $ActualQuantity = $x; //$row['Actual Quantity'] ?: 'NULL';
                $MPN            = $row['MPN'];
                $note           = $row['note'];
                $UPCCode        = $row['UPC Code'];
                $Weight         = $row['Weight(lbs)'] ? floatval($row['Weight(lbs)']) : NULL;
                $Reserved       = NULL; //$row['Reserved'] ?: NULL;

                // log the change
                $sql = $this->insertMssql("overstock-change", [
                    'OrderDate'       => $date,
                    'Channel'         => $channel,
                    'OrderNo'         => $orderId,
                    'Change'          => $change,
                    'SKU Number'      => $SKUNumber,
                    'Title'           => $Title,
                    'cost'            => $cost,
                    'condition'       => $condition,
                    'Allocation'      => $Allocation,
                    'Actual Quantity' => $ActualQuantity,
                    'MPN'             => $MPN,
                    'note'            => $note,
                    'UPC Code'        => $UPCCode,
                    'Weight(lbs)'     => $Weight,
                    'Reserved'        => $Reserved, // number!
                ]);

                $ret = $accdb->exec($sql);
                if (!$ret && $accdb->errorCode() != '00000') {
                    $this->error(__METHOD__);
                    $this->error(print_r($accdb->errorInfo(), true));
                    $this->error($sql);
                }
            }
        }

       #$this->log(count($this->orders). ' new orders');
    }

    protected function findSku($accdb, $sku)
    {
        // first, check the given sku in overstock
        $sql = "SELECT * FROM [overstock] WHERE [SKU Number]='$sku'";
        $row = $accdb->query($sql)->fetch();
        if ($row) {
            return $sku;
        }

        // if not found, then check the skus in same group
        $list = $this->di->get('skuService')->getAltSkus($sku);

        if (count($list) == 0) {
           #$this->error(__METHOD__.": no alternative sku found for $sku");
            return false;
        }

        $found = [];

        foreach ($list as $pn) {
            $sql = "SELECT * FROM [overstock] WHERE [SKU Number]='$pn'";
            $row = $accdb->query($sql)->fetch();
            if ($row) {
                $found[] = $pn;
            }
        }

        if (count($found) == 0) {
            return false;
        }

        if (count($found) > 1) {
            $this->error("Duplicated SKUs in overstock: ".implode(' / ', $found));
        }

        return $found[0];
    }

    protected function openAccessDB()
    {
        $dbname = "z:/BTE-Price-List/bte-dataprocess-files.accdb";

        if (!IS_PROD) {
            $dbname = "C:/Users/BTE/Desktop/bte-dataprocess-files.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }
}
