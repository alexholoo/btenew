<?php

class TNT_Tracking extends TrackingDownloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/tntshipments.csv';
        $filename = Filenames::get('tnt.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
