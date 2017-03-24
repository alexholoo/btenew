<?php

use Supplier\Synnex\Ftp;

class Synnex_Tracking extends TrackingDownloader
{
    public function download()
    {
        // Tracking is saved in "E:/BTE/tracking/synnex/*.xml";
        $folder = Filenames::get('synnex.tracking');
        Ftp::getTracking($folder);
    }
}
