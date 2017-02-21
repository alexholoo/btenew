<?php

class Techdata_Tracking extends TrackingDownloader
{
    public function download()
    {
        // No idea how to do
        $source = 'w:/out/shipping/TD_tracking.csv';
        $filename = Filenames::get('techdata.tracking');
        copy($source, $filename);
    }
}
