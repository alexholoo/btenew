<?php

include __DIR__ . '/../public/init.php';

class AmazonCategoryJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $this->saveCategories();
    }

    private function saveCategories()
    {
        $orders = $this->getOrders();

        foreach ($orders as $order) {
            $channel = $order['channel'];
            $sku     = $order['sku'];

            if (strtolower(substr($channel, 0, 6)) != 'amazon') {
                continue; // go to next SKU
            }

            if ($this->skuCategoryExists($sku)) {
                continue; // go to next SKU
            }

            $category = $this->getAmazonSkuCategory($channel, $sku);

            if (!$category) {
                $this->log("Failed to getCategory: $sku");
                continue;
            }

            $id   = $category['ProductCategoryId'];
            $name = $category['ProductCategoryName'];

            $this->log("$sku\t$id\t$name");

            $names = [];
            while ($category) {
                $catId   = $category['ProductCategoryId'];
                $catName = $category['ProductCategoryName'];

                array_unshift($names, $catName);

                if (isset($category['Parent'])) {
                    $parentId = $category['Parent']['ProductCategoryId'];
                    $category = $category['Parent'];
                } else {
                    $parentId = '';
                    $category = null;
                }

                //$this->saveAmazonCategory($catId, $catName, $parentId);
            }

            array_shift($names); // remove duplicated 'Categories'
            $this->saveSkuCategory($sku, $id, implode(' > ', $names));

            sleep(6); // Amazon throttling
        }

        $this->log(count($orders). ' SKUs processed.');
    }

    private function getAmazonSkuCategory($channel, $sku)
    {
        $this->log("getCategory: $channel $sku");

        $store = 'bte-amazon-ca';

        if ($channel = 'Amazon-US') {
            $store = 'bte-amazon-us';
        }

        $api = new \AmazonProductInfo($store);

        $api->setSKUs($sku);
        $api->fetchCategories();

        $products = $api->getProduct();
        $product = $products[0];

        if (!$product) {
            return false;
        }

        $data = $product->getData();
        $category = $data['Categories'][0];

        return $category;
    }

    private function saveAmazonCategory($id, $name, $parent)
    {
        if ($this->amazonCategoryExists($id)) {
            return;
        }

        try {
            $this->db->insertAsDict('amazon_category', [
                'category_id'   => $id,
                'category_name' => $name,
                'parent_id'     => $parent
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    private function saveSkuCategory($sku, $catId, $catName)
    {
        if ($this->skuCategoryExists($sku)) {
            return;
        }

        try {
            $this->db->insertAsDict('amazon_sku_category', [
                'sku'           => $sku,
                'category_id'   => $catId,
                'category_name' => $catName
            ]);
        } catch (\Exception $e) {
            //echo $e->getMessage(), EOL;
        }
    }

    private function amazonCategoryExists($id)
    {
        $sql = "SELECT category_id FROM amazon_category WHERE category_id='$id'";
        $result = $this->db->fetchOne($sql);
        return ($result);
    }

    private function skuCategoryExists($sku)
    {
        $sql = "SELECT sku FROM amazon_sku_category WHERE sku='$sku'";
        $result = $this->db->fetchOne($sql);
        return $result;
    }

    protected function getOrders()
    {
        $filename = 'w:/out/shipping/all_mgn_orders.csv';
        if (IS_PROD) {
            $filename = 'E:/BTE/import/all_mgn_orders.csv';
        }

        if (!($handle = @fopen($filename, 'rb'))) {
            $this->log("Failed to open file: $filename");
            return [];
        }

        $this->log("Loading $filename");

        $title = fgetcsv($handle);

        $columns = [
             'channel',
             'date',
             'order_id',
             'mgn_order_id',
             'express',
             'buyer',
             'address',
             'city',
             'province',
             'postalcode',
             'country',
             'phone',
             'email',
             'sku',
             'price',
             'qty',
             'shipping',
             'product_name',
        ];

        $orders = [];

        while (($fields = fgetcsv($handle))) {
            $orderId = $fields[2];
            if (count($columns) != count($fields)) {
                $this->log('Error: '. $orderId);
            }
            $order = array_combine($columns, $fields);
            $orders[] = [
                'channel' => $order['channel'],
                'sku'     => $order['sku'],
            ];
        }

        fclose($handle);

        return $orders;
    }
}

$job = new AmazonCategoryJob();
$job->run($argv);
