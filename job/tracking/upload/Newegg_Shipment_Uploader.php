<?php

use Toolkit\File;

class Newegg_Shipment_Uploader extends Tracking_Uploader
{
    public function run($argv = [])
    {
        try {
            $this->upload();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function upload()
    {
        // CA
        $client = new Marketplace\Newegg\Client('CA');
        $filename = Filenames::get('newegg.ca.shipping');

        if (file_exists($filename)) {
            $client->uploadTracking($filename);
            $this->markOrdersShipped($filename);
            File::archive($filename);
        }

        // US
        $client = new Marketplace\Newegg\Client('US');
        $filename = Filenames::get('newegg.us.shipping');

        if (file_exists($filename)) {
            $client->uploadTracking($filename);
            $this->markOrdersShipped($filename);
            File::archive($filename);
        }
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
