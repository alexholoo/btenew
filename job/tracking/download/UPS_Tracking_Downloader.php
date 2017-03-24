<?php

class UPS_Tracking_Downloader extends Tracking_Downloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/UPS/UPS_CSV_EXPORT.csv';
        $filename = Filenames::get('ups.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
