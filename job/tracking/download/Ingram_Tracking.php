<?php

class Ingram_Tracking extends TrackingDownloader
{
    public function download()
    {
        // No idea how to do
        $source = 'w:/out/shipping/ing_tracking.csv';
        $filename = Filenames::get('ingram.tracking');
        copy($source, $filename);
    }
}
