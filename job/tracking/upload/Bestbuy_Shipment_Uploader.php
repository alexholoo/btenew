<?php

use Toolkit\File;

class Bestbuy_Shipment_Uploader extends Tracking_Uploader
{
    public function upload()
    {
        $client = new Marketplace\Bestbuy\Client();

        $filename = Filenames::get('bestbuy.shipping');

        if (file_exists($filename)) {
            $this->uploadTracking($client, $filename);
            File::backup($filename);
        }
    }

    protected function uploadTracking($filename)
    {
        $orders = $this->csvToArray($filename);

        $shipmentService = $this->di->get('shipmentService');

        foreach ($orders as $order) {
            $orderId = $order['orderId'];

            $tracking = [
                'carrierCode'    => $order['carrierCode'],
                'carrierName'    => $order['carrierName'],
                'trackingNumber' => $order['trackingNumber'],
            ];

            $client->updateTracking($orderId, $tracking);

            $shipmentService->markOrderAsShipped($orderId);
        }

        fclose($fp);
    }
}
