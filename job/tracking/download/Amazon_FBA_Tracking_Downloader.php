<?php

class Amazon_FBA_Tracking_Downloader extends Tracking_Downloader
{
    public function download()
    {
        $source = 'E:/BTE/amazon/reports/amazon_ca_FBA.txt';
        $filename = Filenames::get('amazon.ca.fba.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }

        $source = 'E:/BTE/amazon/reports/amazon_us_FBA.txt';
        $filename = Filenames::get('amazon.us.fba.tracking');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
