<?php

include 'classes/Job.php';

class AmazonCaOrderImportToAccessJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->importAmazonCAOrders();
    }

    protected function importAmazonCAOrders()
    {
        $orders = $this->getOrders();
        $accdb = $this->openAccessDB();

        $yesterday = date('Y-m-d', strtotime('Yesterday'));

        foreach ($orders as $order) {
            if (($date = $order['date']) < $yesterday) {
                continue;
            }

            $sku         = $this->getSku($order['sku']);
            $orderid     = $order['order_id'];
            $xpress      = $order['express'];
            $stockStatus = $order['stock_status'];
            $qty         = $order['qty'];
            $supplier    = $this->getSupplier($order['sku']);
            $ponum       = ' ';
            $mfrpn       = $this->getMfrPartNum($order['sku']);
            $notes       = $order['notes'];
            $dimension   = $order['dimension'];

            $sql = "SELECT * FROM Amazon_CA WHERE [order_id]='$orderid'";
            $result = $accdb->query($sql)->fetch();
            if ($result) {
                continue;
            }

            $sql = "INSERT INTO Amazon_CA (
                        [date],
                        [order_id],
                        [Stock Status],
                        [Xpress],
                        [qty],
                        [supplier],
                        [supplier_sku],
                        [MPN],
                        [Supplier#],
                        [notes],
                        [related_sku],
                        [dimension]
                    )
                    VALUES (
                        '$date',
                        '$orderid',
                        '$stockStatus',
                        '$xpress',
                        '$qty',
                        '$supplier',
                        '$sku',
                        '$mfrpn',
                        '$ponum',
                        '$notes',
                        '',
                        '$dimension'
                    )";

            $ret = $accdb->exec($sql);

            if (!$ret) {
                print_r($this->accdb->errorInfo());
            }

            $this->log($orderid);
        }
    }

    protected function openAccessDB()
    {
        $dbname = "Z:/Purchasing/General Purchase.accdb";

        if (gethostname() != 'BTELENOVO') {
            $dbname = "C:/Users/BTE/Desktop/General Purchase.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }

    protected function getOrders()
    {
        $file = 'E:/BTE/import/ca_order_notes.csv';

        if (gethostname() != 'BTELENOVO') {
            $file = 'w:/out/purchasing/ca_order_notes.csv';
        }

        if (($fh = @fopen($file, 'rb')) === false) {
            $this->log("Failed to open file: $file");
            return [];
        }

        $this->log("Loading $file");

        fgetcsv($fh); // skip the first line

        $orders = [];
        while(($fields = fgetcsv($fh))) {
            $orders[] = [
                'date'         => $fields[0],
                'order_id'     => $fields[1],
                'stock_status' => $fields[2],
                'express'      => $fields[3],
                'qty'          => $fields[4],
                'supplier'     => $fields[5],
                'sku'          => $fields[6],
                'mpn'          => $fields[7],
                'supplier_no'  => $fields[8],
                'notes'        => $fields[9],
                'related_sku'  => $fields[10],
                'dimension'    => $fields[11],
            ];
        }

        fclose($fh);

        return $orders;
    }

    protected function getSku($sku)
    {
        $parts = explode('-', $sku);
        array_shift($parts);
        return implode('-', $parts);
    }

    protected function getSupplier($sku)
    {
        $names = [
            'AS'  => 'ASI',
            'SYN' => 'Synnex',
            'ING' => 'Ingram Micro',
            'EP'  => 'Eprom',
            'TD'  => 'Techdata',
            'TAK' => 'Taknology',
            'SP'  => 'Supercom',
        ];

        $parts = explode('-', $sku);
        $prefix = $parts[0];

        return isset($names[$prefix]) ? $names[$prefix] : $prefix;
    }

    protected function getMfrPartNum($sku)
    {
        return $this->di->get('productService')->getMpnFromSku($sku);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonCaOrderImportToAccessJob();
$job->run($argv);