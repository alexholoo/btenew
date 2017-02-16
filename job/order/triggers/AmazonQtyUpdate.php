<?php

use Supplier\Supplier;
use Marketplace\Amazon\Feeds\PriceAndQuantityUpdateFile;

class AmazonQtyUpdate extends OrderTrigger
{
    protected $priority = 0;  // 0 to disable

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);
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

            $this->log("$orderid => $sku $qty");

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

        $this->log(count($this->orders). ' orders processed.');

        #$this->uploadFeed('bte-amazon-ca', $flatFileCA->getFilename());
        #$this->uploadFeed('bte-amazon-us', $flatFileUS->getFilename());
    }

    private function outOfStock($sku, $qty)
    {
        $totalQty = 0;

        $skuGroup = $this->di->get('skuService')->getAltSkus($sku);

        foreach ($skuGroup as $sku) {
            $client = Supplier::createClient($sku);

            if ($client) {
                $result = $client->getPriceAvailability($sku);
                $availQty = $result->getFirst()->getTotalQty();

                $this->log("\t$sku\t$availQty");

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

        $this->log("Uploading feed: $file");

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
