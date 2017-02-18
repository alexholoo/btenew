<?php

use Supplier\DH\Ftp;

class DH_Tracking extends TrackingDownloader
{
    public function download()
    {
        $filename = Filenames::get('dh.tracking');
        Ftp::getTracking($filename);
    }
}
