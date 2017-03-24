<?php

use Supplier\Synnex\Ftp;

class Synnex_Tracking_Downloader extends Tracking_Downloader
{
    public function download()
    {
        // Tracking is saved in "E:/BTE/tracking/synnex/*.xml";
        $folder = Filenames::get('synnex.tracking');
        Ftp::getTracking($folder);
    }
}
