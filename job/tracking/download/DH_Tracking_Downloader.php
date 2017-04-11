<?php

use Supplier\DH\Ftp;

class DH_Tracking_Downloader extends Tracking_Downloader
{
    public function run($argv = [])
    {
        try {
            $this->download();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function download()
    {
        $filename = Filenames::get('dh.tracking');
        Ftp::getTracking($filename);
    }
}
