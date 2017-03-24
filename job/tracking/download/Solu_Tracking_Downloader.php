<?php

class Solu_Tracking_Downloader extends Tracking_Downloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/solu_shipment.csv';
        $filename = Filenames::get('solu.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
