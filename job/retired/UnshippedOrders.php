<?php

include 'classes/Job.php';

use Shipment\AmazonShipmentFile;

class AmazonUnshippedOrders extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $store    = 'bte-amazon-ca';
        $filename = 'w:/out/shipping/amazon_ca_shipment.txt';
        $filename = 'E:/BTE/shipping/amazon_ca_shipment.txt';
        $orders   = $this->getUnshippedOrders($store);
        $fname    = $this->outputFeed('CA', $orders);

        $store    = 'bte-amazon-us';
        $filename = 'w:/out/shipping/amazon_us_shipment.txt';
        $filename = 'E:/BTE/shipping/amazon_us_shipment.txt';
        $orders   = $this->getUnshippedOrders($store);
        $fname    = $this->outputFeed('US', $orders);
    }

    private function outputFeed($site, $orders)
    {
        $feedFile = new AmazonShipmentFile($site);

        $shipmentService = $this->di->get('shipmentService');

        foreach ($orders as $order) {
            $orderId = $order->getAmazonOrderId();
            $tracking = $shipmentService->getOrderTracking($orderId);
            if ($tracking) {
                $feedFile->write([
                    $orderId,                     //'order-id'
                    '',                           //'order-item-id'
                    '',                           //'quantity'
                    $tracking['shipDate'],        //'ship-date'
                    $tracking['carrierCode'],     //'carrier-code'
                    $tracking['carrierName'],     //'carrier-name'
                    $tracking['trackingNumber'],  //'tracking-number'
                    $tracking['shipMethod'],      //'ship-method'
                ]);
            }
        }

        return $feedFile->getFilename();
    }

    private function getUnshippedOrders($store)
    {
        $orders = [];

        try {
            $api = new AmazonOrderList($store);

            $api->setFulfillmentChannelFilter("MFN");
            $api->setLimits('Modified', "-24 hours");
            $api->setOrderStatusFilter(["Unshipped", "PartiallyShipped", "Canceled", "Unfulfillable"]);
            $api->setUseToken();
            $api->fetchOrders();

            $logfile = str_replace('job', 'Amazon-List-Orders', $this->getLogFilename());
            foreach ($api->getRawResponses() as $response) {
                file_put_contents($logfile, $response['body'], FILE_APPEND);
            }

            $list = $api->getList();

            foreach ($list as $order) {
                if ($order->getOrderStatus() == 'Unshipped') {
                    $orders[] = $order;
                }
            }
        } catch (Exception $ex) {
            echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
        }

        return $orders;
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonUnshippedOrders();
$job->run($argv);
