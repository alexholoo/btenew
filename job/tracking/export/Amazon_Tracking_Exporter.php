<?php

class Amazon_Tracking_Exporter extends Tracking_Exporter
{
    public function export()
    {
        // Nothing to do, it's done in AmazonShippingUploadJob
        //
        // It's might be better to move AmazonShippingUploadJob here
        return;

        $store    = 'bte-amazon-ca';
        $orders   = $this->getUnshippedOrders($store);
        $filename = Filenames::get('amazon.ca.shipping');
        $this->outputFeed($filename, $orders);

        $store    = 'bte-amazon-us';
        $orders   = $this->getUnshippedOrders($store);
        $filename = Filenames::get('amazon.us.shipping');
        $this->outputFeed($filename, $orders);
    }

    private function getUnshippedOrders($store)
    {
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

                if ($carrierCode == 'TNT') {
                    $carrierCode = 'Other';
                    $carrierName = 'TNT';
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
}
