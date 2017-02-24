<?php

use Toolkit\File;

class Newegg_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = Filenames::get('newegg.ca.shipping');

        $client = new Marketplace\Newegg\Client('CA');
#       $client->uploadTracking($filename);

        $this->markOrdersShipped($filename);

        File::backup($filename);
    }

    protected function markOrdersShipped($filename)
    {
        if (!file_exists($filename)) {
            $this->error("File not found: $filename");
            return;
        }

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
