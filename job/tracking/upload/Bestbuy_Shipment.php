<?php

use Toolkit\File;

class Bestbuy_Shipment extends TrackingUploader
{
    public function upload()
    {
        $filename = 'w:/out/ship/bestbuy_shipping.csv';
        //File::backup($filename);
    }
}