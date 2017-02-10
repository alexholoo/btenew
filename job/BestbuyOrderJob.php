<?php

include 'classes/Job.php';

class BestbuyOrderJob extends Job
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
            $orderId     = $order['orderId'];
            $sku         = $order['sku'];
            $channel     = 'BestbuyCA';
            $xpress      = $order['express'];
            $qty         = $order['qty'];
            $supplier    = $this->skuService->getSupplier($sku);
            $ponum       = ' ';
            $mfrpn       = $this->skuService->getMpn($sku);
            $stockStatus = ' ';

            $sql = "SELECT * FROM BestbuyCA WHERE [Order #]='$orderId'";
            $result = $accdb->query($sql)->fetch();
            if ($result) {
                continue;
            }

            $sql = "INSERT INTO BestbuyCA (
                        [Work Date],
                        [Channel],
                        [Order #],
                        [Express],
                        [Stock Status],
                        [Qty],
                        [Supplier],
                        [Supplier SKU],
                        [Mfr #],
                        [Supplier #],
                        [Remarks],
                        [Xpress],
                        [RelatedSKU],
                        [Dimension]
                    )
                    VALUES (
                        '$workDate',
                        '$channel',
                        '$orderId',
                        $xpress,
                        '$stockStatus',
                        '$qty',
                        '$supplier',
                        '$sku',
                        '$mfrpn',
                        '$ponum',
                        '',
                        $xpress,
                        '',
                        ''
                    )";

            $ret = $accdb->exec($sql);

            if (!$ret) {
                print_r($accdb->errorInfo());
            }

            $this->log($orderId);
        }
    }

    protected function getBestbuyOrders()
    {
        $orders = [];
/*
        // only today's orders
        $start = date('Y-m-d\T00:00:00');
        $end   = date('Y-m-d\T23:59:59');

        $url = "https://marketplace.bestbuy.ca/api/orders?start_date=$start&end_date=$end";

        $apikey = $this->di->get('config')->bestbuy->apikey;

        $options = [
            'http' => [
                'header'  => "Authorization: $apikey\r\n",
                'method'  => 'GET',
               #'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === FALSE) {
            $this->log(__METHOD__." Error");
            return $orders;
        }
*/
        $result = file_get_contents('../tmp/bborders.json');

        $json = json_decode($result);

        foreach ($json->orders as $order) {
            foreach ($order->order_lines as $item) {
                $orders[] = [
                    'date'    => substr($order->created_date, 0, 10),
                    'orderId' => $order->order_id,
                    'sku'     => $item->offer_sku,
                    'price'   => $item->price,
                    'qty'     => $item->quantity,
                    'express' => intval($order->shipping_type_code == 'E'),
                ];
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

include __DIR__ . '/../public/init.php';

$job = new BestbuyOrderJob();
$job->run($argv);
