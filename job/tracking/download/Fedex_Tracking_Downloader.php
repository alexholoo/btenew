<?php

class Fedex_Tracking_Downloader extends Tracking_Downloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/fedex_shipment.txt';
        $filename = Filenames::get('fedex.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
