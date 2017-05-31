<?php

class Fedex_Tracking_Downloader extends Tracking_Downloader
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
        $source = 'w:/out/shipping/fedex_shipment.csv';
        $filename = Filenames::get('fedex.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
