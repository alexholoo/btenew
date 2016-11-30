<?php

class PriceAvailJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
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
        $today = date('Y-m-d');

        $sql = "SELECT * FROM ca_order_notes WHERE date = '$today'";
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

include __DIR__ . '/../public/init.php';

$job = new PriceAvailJob();
$job->run($argv);
