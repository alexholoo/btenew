<?php

use Supplier\Supplier;
use Marketplace\Amazon\Feeds\PriceAndQuantityUpdateFile;

class OrderTriggerJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');

        $now = date('Ymd-His');

        $this->flatFileCA = new PriceAndQuantityUpdateFile("amazon-ca-priceqty-$now.csv");
       #$this->flatFileCA = new PriceAndQuantityUpdateFile("E:/BTE/amazon/update/amazon-ca-priceqty-$now.csv");
        $this->flatFileUS = new PriceAndQuantityUpdateFile("amazon-us-priceqty-$now.csv");
       #$this->flatFileUS = new PriceAndQuantityUpdateFile("E:/BTE/amazon/update/amazon-us-priceqty-$now.csv");
    }

    public function run($argv = [])
    {
        // Get orders in last 10 minutes
        $orders = $this->getOrders();

        foreach ($orders as $order) {
            $channel = $order['channel'];
            $sku     = $order['sku'];
            $price   = $order['price'];
            $qty     = $order['qty'];

            $client = Supplier::createClient($sku);

            if ($client) {
                echo 'PriceAvailability: ', $sku, EOL;

                $result = $client->getPriceAvailability($sku);
                //pr($result);

                $totalQty = $result->getFirst()->getTotalQty();
                if ($totalQty < 2) {
                    if ($channel == 'Amazon-ACA') {
                        $this->flatFileCA->write([ $sku, $price, 0 ]);
                    }
                    if ($channel == 'Amazon-US') {
                        $this->flatFileUS->write([ $sku, $price, 0 ]);
                    }
                }
            } else {
                // TOOD:
            }
        }

        $this->flatFileCA->close();
        $this->flatFileUS->close();

        #$this->uploadFeed('bte-amazon-ca', $this->flatFileCA->getFilename());
        #$this->uploadFeed('bte-amazon-us', $this->flatFileUS->getFilename());
    }

    private function uploadFeed($store, $file)
    {
        if (!file_exists($file)) {
            return;
        }

        echo "Uploading feed: $file", EOL;

        $client = new Marketplace\Amazon\Client($store);
        $client->uploadPriceQuantity($file);
    }

    private function getOrders($minute = 10)
    {
        $orders = [];

        $sql = "SELECT Channel, SellerSKU, ItemPrice, QuantityOrdered 
                FROM amazon_order o
                JOIN amazon_order_item i ON o.OrderId = i.OrderId
                WHERE o.createdon > (NOW() - INTERVAL $minute MINUTE)";

        $items = $this->db->fetchAll($sql);

        foreach ($items as $item) {
            $orders[] = [
                'channel' => $item['Channel'],
                'sku'     => $item['SellerSKU'],
                'price'   => $item['ItemPrice'],
                'qty'     => $item['QuantityOrdered']
            ];
        }

        return $orders;
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderTriggerJob();
$job->run($argv);
