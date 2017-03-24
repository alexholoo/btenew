<?php

class Techdata_Tracking_Downloader extends Tracking_Downloader
{
    public function download()
    {
        // No idea how to do
        $source = 'w:/out/shipping/TD_tracking.csv';
        $filename = Filenames::get('techdata.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
