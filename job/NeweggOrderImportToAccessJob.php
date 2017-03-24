<?php

include 'classes/Job.php';

use Marketplace\Newegg\StdOrderListFile;

class NeweggOrderImportToAccessJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        include('order/Filenames.php');

        $neweggOrderReport = Filenames::get('newegg.ca.master.order');
        $this->channel = 'NeweggCA';
        $this->importNeweggOrders($neweggOrderReport, 'Newegg', 'CA');

        $neweggOrderReport = Filenames::get('newegg.us.master.order');
        $this->channel = 'NeweggUS';
        $this->importNeweggOrders($neweggOrderReport, 'NeweggUSA', 'US');
    }

    protected function importNeweggOrders($filename, $table, $site)
    {
        // start from yesterday to avoid midnight issue (UTC timezone)
        $start = date('Y-m-d', strtotime('Yesterday'));

        $channel = $this->channel;

        $stockStatus = ' ';
        $lastOrderNo = '';

        $accdb = $this->openAccessDB();

        $orderFile = new StdOrderListFile($filename, $site);

        while ($fields = $orderFile->read()) {

            $orderNo    = $fields['Order Number'];
            $date       = date('Y-m-d', strtotime($fields['Order Date & Time']));
            $sku        = $fields['Item Seller Part #'];
            $qty        = $fields['Quantity Ordered'];
            $shipMethod = $fields['Order Shipping Method'];

            if ($date < $start) {
                // date is UTC time
                continue;
            }

            // check if the order already imported
            $sql = "SELECT * FROM $table WHERE [PO #]='$orderNo'";
            $result = $accdb->query($sql)->fetch();
            if ($result && $orderNo != $lastOrderNo) {
                //echo "Skip $orderNo $channel\n";
                continue;
            }

            $supplier   = $this->getSupplier($sku);
            $ponum      = $this->getSku($sku);
            $shipMethod = $this->getShipMethod($shipMethod);
            $mfrpn      = $this->getMfrPartNum($sku);

            #$data = array_combine($title, $fields);
            #print_r($data); break;

            echo "Importing $orderNo $channel\n";

            $sql = $this->insertMssql($table, [
                'Work Date'    => $date,
                'Channel'      => $channel,
                'PO #'         => $orderNo,
                'ShipMethod'   => $shipMethod,
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

    protected function getShipMethod($shipMethod)
    {
        # Expedited Shipping (3-5 business days)
        # International Expedited Shipping (3-5 business days)
        # One-Day Shipping(Next day)
        # Standard Shipping (5-7 business days)
        # Two-Day Shipping(2 business days)

        $keywords = [ 'Expedited', 'One-Day', 'Standard', 'Two-Day' ];

        foreach ($keywords as $keyword) {
            if (strpos($shipMethod, $keyword) !== false) {
                return $keyword;
            }
        }

        return '';
    }

    protected function getMfrPartNum($sku)
    {
        return $this->di->get('skuService')->getMpn($sku);
    }
}

include __DIR__ . '/../public/init.php';

$job = new NeweggOrderImportToAccessJob();
$job->run($argv);
