<?php

class PriceAvailability
{
    protected $orders;

    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function setOrders($orders)
    {
        $this->orders = $orders;
    }

    public function run($argv = [])
    {
        echo __METHOD__, EOL;
        //$this->getPriceAvail()
    }

    protected function getPriceAvail()
    {
        global $argv;

        if (empty($arg) && isset($argv[1])) {
            $arg = $argv[1];
        }

        $skus = $this->getSkus($arg);

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

    protected function getSkus($arg)
    {
        if (empty($arg)) {
            $arg = date('Y-m-d');
        }

        $sql = "SELECT * FROM ca_order_notes WHERE date = '$arg'";
        $result = $this->db->query($sql);

        $list = [];
        while ($row = $result->fetch(\Phalcon\Db::FETCH_ASSOC)) {
            $related_sku = explode('|', $row['related_sku']);
            $related_sku = array_map('trim', $related_sku);
            $related_sku = array_filter($related_sku);

            $list = array_merge($list, $related_sku);
        }

        return array_unique($list);
    }
}
