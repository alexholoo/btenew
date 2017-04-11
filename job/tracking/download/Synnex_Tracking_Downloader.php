<?php

use Supplier\Synnex\Ftp;

class Synnex_Tracking_Downloader extends Tracking_Downloader
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
        // Tracking is saved in "E:/BTE/tracking/synnex/*.xml";
        $folder = Filenames::get('synnex.tracking');
        Ftp::getTracking($folder);
    }
}
