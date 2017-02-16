<?php

class Amazon_Tracking extends TrackingJob
{
    public function getStatus()
    {
        return 1; // 1-enabled, 0-disabled
    }

    public function merge()
    {
        $this->tracking = 'E:/BTE/tracking/amazon/amazon_ca_dropship_tracking.csv';
        $this->mergeFile('Canada');

        $this->tracking = 'E:/BTE/tracking/amazon/amazon_us_dropship_tracking.csv';
        $this->mergeFile('United States');
    }

    public function mergeFile($site)
    {
        if (($fp = fopen($this->tracking, 'r')) == false) {
            return;
        }

        while (($fields = fgetcsv($fp))) {
            $orderId = $fields[0];
            $orderItemId = '';
            $quantity = '';
            $shipDate = $fields[4];
            $carrierCode = $fields[3];
            $carrierName = '';
            $trackingNumber = $fields[2];
            $shipMethod = '';
            $fullAddress = '';

            $this->log("\t$shipDate\t$orderId\t$trackingNumber");

            if ($this->masterShipment) {
                $row = [
                    $orderId,
                    $orderItemId,
                    $quantity,
                    $shipDate,
                    $carrierCode,
                    $carrierName,
                    $trackingNumber,
                    $shipMethod,
                    $fullAddress,
                    $site
                ];
                $this->masterShipment->write($row);
            }
        }

        fclose($fp);
    }

    public function download()
    {
        $this->store = 'bte-amazon-ca';
        $this->dropship = 'E:/BTE/tracking/amazon/amazon_ca_dropship.csv';
        $this->tracking = 'E:/BTE/tracking/amazon/amazon_ca_dropship_tracking.csv';
        $this->downloadTracking();

        $this->store = 'bte-amazon-us';
        $this->dropship = 'E:/BTE/tracking/amazon/amazon_us_dropship.csv';
        $this->tracking = 'E:/BTE/tracking/amazon/amazon_us_dropship_tracking.csv';
        $this->downloadTracking();
    }

    public function downloadTracking()
    {
        if (file_exists($this->tracking) && time() - filemtime($this->tracking) < 3600) {
            return;
        }

        $dropship = fopen($this->dropship, 'w');
        $tracking = fopen($this->tracking, 'w');

        $api = new \AmazonFulfillmentOrderList($this->store);

        $api->setStartTime(gmdate("Y-m-d\TH:i:s", time()-3600*24*30));
        $api->setUseToken(true);
        $api->fetchOrderList();

        $orders = $api->getFullList();

        if (!is_array($orders)) {
            //pr($orders);
            return;
        }

        foreach ($orders as $order) {
            $data = $order->getOrder();
            //fpr($data);

            $orderId = $data['Details']['DisplayableOrderId'];
            $orderDateTime = $data['Details']['DisplayableOrderDateTime'];

            fputcsv($dropship, [ $orderId, $orderDateTime ]);

            if (!isset($data['Shipments'])) {
                continue; // not shipped yet, or received
            }

            foreach ($data['Shipments'] as $shipment) {

                $shipmentStatus = $shipment['FulfillmentShipmentStatus'];
                $shippingDateTime = substr($shipment['ShippingDateTime'], 0, 10);

                if ($shipmentStatus != 'SHIPPED') { // PENDING
                    continue;
                }

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

//include __DIR__ . '/../public/init.php';
//
//$job = new Amazon_Tracking();
//$job->download();
