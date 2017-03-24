<?php

class Amazon_Listing_Downloader extends Listing_Downloader
{
    public function download()
    {
        $source = 'E:/BTE/amazon/reports/amazon_ca_listings.txt';
        $filename = Filenames::get('amazon.ca.listing');
        if (file_exists($source)) {
            copy($source, $filename);
        }

        $source = 'E:/BTE/amazon/reports/amazon_us_listings.txt';
        $filename = Filenames::get('amazon.us.listing');
        if (file_exists($source)) {
            copy($source, $filename);
        }
    }
}
