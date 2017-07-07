<?php

include __DIR__ . '/../public/init.php';

class OverstockDeductFbaJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        if (!isset($args[1])) {
            echo "Missing filename\n";
            return;
        }

        $filename = $args[1];
        $items = $this->loadItems($filename);
        $this->deductOverstock($items);
    }

    protected function loadItems($filename)
    {
        if (!file_exists($filename)) {
            echo "$filename not found\n";
            return [];
        }

        $fp = fopen($filename, 'r');
        fgetcsv($fp);

        $columns = [ 'mpn', 'qty' ];

        $items = [];
        while ($values = fgetcsv($fp, 0, "\t")) {
            $fields = array_combine($columns, $values);
            $items[] = $fields;
        }

        fclose($fp);

        return $items;
    }

    protected function deductOverstock($items)
    {
        //TODO: some code need to be moved to overstockService
        //$overstockService = $this->di->get('overstockService');

        $accdb = $this->openAccessDB();
        foreach ($items as $item) {
            $this->deductItem($accdb, $item);
        }
    }

    protected function deductItem($accdb, $item)
    {
        $fbasku = $item['mpn'];
        $qty = $item['qty'];

        $mpn = substr($fbasku, 8); // remove BTE-FBA-
        $skus = $this->findSkus($mpn);
        foreach ($skus as $sku) {
            $sql = "SELECT * FROM [overstock] WHERE [SKU Number]='$sku'";
            $row = $accdb->query($sql)->fetch();

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

            $sql = "UPDATE [overstock] SET [Actual Quantity]=$x, [Reserved]='' WHERE [SKU Number]='$sku'";

            $ret = $accdb->exec($sql);
            if (!$ret && $accdb->errorCode() != '00000') {
                $this->error(__METHOD__);
                $this->error(print_r($accdb->errorInfo(), true));
                $this->error($sql);
            }

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
                'Channel'         => 'FBA',
                'OrderNo'         => $fbasku,
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

    protected function findSkus($mpn)
    {
        $sql = "SELECT * FROM sku_mpn_map WHERE mpn='$mpn'";
        $rows = $this->db->fetchAll($sql);
        return array_column($rows, 'sku');
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

$job = new OverstockDeductFbaJob();
$job->run($argv);
