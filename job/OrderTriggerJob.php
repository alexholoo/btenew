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

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1');
    }

    public function run($argv = [])
    {
        if ($this->redis->get('job:sku-map-import:running')) {
            return;
        }

        $now = date('Ymd-His');

        $flatFileCA = new PriceAndQuantityUpdateFile("E:/BTE/amazon/update/amazon-ca-priceqty-$now.csv");
        $flatFileUS = new PriceAndQuantityUpdateFile("E:/BTE/amazon/update/amazon-us-priceqty-$now.csv");

        // Get orders in last 10 minutes
        $orders = $this->getLatestOrders();

        foreach ($orders as $order) {
            $channel = $order['channel'];
            $orderid = $order['orderid'];
            $sku     = $order['sku'];
            $price   = $order['price'];
            $qty     = $order['qty'];

            echo $orderid, ' => ',  $sku, ' ', $qty, EOL;

            if ($this->outOfStock($sku, $qty)) {
                if ($channel == 'Amazon-ACA') {
                    $flatFileCA->write([ $sku, $price, 0 ]);
                }
                if ($channel == 'Amazon-US') {
                    $flatFileUS->write([ $sku, $price, 0 ]);
                }
            }
        }

        $flatFileCA->close();
        $flatFileUS->close();

        echo count($orders), ' orders processed.', EOL;

        #$this->uploadFeed('bte-amazon-ca', $flatFileCA->getFilename());
        #$this->uploadFeed('bte-amazon-us', $flatFileUS->getFilename());
    }

    private function outOfStock($sku, $qty)
    {
        $totalQty = 0;

        $skuGroup = $this->di->get('productService')->getSkuGroup($sku);

        foreach ($skuGroup as $sku) {
            $client = Supplier::createClient($sku);

            if ($client) {
                $result = $client->getPriceAvailability($sku);
                $availQty = $result->getFirst()->getTotalQty();

                echo "\t$sku\t", $availQty, EOL;

                if ($availQty > 0) {
                    $totalQty += $availQty;
                }
            } else {
                $invQty = $this->getInventoryQty($sku, $qty);
                $totalQty += $invQty;
            }
        }

        return $totalQty <= $qty;
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

    private function getInventoryQty($sku, $qty)
    {
        $sql = "SELECT overall_qty FROM master_sku_list WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        if ($result) {
            $totalQty = $result['overall_qty'];
            $availQty = max($totalQty - $qty, 0);
            $sql = "UPDATE master_sku_list SET overall_qty='$availQty'";
            $this->db->execute($sql);
            return $totalQty;
        }
        return 0;
    }

    private function getLatestOrders($minute = 10)
    {
        $orders = [];

        // This is NOT latest orders, this is all today's orders !!!
        $sql = "SELECT * FROM all_mgn_orders WHERE channel IN ('Amazon-ACA', 'Amazon-US') AND date=DATE(NOW())";

        $items = $this->db->fetchAll($sql);

        foreach ($items as $item) {
            $orders[] = [
                'channel' => $item['channel'],
                'orderid' => $item['order_id'],
                'sku'     => $item['skus_sold'],  // ??
                'price'   => $item['sku_price'],  // ??
                'qty'     => $item['skus_qty']    // ??
            ];
        }

        return $orders;
    }

    private function getOrders($minute = 10)
    {
        $orders = [];

        $sql = "SELECT Channel, o.OrderId, SellerSKU, ItemPrice, QuantityOrdered
                FROM amazon_order o
                JOIN amazon_order_item i ON o.OrderId = i.OrderId
                WHERE o.createdon > (NOW() - INTERVAL $minute MINUTE)";

        $items = $this->db->fetchAll($sql);

        foreach ($items as $item) {
            $orders[] = [
                'channel' => $item['Channel'],
                'orderid' => $item['OrderId'],
                'sku'     => $item['SellerSKU'],
                'price'   => $item['ItemPrice'] / $item['QuantityOrdered'],
                'qty'     => $item['QuantityOrdered']
            ];
        }

        return $orders;
    }
}

include __DIR__ . '/../public/init.php';

$job = new OrderTriggerJob();
$job->run($argv);
