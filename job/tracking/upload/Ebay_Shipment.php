<?php

use Toolkit\File;

class Ebay_Shipment extends TrackingUploader
{
    public function upload()
    {
        // BTE
        $client = new Marketplace\eBay\Client('bte');
        $filename = Filenames::get('ebay.bte.shipping');
        $this->uploadTracking($client, $filename);

        File::backup($filename);

        // ODO
        $client = new Marketplace\eBay\Client('odo');
        $filename = Filenames::get('ebay.odo.shipping');
        $this->uploadTracking($client, $filename);

        File::backup($filename);
    }

    protected function uploadTracking($client, $filename)
    {
        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        fgetcsv($fp); // skip first line

        $columns = [ 'OrderID', 'Date', 'Carrier', 'TrackingNumber', 'TransactionID' ];

        while (($fields = fgetcsv($fp))) {
            if (count($columns) != count($fields)) {
                $this->error(__METHOD__ . print_r($fields, true));
                continue;
            }

            $data = array_combine($columns, $fields);

            $client->completeSale($data);
        }

        fclose($fp);
    }
}
