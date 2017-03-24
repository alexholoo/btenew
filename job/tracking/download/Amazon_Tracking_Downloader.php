<?php

class Amazon_Tracking_Downloader extends Tracking_Downloader
{
    protected $store;

    public function download()
    {
        // CA
        $this->store = 'bte-amazon-ca';
        $this->orderFile = Filenames::get('amazon.ca.dropship');
        $this->trackingFile = Filenames::get('amazon.ca.tracking');
        $list = $this->downloadTracking();

        // US
        $this->store = 'bte-amazon-us';
        $this->orderFile = Filenames::get('amazon.us.dropship');
        $this->trackingFile = Filenames::get('amazon.us.tracking');
        $list = $this->downloadTracking();
    }

    protected function downloadTracking()
    {
        $this->log('Downloading Amazon Dropship Tracking: '.$this->store);

        $api = new \AmazonFulfillmentOrderList($this->store);

        $api->setStartTime('-30 days');
        $api->setUseToken(true); // auto fetch next
        $api->fetchOrderList();

        $list = $api->getFullList();
        if (empty($list)) {
            return;
        }

        if (0) {
            // save the response to log file
            $logfile = str_replace('job', 'Amazon-FBA-Orders', $this->getLogFilename());
            foreach ($api->getRawResponses() as $response) {
                file_put_contents($logfile, $response['body'], FILE_APPEND);
            }
        }

        $orderFile = fopen($this->orderFile, 'w');
       #fputcsv($orderFile, ['orderId', 'orderDate']);

        $trackingFile = fopen($this->trackingFile, 'w');
       #fputcsv($trackingFile, ['orderId', 'shipmentStatus', 'TrackingNumber', 'Carrier', 'shippingDate']);

        foreach ($list as $fulfillment) {
            $order     = $fulfillment->getOrder();

            $details   = $order['Details'];
            $items     = $order['Items'];
            $shipments = isset($order['Shipments']) ? $order['Shipments'] : [];

            $orderId   = $details['DisplayableOrderId'];
            $orderDate = substr($details['DisplayableOrderDateTime'], 0, 10);

            // This is not necessary
            fputcsv($orderFile, [ $orderId, $orderDate ]);

            // This is what we need
            foreach ($shipments as $shipment) {
                $shipmentStatus = $shipment['FulfillmentShipmentStatus'];
                $shippingDate   = substr($shipment['ShippingDateTime'], 0, 10);

                if ($shipmentStatus != 'SHIPPED') {
                    continue;
                }

                foreach ($shipment['FulfillmentShipmentPackage'] as $package) {
                    fputcsv($trackingFile, [
                        $orderId,
                        $shipmentStatus,
                        $package['TrackingNumber'],
                        $package['CarrierCode'],
                        $shippingDate
                    ]);
                }
            }
        }

        fclose($orderFile);
        fclose($trackingFile);
    }
}
