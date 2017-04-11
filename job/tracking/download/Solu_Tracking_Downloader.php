<?php

class Solu_Tracking_Downloader extends Tracking_Downloader
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
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/solu_shipment.csv';
        $filename = Filenames::get('solu.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
