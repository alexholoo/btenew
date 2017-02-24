<?php

include 'classes/Job.php';

use Supplier\Supplier;

class DropshipTrackingJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

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
