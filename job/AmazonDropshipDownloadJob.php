<?php

include 'classes/Job.php';

class AmazonDropshipDownloadJob extends Job
{
    protected $store;
    protected $folder = 'E:/BTE/amazon/reports/';

    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        // CA
        $this->store = 'bte-amazon-ca';
        $this->orderFile = 'amazon_ca_drop_ship.csv';
        $this->trackingFile = 'amazon_ca_drop_ship_tracking.csv';
        $list = $this->getDropshipTracking();

        // US
        $this->store = 'bte-amazon-us';
        $this->orderFile = 'amazon_us_drop_ship.csv';
        $this->trackingFile = 'amazon_us_drop_ship_tracking.csv';
        $list = $this->getDropshipTracking();
    }

    protected function getDropshipTracking()
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

        $orderFile = fopen($this->folder.$this->orderFile, 'w');
       #fputcsv($orderFile, ['orderId', 'orderDate']);

        $trackingFile = fopen($this->folder.$this->trackingFile, 'w');
       #fputcsv($trackingFile, ['orderId', 'shipmentStatus', 'Carrier', 'TrackingNumber', 'shippingDate']);

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
                        $package['CarrierCode'],
                        $package['TrackingNumber'],
                        $shippingDate
                    ]);
                }
            }
        }

        fclose($orderFile);
        fclose($trackingFile);
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonDropshipDownloadJob();
$job->run($argv);
