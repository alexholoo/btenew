<?php

class Fedex_Tracking extends TrackingDownloader
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
