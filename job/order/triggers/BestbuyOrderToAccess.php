<?php

class BestbuyOrderToAccess extends OrderTrigger
{
    protected $priority = 110;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->importBestbuyOrders();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function importBestbuyOrders()
    {
        $this->skuService = $this->di->get('skuService');

        $accdb = $this->openAccessDB();

        foreach ($this->orders as $order) {
            if ($order['channel'] != 'Bestbuy') {
                continue;
            }

            $workDate    = $order['date'];
            $orderId     = $order['reference'];
            $sku         = $order['sku'];
            $channel     = 'BestbuyCA';
            $xpress      = $order['express'];
            $qty         = $order['qty'];
            $supplier    = $skuService->getSupplier($sku);
            $ponum       = ' ';
            $mfrpn       = $skuService->getMpn($sku);
            $stockStatus = ' ';

            if (isset($order['overstock-changed'])) {
                $stockStatus = 'In Stock';
            }

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
