<?php

class AmazonOrderToAccess extends OrderTrigger
{
    protected $priority = 100;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->importAmazonOrders();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function importAmazonOrders()
    {
        $skuService = $this->di->get('skuService');

        $accdb = $this->openAccessDB();

        foreach ($this->orders as $order) {
            if ($order['channel'] != 'Amazon-ACA') {
                continue;
            }

            $sku         = $this->getSku($order['sku']);
            $date        = $order['date'];
            $orderid     = $order['order_id'];
            $xpress      = $order['express'];
            $stockStatus = ' ';
            $qty         = $order['qty'];
            $supplier    = $this->getSupplier($order['sku']);
            $ponum       = ' ';
            $mfrpn       = $skuService->getMpn($order['sku']);
            $notes       = '';
            $dimension   = '';

            if (isset($order['overstock-changed'])) {
                $stockStatus = 'In Stock';
            }

            $sql = "SELECT * FROM Amazon_CA WHERE [order_id]='$orderid'";
            $result = $accdb->query($sql)->fetch();
            if ($result) {
                continue;
            }

            $sql = $this->insertMssql("Amazon_CA", [
                'date'         => $date,
                'order_id'     => $orderid,
                'Stock Status' => $stockStatus,
                'Xpress'       => $xpress,
                'qty'          => $qty,
                'supplier'     => $supplier,
                'supplier_sku' => $sku,
                'MPN'          => $mfrpn,
                'Supplier#'    => $ponum,
                'notes'        => $notes,
                'related_sku'  => '',
                'dimension'    => $dimension,
            ]);

            $ret = $accdb->exec($sql);

            if (!$ret) {
                $this->error(__METHOD__);
                $this->error($accdb->errorInfo());
                $this->error($sql);
            }

            $this->log($orderid);
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
