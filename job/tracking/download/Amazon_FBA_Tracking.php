<?php

class Amazon_FBA_Tracking extends TrackingDownloader
{
    public function download()
    {
        $source = 'E:/BTE/amazon/reports/amazon_ca_FBA.txt';
        $filename = Filenames::get('amazon.ca.fba.tracking');
        copy($source, $filename);

        $source = 'E:/BTE/amazon/reports/amazon_us_FBA.txt';
        $filename = Filenames::get('amazon.us.fba.tracking');
        copy($source, $filename);
    }
}
