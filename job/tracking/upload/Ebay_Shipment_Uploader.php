<?php

use Toolkit\File;

class Ebay_Shipment_Uploader extends Tracking_Uploader
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
        // BTE
        $client = new Marketplace\eBay\Client('gfs');

        $filename = Filenames::get('ebay.gfs.shipping');
        if (file_exists($filename)) {
            $this->uploadTracking($client, $filename);
            File::backup($filename);
        }

        // ODO
        $client = new Marketplace\eBay\Client('odo');

        $filename = Filenames::get('ebay.odo.shipping');
        if (file_exists($filename)) {
            $this->uploadTracking($client, $filename);
            File::backup($filename);
        }
    }

    protected function uploadTracking($client, $filename)
    {
        $orders = $this->csvToArray($filename);

        $shipmentService = $this->di->get('shipmentService');

        foreach ($orders as $order) {
            $client->completeSale($order);
            $shipmentService->markOrderAsShipped($order['RecordNumber']);
        }
    }
}
