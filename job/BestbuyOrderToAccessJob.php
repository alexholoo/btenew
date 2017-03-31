<?php

include 'classes/Job.php';

class BestbuyOrderToAccessJob extends Job 
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->skuService = $this->di->get('skuService');

        $this->importBestbuyOrders();
    }

    protected function importBestbuyOrders()
    {
        $accdb = $this->openAccessDB();

        $orders = $this->getBestbuyOrders();

        foreach ($orders as $order) {
            $workDate    = $order['date'];
            $orderId     = substr($order['orderId'], 0, -2);
            $sku         = $order['sku'];
            $channel     = 'BestbuyCA';
            $xpress      = $order['express'];
            $qty         = $order['qty'];
            $supplier    = $this->skuService->getSupplier($sku);
            $ponum       = ' ';
            $mfrpn       = $this->skuService->getMpn($sku);
            $stockStatus = ' ';

            // TODO: fix multi-items-order issue
            $sql = "SELECT * FROM BestbuyCA WHERE [Order #]='$orderId'";
            $result = $accdb->query($sql)->fetch();
            if ($result) {
                continue;
            }

            $data = [
                'Work Date'     => $workDate,
                'Channel'       => $channel,
                'Order #'       => $orderId,
                'Express'       => $xpress,
                'Stock Status'  => $stockStatus,
                'Qty'           => $qty,
                'Supplier'      => $supplier,
                'Supplier SKU'  => $sku,
                'Mfr #'         => $mfrpn,
                'Supplier #'    => ' ',
                'Remarks'       => '',
                'RelatedSKU'    => '',
                'Dimension'     => '',
            ];

            $sql = $this->insertMssql('BestbuyCA', $data);

            $ret = $accdb->exec($sql);

            if (!$ret) {
                $this->error(__METHOD__);
                $this->error(print_r($accdb->errorInfo(), true));
                $this->error($sql);
            }

            $this->log($orderId);
        }
    }

    protected function getBestbuyOrders()
    {
        include ('order/Filenames.php');

        $filename = Filenames::get('bestbuy.order');
        $orderFile = new Marketplace\Bestbuy\OrderReportFile($filename);

        $orders = [];

        while ($order = $orderFile->read()) {
            if ($order['status'] == 'CANCELED') {
                continue;
            }
           #$orderId = $order['orderId'];
            $orders[] = $order;
        }

        return $orders;
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
}

include __DIR__ . '/../public/init.php';

$job = new BestbuyOrderToAccessJob();
$job->run($argv);
