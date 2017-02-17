<?php

use Toolkit\File;

class Ebay_Shipment extends TrackingUploader
{
    public function upload()
    {
        // BTE
        $client = new Marketplace\eBay\Client('bte');
        $filename = 'w:/out/ship/ebay_shipping_bte.csv';
        $this->uploadTracking($client, $filename);

        File::backup($filename);

        // ODO
        $client = new Marketplace\eBay\Client('odo');
        $filename = 'w:/out/ship/ebay_shipping_odo.csv';
        $this->uploadTracking($client, $filename);

        File::backup($filename);
    }

    protected function uploadTracking($client, $filename)
    {
        if (($fp = fopen($filename, 'r')) == false) {
            $this->error("File not found: $filename");
            return;
        }

        while (($fields = fgetcsv($fp))) {
            $client->completeSale([
                'OrderID'        => $fields[0],
                'TransactionID'  => $fields[0],
                'TrackingNumber' => $fields[0],
                'Carrier'        => $fields[0],
                'Date'           => $fields[0],
            ]);
        }

        fclose($fp);
    }
}
