<?php

class CanadaPost_Tracking_Downloader extends Tracking_Downloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/cpc.xml';
        $filename = Filenames::get('canada.post.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
