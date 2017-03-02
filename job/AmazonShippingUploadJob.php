<?php

include 'classes/Job.php';

use Toolkit\File;
use Shipment\AmazonShipmentFile;

class AmazonShippingUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $store    = 'bte-amazon-ca';
        $orders   = $this->getUnshippedOrders($store);
        #filename = 'w:/out/shipping/amazon_ca_shipment.txt';
        $filename = 'E:/BTE/shipping/amazon_ca_shipment.txt';
        $this->outputFeed($filename, $orders);
        $this->uploadFeed($store, $filename);

        $store    = 'bte-amazon-us';
        $orders   = $this->getUnshippedOrders($store);
        #filename = 'w:/out/shipping/amazon_us_shipment.txt';
        $filename = 'E:/BTE/shipping/amazon_us_shipment.txt';
        $this->outputFeed($filename, $orders);
        $this->uploadFeed($store, $filename);
    }

    private function getUnshippedOrders($store)
    {
        //$this->log('=> '. __FUNCTION__);

        $orders = [];

        try {
            $api = new AmazonOrderList($store);

            $api->setFulfillmentChannelFilter("MFN");
            $api->setLimits('Modified', "-7 days");
            $api->setOrderStatusFilter(["Unshipped", "PartiallyShipped", "Canceled", "Unfulfillable"]);
            $api->setUseToken();
            $api->fetchOrders();

            if (0) {
                // save the response to log file
                $logfile = str_replace('job', 'Amazon-List-Orders', $this->getLogFilename());
                foreach ($api->getRawResponses() as $response) {
                    file_put_contents($logfile, $response['body'], FILE_APPEND);
                }
            }

            // return only unshipped orders
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

    private function outputFeed($filename, $orders)
    {
        //$this->log('=> '. __FUNCTION__);

        $feedFile = new AmazonShipmentFile($filename);

        $shipmentService = $this->di->get('shipmentService');

        foreach ($orders as $order) {
            $orderId = $order->getAmazonOrderId();

            $tracking = $shipmentService->getOrderTracking($orderId);

            if ($tracking) {
                // change unrecognized carriers to 'Other'
                $carrierCode = $tracking['carrierCode'];
                $carrierName = $tracking['carrierName'];

                // Amazon Report the Error: The carrier-code field contains an invalid value: Purolator.
                if ($carrierCode == 'Purolator') {
                    $carrierCode = 'Other';
                    $carrierName = 'Purolator';
                }

                // Amazon Report the Error: The carrier-code field contains an invalid value: Loomis.
                if ($carrierCode == 'Loomis') {
                    $carrierCode = 'Other';
                    $carrierName = 'Loomis';
                }

                $feedFile->write([
                    $orderId,                     //'order-id'
                    '',                           //'order-item-id'
                    '',                           //'quantity'
                    $tracking['shipDate'],        //'ship-date'
                    $carrierCode,                 //'carrier-code'
                    $carrierName,                 //'carrier-name'
                    $tracking['trackingNumber'],  //'tracking-number'
                    $tracking['shipMethod'],      //'ship-method'
                ]);
            }
        }
    }

    private function uploadFeed($store, $file)
    {
        //$this->log('=> '. __FUNCTION__);

        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            //$this->error(__METHOD__." File not found: $file");
            return;
        }

        $this->log("Uploading shipping: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType('_POST_FLAT_FILE_FULFILLMENT_DATA_');
        $api->setFeedContent($feed);
        $api->submitFeed();

        $this->markOrdersShipped($file);

        File::backup($file);

       #$this->log(print_r($api->getResponse(), true));
    }

    protected function markOrdersShipped($filename)
    {
        $fp = fopen($filename, 'r');

        $columns = fgetcsv($fp); // skip first line

        $shipmentService = $this->di->get('shipmentService');

        while (($fields = fgetcsv($fp))) {
            $orderId = $fields[0];
            $shipmentService->markOrderAsShipped($orderId);
        }

        fclose($fp);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonShippingUploadJob();
$job->run($argv);
