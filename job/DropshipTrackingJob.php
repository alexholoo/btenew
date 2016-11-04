<?php

use Supplier\Supplier;

class DropshipTrackingJob
{
    public function __construct()
    {
        $this->di = \Phalcon\Di::getDefault();
        $this->db = $this->di->get('db');
        $this->queue = $this->di->get('queue');
    }

    public function run($argv = [])
    {
        $sql = 'SELECT * FROM purchase_order_log WHERE shipped=0';
        $result = $this->db->query($sql);

        while ($order = $result->fetch()) {
            $sku     = $order['sku'];
            $orderId = $order['orderid'];
            $ponum   = $order['ponumber'];
            $invoice = $order['invoice'];

            echo $orderId, ' ', $sku, ' ';

            try {

                $client = Supplier::createClient($sku);
                if (!$client) {
                    echo 'Not support', EOL;
                    continue;
                }

                $tracking = $client->getOrderStatus($orderId);
                if ($tracking->trackingNumber) {
                    echo $tracking->trackingNumber;
                } else {
                    echo '<NotYet>';
                }

            } catch (\Exception $e) {
                echo $e->getMessage();
            }

            echo EOL;
        }
    }
}

include __DIR__ . '/../public/init.php';

$job = new DropshipTrackingJob();
$job->run($argv);
