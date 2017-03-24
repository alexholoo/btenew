<?php

include 'classes/Job.php';

class NeweggOrderImportToAccessJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        /**
         * Before this job is running, the files
         *
         *    w:\data\csv\newegg\canada_order\neweggcanada_master_orders.csv
         *    w:\data\csv\newegg\us_order\neweggusa_master_orders.csv
         *
         * have been copyied to
         *
         *    E:/BTE/orders/newegg/neweggcanada_master_orders.csv
         *    E:/BTE/orders/newegg/neweggusa_master_orders.csv
         */

        $neweggOrderReport = "E:/BTE/orders/newegg/neweggcanada_master_orders.csv";
        $this->channel = 'NeweggCA';
        $this->importNeweggOrders($neweggOrderReport, 'Newegg');

        $neweggOrderReport = "E:/BTE/orders/newegg/neweggusa_master_orders.csv";
        $this->channel = 'NeweggUS';
        $this->importNeweggOrders($neweggOrderReport, 'NeweggUSA');
    }

    protected function importNeweggOrders($filename, $table)
    {
        // start from yesterday to avoid midnight issue (UTC timezone)
        $start = date('Y-m-d', strtotime('Yesterday'));

        $channel = $this->channel;

        $stockStatus = ' ';
        $lastOrderNo = '';

        $accdb = $this->openAccessDB();

        $orderFile = fopen($filename, 'r');
        $title = fgetcsv($orderFile);

        while (($fields = fgetcsv($orderFile)) != false) {

            $orderNo    = $fields[0];
            $date       = date('Y-m-d', strtotime($fields[1]));
            $sku        = $fields[16];
            $qty        = $fields[27];
            $shipMethod = $fields[15];

            if ($date < $start) {
                // date is UTC time
                continue;
            }

            // check if the order already imported
            $sql = "SELECT * FROM $table WHERE [PO #]='$orderNo'";
            $result = $accdb->query($sql)->fetch();
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

            echo "Importing $orderNo $channel\n";

            $sql = $this->insertMssql($table, [
                'Work Date'    => $date,
                'Channel'      => $channel,
                'PO #'         => $orderNo,
                'Xpress'       => $xpress,
                'Stock Status' => $stockStatus,
                'Qty'          => $qty,
                'Supplier'     => $supplier,
                'Supplier SKU' => $ponum,
                'Mfr #'        => $mfrpn,
                'Supplier #'   => '',
                'Remarks'      => '',
                'RelatedSKU'   => '',
                'Dimension'    => '',
            ]);

            $ret = $accdb->exec($sql);

            if (!$ret) {
                $this->error(__METHOD__);
                $this->error(print_r($accdb->errorInfo(), true));
                $this->error($sql);
            }

            $lastOrderNo = $orderNo;
        }

        fclose($orderFile);
    }

    protected function openAccessDB()
    {
        $dbname = "Z:/Purchasing/General Purchase.accdb";
        if (!IS_PROD) {
            $dbname = "C:/Users/BTE/Desktop/General Purchase.accdb";
        }

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
        return $this->di->get('skuService')->getMpn($sku);
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggOrderImportToAccessJob();
$job->run($argv);
