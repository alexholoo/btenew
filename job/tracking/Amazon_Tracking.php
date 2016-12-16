<?php

class Amazon_Tracking extends TrackingJob
{
    public function getStatus()
    {
        return 0; // 1-enabled, 0-disabled
    }

    public function merge()
    {
    }

    public function download()
    {
        $this->store = 'bte-amazon-ca';
        $this->dropship = 'E:/BTE/tracking/amazon_ca_dropship.csv';
        $this->tracking = 'E:/BTE/tracking/amazon_ca_dropship_tracking.csv';
        $this->downloadTracking();

        $this->store = 'bte-amazon-us';
        $this->dropship = 'E:/BTE/tracking/amazon_us_dropship.csv';
        $this->tracking = 'E:/BTE/tracking/amazon_us_dropship_tracking.csv';
        $this->downloadTracking();
    }

    public function downloadTracking()
    {
        $dropship = fopen($this->dropship, 'w');
        $tracking = fopen($this->tracking, 'w');

        $api = new \AmazonFulfillmentOrderList($this->store);

        $api->setStartTime(gmdate("Y-m-d\TH:i:s", time()-3600*24*30));
        $api->setUseToken(true);
        $api->fetchOrderList();

        $orders = $api->getFullList();

        if (!is_array($orders)) {
            pr($orders);
            return;
        }

        foreach ($orders as $order) {
            $data = $order->getOrder();

            $orderId = $data['Details']['DisplayableOrderId'];
            $orderDateTime = $data['Details']['DisplayableOrderDateTime'];

            fputcsv($dropship, [ $orderId, $orderDateTime ]);

            foreach ($data['Shipments'] as $shipment) {

                $shipmentStatus = $shipment['FulfillmentShipmentStatus'];
                $shippingDateTime = substr($shipment['ShippingDateTime'], 0, 10);

                foreach ($shipment['FulfillmentShipmentPackage'] as $package) {
                    fputcsv($tracking, [
                        $orderId,
                        $shipmentStatus,
                        $package['TrackingNumber'],
                        $package['CarrierCode'],
                        $shippingDateTime
                    ]);
                }
            }
        }

        fclose($dropship);
        fclose($tracking);
    }
}

//include __DIR__ . '/../../public/init.php';

//$job = new Amazon_Tracking();
//$job->download();
