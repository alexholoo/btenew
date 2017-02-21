<?php

use Toolkit\File;

class Bestbuy_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = Filenames::get('bestbuy.shipping');
        //File::backup($filename);
    }
}
