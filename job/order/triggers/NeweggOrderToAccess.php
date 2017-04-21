<?php

class NeweggOrderToAccess extends OrderTrigger
{
    protected $priority = 130;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('=> Order Trigger: '. __CLASS__);

        try {
            $this->importNeweggOrders();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    protected function importNeweggOrders()
    {
        $lastOrderNo = '';

        $accdb = $this->openAccessDB();

        $neweggService = $this->di->get('neweggService');

        foreach ($this->orders as $order) {

            $orderNo = $order['order_id'];
            $date    = $order['date'];
            $sku     = $order['sku'];
            $qty     = $order['qty'];

            $shipMethod = $neweggService->getShipMethodName($order['express']);

            if ($order['channel'] == 'NeweggCA') {
                $channel = 'NeweggCA';
                $table   = 'Newegg';
            } else if ($order['channel'] == 'NeweggUSA') {
                $channel = 'NeweggUS';
                $table   = 'NeweggUSA';
            } else {
                continue;
            }

            $stockStatus = ' ';
            if (isset($order['overstock-changed'])) {
                $stockStatus = 'In Stock';
            }

            // check if the order already imported
            $sql = "SELECT * FROM $table WHERE [PO #]='$orderNo'";
            $result = $accdb->query($sql)->fetch();
            if ($result && $orderNo != $lastOrderNo) {
                //echo "Skip $orderNo $channel\n";
                continue;
            }

            $supplier = $this->getSupplier($sku);
            $ponum    = $this->getSupplierSku($sku);
            $mfrpn    = $this->getMfrPartNum($sku);

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

    protected function getMfrPartNum($sku)
    {
        return $this->di->get('skuService')->getMpn($sku);
    }
}
