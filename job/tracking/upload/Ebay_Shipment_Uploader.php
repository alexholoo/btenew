<?php

use Toolkit\File;

class Ebay_Shipment_Uploader extends Tracking_Uploader
{
    public function run($argv = [])
    {
        $this->upload();
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
       #if (!file_exists($filename)) {
           #$this->error(__METHOD__." File not found: $filename");
           #return;
       #}

        $fp = fopen($filename, 'r');

        fgetcsv($fp); // skip first line

        $columns = [ 'OrderID', 'Date', 'Carrier', 'TrackingNumber', 'TransactionID' ];

        $shipmentService = $this->di->get('shipmentService');

        while (($fields = fgetcsv($fp))) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__ . print_r($fields, true));
                continue;
            }

            $data = array_combine($columns, $fields);

            $client->completeSale($data);

            $shipmentService->markOrderAsShipped($data['OrderID']);
        }

        fclose($fp);
    }
}
