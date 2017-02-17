<?php

use Supplier\DH\Ftp;

class DH_Tracking extends TrackingDownloader
{
    public function download()
    {
        $filename = 'E:/BTE/tracking/dh/DH-TRACKING';
        Ftp::getTracking($filename);
    }
}
