<?php

use Supplier\DH\Ftp;

class DH_Tracking_Downloader extends Tracking_Downloader
{
    public function run($argv = [])
    {
        $this->download();
    }

    public function download()
    {
        $filename = Filenames::get('dh.tracking');
        Ftp::getTracking($filename);
    }
}
