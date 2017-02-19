<?php

class CanadaPost_Tracking extends TrackingDownloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/cpc.xml';
        $filename = Filenames::get('canada.post.tracking');
        copy($source, $filename);
    }
}
