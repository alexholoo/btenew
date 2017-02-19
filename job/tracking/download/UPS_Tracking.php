<?php

class UPS_Tracking extends TrackingDownloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/UPS/UPS_CSV_EXPORT.csv';
        $filename = Filenames::get('ups.tracking');
        copy($source, $filename);
    }
}
