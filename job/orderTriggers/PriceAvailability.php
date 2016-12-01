<?php

class PriceAvailability extends Job
{
    protected $priority = 0;  // 0 to disable
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
        $this->log('>> '. __CLASS__);
        $this->getPriceAvail();
    }

    protected function getPriceAvail()
    {
        $skus = $this->getSkus();

        foreach ($skus as $sku) {
            try {
                $this->log('Price & Availability for '. $sku);
                $client = \Supplier\Supplier::createClient($sku);
                if ($client) {
                    $client->getPriceAvailability($sku);
                }
            } catch (Exception $e) {
                $this->log($e->getMessage());
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