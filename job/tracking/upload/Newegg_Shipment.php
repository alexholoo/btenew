<?php

use Toolkit\File;
use Marketplace\Newegg\Ftp;

class Newegg_Shipment extends TrackingUploader
{
    public function upload()
    {
        $localFile = 'w:/out/shipping/newegg_canada_tracking.csv';
        $remoteFile = '/Inbound/Shipping/newegg_canada_tracking.csv';

        Ftp::upload($localFile, $remoteFile);

        File::backup($localFile);
    }
}
