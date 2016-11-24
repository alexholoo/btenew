<?php

class NeweggOrderImportToAccessJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        # W:\data\csv\newegg\canada_order\neweggcanada_master_orders.csv
        $neweggOrderReport = "E:/BTE/orders/newegg/neweggcanada_master_orders.csv";
        $this->importNeweggOrders($neweggOrderReport, 'NeweggCA');

        # w:\data\csv\newegg\us_order\neweggusa_master_orders.csv
       #$neweggOrderReport = "E:/BTE/orders/newegg/neweggusa_master_orders.csv";
       #$this->importNeweggOrders($neweggOrderReport, 'NeweggUS');
    }

    protected function importNeweggOrders($filename, $channel)
    {
        $today = date('Y-m-d');

        $stockStatus = ' ';
        $lastOrderNo = '';

        $dbname = "Z:/Purchasing/General Purchase.accdb";
        if (gethostname() != 'BTELENOVO') {
            $dbname = "C:/Users/BTE/Desktop/General Purchase.accdb";
        }
        $db = $this->openAccessDB($dbname);

        $orderFile = fopen($filename, 'r');
        $title = fgetcsv($orderFile);

        while (($fields = fgetcsv($orderFile)) != false) {

            $orderNo    = $fields[0];
            $date       = date('Y-m-d', strtotime($fields[1]));
            $sku        = $fields[16];
            $qty        = $fields[26];
            $shipMethod = $fields[15];

            if ($date != $today) {
                continue;
            }

            // check if the order already exported
            $sql = "SELECT * FROM Newegg WHERE [PO #]='$orderNo'";
            $result = $db->query($sql)->fetch();
            if ($result && $orderNo != $lastOrderNo) {
                echo "Skip $orderNo\n";
                continue;
            }

            $supplier = $this->getSupplier($sku);
            $ponum    = $this->getSku($sku);
            $xpress   = $this->isExpress($shipMethod);
            $mfrpn    = $this->getMfrPartNum($sku);

            #$data = array_combine($title, $fields);
            #print_r($data); break;

            echo "Importing $orderNo\n";

            $sql = "INSERT INTO Newegg (
                        [Work Date],
                        [Channel],
                        [PO #],
                        [Xpress],
                        [Stock Status],
                        [Qty],
                        [Supplier],
                        [Supplier SKU],
                        [Mfr #],
                        [Supplier #],
                        [Remarks],
                        [RelatedSKU],
                        [Dimension]
                    )
                    VALUES (
                        '$date',
                        '$channel',
                        '$orderNo',
                        $xpress,
                        '$stockStatus',
                        '$qty',
                        '$supplier',
                        '$ponum',
                        '$mfrpn',
                        '',
                        '',
                        '',
                        ''
                    )";

            $ret = $db->exec($sql);

            if (!$ret) {
                print_r($db->errorInfo());
            }

            $lastOrderNo = $orderNo;
        }

        fclose($orderFile);
    }

    protected function openAccessDB($dbname)
    {
        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);
        return $db;
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

    protected function isExpress($shipMethod)
    {
        # Expedited Shipping (3-5 business days)
        # One-Day Shipping(Next day)
        # Standard Shipping (5-7 business days)
        # Two-Day Shipping(2 business days)

        if (strpos($shipMethod, 'Standard Shipping') !== false) {
            return 0;
        }

        return 1;
    }

    protected function getMfrPartNum($sku)
    {
        return $this->di->get('productService')->getMpnFromSku($sku);
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggOrderImportToAccessJob();
$job->run($argv);
