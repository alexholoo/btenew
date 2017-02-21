<?php

use Toolkit\File;

class Newegg_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = Filenames::get('newegg.ca.shipping');

        $client = new Marketplace\Newegg\Client('CA');
        $client->uploadTracking($filename);

        File::backup($filename);
    }
}
