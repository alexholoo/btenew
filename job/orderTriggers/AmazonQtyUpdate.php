<?php

use Supplier\Supplier;
use Marketplace\Amazon\Feeds\PriceAndQuantityUpdateFile;

class AmazonQtyUpdate
{
    protected $priority = 30;
    protected $orders;

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');

        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1');
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function run($argv = [])
    {
        echo '>> ', __CLASS__, EOL;
        $this->generateFlatFiles();
    }

    private function generateFlatFiles()
    {
        if ($this->redis->get('job:sku-map-import:running')) {
            return;
        }

        $now = date('ymd-H');

        $flatFileCA = new PriceAndQuantityUpdateFile("E:/BTE/amazon/update/amazon-ca-qty-$now.csv");
        $flatFileUS = new PriceAndQuantityUpdateFile("E:/BTE/amazon/update/amazon-us-qty-$now.csv");

        foreach ($this->orders as $order) {
            $channel = $order['channel'];
            $orderid = $order['order_id'];
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

        echo count($this->orders), ' orders processed.', EOL;

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
            $overallQty = $result['overall_qty'];
            $remaining = max($overallQty - $qty, 0);
            $sql = "UPDATE master_sku_list SET overall_qty='$remaining' WHERE sku='$sku'";
            $this->db->execute($sql);
            return $overallQty;
        }
        return 0;
    }
}
