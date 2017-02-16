<?php

class AmazonCategory extends Job
{
    protected $priority = 0;  // 0 to disable
    protected $orders;

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
        $this->log('>> '. __CLASS__);

        $this->saveCategories();
    }

    private function saveCategories()
    {
        foreach ($this->orders as $order) {
            $channel = $order['channel'];
            $sku     = $order['sku'];

            if (strtolower(substr($channel, 0, 6)) != 'amazon') {
                continue; // go to next SKU
            }

            if ($this->skuCategoryExists($sku)) {
                continue; // go to next SKU
            }

            $category = $this->getAmazonSkuCategory($channel, $sku);

            $id   = $category['ProductCategoryId'];
            $name = $category['ProductCategoryName'];

            $this->saveSkuCategory($sku, $id, $name);

            $this->log("$sku\t$id\t$name");

            while ($category) {
                $id = $category['ProductCategoryId'];
                $name = $category['ProductCategoryName'];

                if (isset($category['Parent'])) {
                    $parentId = $category['Parent']['ProductCategoryId'];
                    $category = $category['Parent'];
                } else {
                    $parentId = '';
                    $category = null;
                }

                $this->saveAmazonCategory($id, $name, $parentId);
            }
        }

        $this->log(count($this->orders). ' SKUs processed.');
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
}
