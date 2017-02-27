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
        if (!file_exists($filename)) {
            $this->error(__METHOD__." File not found: $filename");
            return;
        }

        $fp = fopen($filename, 'r');

        $columns = fgetcsv($fp, 0, "\t"); // skip first line
        /*
            'receipt-id',
            'receipt-item-id',
            'quantity',
            'tracking-type',
            'tracking-number',
            'ship-date',
        */

        $shipmentService = $this->di->get('shipmentService');

        while (($fields = fgetcsv($fp, 0, "\t"))) {
            $orderId = $fields[0];
            $shipmentService->markOrderAsShipped($orderId);
        }

        fclose($fp);
    }
}
