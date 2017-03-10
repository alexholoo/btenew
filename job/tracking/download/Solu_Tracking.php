<?php

class Solu_Tracking extends TrackingDownloader
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
