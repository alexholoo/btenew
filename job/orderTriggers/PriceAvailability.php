<?php

class PriceAvailability
{
    protected $priority = 20;
    protected $orders;

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function run($argv = [])
    {
        echo '>> ', __CLASS__, EOL;
        $this->getPriceAvail();
    }

    protected function getPriceAvail()
    {
        $skus = $this->getSkus();

        foreach ($skus as $sku) {
            try {
                echo 'Price & Availability for ', $sku, PHP_EOL;
                $client = \Supplier\Supplier::createClient($sku);
                if ($client) {
                    $client->getPriceAvailability($sku);
                }
            } catch (Exception $e) {
                echo $e->getMessage(), PHP_EOL;
            }
        }
    }

    protected function getSkus()
    {
        $list = [];

        foreach ($this->orders as $order) {
            $sku = $order['sku'];
            $skuGroup = $this->di->get('productService')->getSkuGroup($sku);
            $list = array_merge($list, $skuGroup);
        }

        return array_unique($list);
    }
}
