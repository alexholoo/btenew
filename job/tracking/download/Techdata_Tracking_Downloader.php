<?php

class Techdata_Tracking_Downloader extends Tracking_Downloader
{
    public function run($argv = [])
    {
        try {
            $this->download();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

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
