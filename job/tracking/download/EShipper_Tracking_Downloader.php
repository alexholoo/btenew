<?php

class EShipper_Tracking_Downloader extends Tracking_Downloader
{
    public function run($argv = [])
    {
        $this->download();
    }

    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/eshipper_shipment.csv';
        $filename = Filenames::get('eshipper.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
