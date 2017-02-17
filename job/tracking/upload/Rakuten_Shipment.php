<?php

use Toolkit\File;

class Rakuten_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = 'w:/out/shipping/rakuten_tracking.txt';

        $client = new Marketplace\Rakuten\Client('US');
        $client->uploadTracking($filename);

        File::backup($filename);
    }
}
