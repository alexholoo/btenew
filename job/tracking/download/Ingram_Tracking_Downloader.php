<?php

class Ingram_Tracking_Downloader extends Tracking_Downloader
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
        $source = 'w:/out/shipping/ing_tracking.csv';
        $filename = Filenames::get('ingram.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
