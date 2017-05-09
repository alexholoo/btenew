<?php

include __DIR__ . '/../public/init.php';

class OverstockAddJob extends Job
{
    public function run($args = [])
    {
        $this->log('>> '. __CLASS__);

        $filename = $args[1];

        if (!file_exists($filename)) {
            echo "$filename not found\n";
            return;
        }

        $accdb = $this->openAccessDB();

        $fp = fopen($filename, 'r');
        $columns = fgetcsv($fp);

        while ($values = fgetcsv($fp)) {
            $fields = array_combine($columns, $values);
            $sku = $fields['sku'];

            $sql = "SELECT * FROM [overstock] WHERE [SKU Number]='$sku'";
            $row = $accdb->query($sql)->fetch();

            if ($row) {
                $totalQty = $row['Actual Quantity'] + $fields['qty'];
                $totalPrice = $row['Actual Quantity'] * $row['cost'] + $fields['cost'] * $fields['qty'];
                $newCost = round($totalPrice/$totalQty);

                $sql = "UPDATE [overstock] SET [Actual Quantity]=$totalQty, [cost]=$newCost"
                     . " WHERE [SKU Number]='$sku'";

                $ret = $accdb->exec($sql);

                if (!$ret && $accdb->errorCode() != '00000') {
                    $this->error(__METHOD__);
                    $this->error(print_r($accdb->errorInfo(), true));
                    $this->error($sql);
                }
            } else {
                $sql = $this->insertMssql("overstock", [
                    'SKU Number'      => $fields['sku'],
                    'Title'           => $fields['title'],
                    'cost'            => intval($fields['cost']),
                    'condition'       => $fields['condition'],
                    'Allocation'      => $fields['allocation'],
                    'Actual Quantity' => intval($fields['qty']),
                    'MPN'             => $fields['mpn'],
                    'note'            => $fields['note'],
                    'UPC Code'        => $fields['upc'],
                    'Weight(lbs)'     => floatval($fields['weight']),
                    'Reserved'        => '',
                   #'ID'              => '',
                ]);

                $ret = $accdb->exec($sql);
                if (!$ret && $accdb->errorCode() != '00000') {
                    $this->error(__METHOD__);
                    $this->error(print_r($accdb->errorInfo(), true));
                    $this->error($sql);
                }
            }
        }

        fclose($fp);

        Toolkit\File::backup($filename);
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

$job = new OverstockAddJob();
$job->run($argv);
