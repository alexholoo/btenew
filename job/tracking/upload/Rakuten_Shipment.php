<?php

use Toolkit\File;

class Rakuten_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = Filenames::get('rakuten.us.shipping');

        $client = new Marketplace\Rakuten\Client('US');
        $client->uploadTracking($filename);

        $this->markOrdersShipped($filename);

        File::backup($filename);
    }

    protected function markOrdersShipped($filename)
    {
        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        fgetcsv($fp); // skip first line

        $shipmentService = $this->di->get('shipmentService');

        while (($fields = fgetcsv($fp))) {
            $orderId = $fields[0]; // TODO: fix
            $shipmentService->markOrderAsShipped($orderId);
        }

        fclose($fp);
    }
}
