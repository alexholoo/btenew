<?php

abstract class Tracking_Downloader extends Job
{
    abstract public function download();

    // only for trackings of ASI/ING/TD dropshipped orders
    private function openAccessDB()
    {
        $dbname = "Z:/Purchasing/General Purchase.accdb";

        if (!IS_PROD) {
            $dbname = "C:/Users/BTE/Desktop/General Purchase.accdb";
        }

        $dsn = "odbc:Driver={Microsoft Access Driver (*.mdb, *.accdb)};DBQ=$dbname;";
        $db = new PDO($dsn);

        return $db;
    }

    // Get order list, part of them are dropshipped orders
    protected function getOrderList()
    {
        $orders = [];

        $accdb = $this->openAccessDB();
        $start = date('Y-m-d', strtotime('-5 day'));

        // Amazon CA
        $sql = "SELECT * FROM Amazon_CA WHERE [date] >= #$start#";
        $rows = $accdb->query($sql);
        while ($row = $rows->fetch()) {
            $orders[] = [
                'date'     => $row['date'],
                'orderId'  => $row['order_id'],
                'status'   => '',
                'supplier' => $row['supplier'],
            ];
        }

        // Bestbuy CA
        $sql = "SELECT * FROM BestbuyCA WHERE [Work Date] >= #$start#";
        $rows = $accdb->query($sql);

        while ($row = $rows->fetch()) {
            $orders[] = [
                'date'     => $row['Work Date'],
                'orderId'  => $row['Order #'],
                'status'   => $row['Stock Status'],
                'supplier' => $row['Supplier'],
            ];
        }

        // Newegg CA
        $sql = "SELECT * FROM Newegg WHERE [Work Date] >= #$start#";
        $rows = $accdb->query($sql);

        while ($row = $rows->fetch()) {
            $orders[] = [
                'date'     => $row['Work Date'],
                'orderId'  => $row['PO #'],
                'status'   => $row['Stock Status'],
                'supplier' => $row['Supplier'],
            ];
        }

        return $orders;
    }

    protected function getDropshippedOrders($supplierName)
    {
        $shipmentService = $this->di->get('shipmentService');

        $orders = [];

        $orderList = $this->getOrderList();

        foreach ($orderList as $order) {
            $orderId = $order['orderId'];
            $supplier = $order['supplier'];

            if ($shipmentService->isOrderShipped($orderId)) {
                continue;
            }

            if ($supplier != $supplierName) {
                continue;
            }

            $orders[] = $order;
        }

        return $orders;
    }
}
