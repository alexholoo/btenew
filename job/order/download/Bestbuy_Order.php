<?php

class Bestbuy_Order extends OrderDownloader
{
    public function download()
    {
        return;

        $this->skuService = $this->di->get('skuService');

        $this->importBestbuyOrders();
    }

    protected function importBestbuyOrders()
    {
        $accdb = $this->openAccessDB();

        $orders = $this->getBestbuyOrders();

        foreach ($orders as $order) {
            $workDate    = $order['date'];
            $orderId     = $order['orderId'];
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
                'Xpress'        => $xpress,
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
        $client = new Marketplace\Bestbuy\Client();

        // only today's orders
        $start = date('Y-m-d\T00:00:00');
        $end   = date('Y-m-d\T23:59:59');

        $orders = $client->listOrders($start, $end);

        // TODO: move this to Marketplace\Bestbuy\Client?
        foreach ($orders as $key => $order) {
            // order_state:
            // - WAITING_ACCEPTANCE
            // - WAITING_DEBIT_PAYMENT
            // - CANCELED
            // - RECEIVED
            if ($order['state'] != 'RECEIVED') {
                unset($orders[$key]);
                $this->log($order['orderId'].' '.$order['state']);
            }
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
