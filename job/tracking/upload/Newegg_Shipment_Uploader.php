<?php

use Toolkit\File;

class Newegg_Shipment_Uploader extends Tracking_Uploader
{
    public function run($argv = [])
    {
        $this->upload();
    }

    public function upload()
    {
        // CA
        $client = new Marketplace\Newegg\Client('CA');
        $filename = Filenames::get('newegg.ca.shipping');

        if (file_exists($filename)) {
            $client->uploadTracking($filename);
            $this->markOrdersShipped($filename);
            File::backup($filename);
        }

        // US
        $client = new Marketplace\Newegg\Client('US');
        $filename = Filenames::get('newegg.us.shipping');

        if (file_exists($filename)) {
            $client->uploadTracking($filename);
            $this->markOrdersShipped($filename);
            File::backup($filename);
        }
    }

    protected function markOrdersShipped($filename)
    {
       #if (!file_exists($filename)) {
           #$this->error(__METHOD__." File not found: $filename");
           #return;
       #}

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
