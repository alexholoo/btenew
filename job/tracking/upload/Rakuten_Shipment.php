<?php

use Toolkit\File;

class Rakuten_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = Filenames::get('rakuten.us.shipping');

        $client = new Marketplace\Rakuten\Client('US');
        $client->uploadTracking($filename);

        File::backup($filename);
    }
}
