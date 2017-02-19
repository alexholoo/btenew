<?php

class EShipper_Tracking extends TrackingDownloader
{
    public function download()
    {
        // Nothing to do, the file is already there
        $source = 'w:/out/shipping/eshipper_shipment.csv';
        $filename = Filenames::get('eshipper.tracking');
        copy($source, $filename);
    }
}
