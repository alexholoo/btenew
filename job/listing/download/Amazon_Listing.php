<?php

class Amazon_Listing extends ListingDownloader
{
    public function download()
    {
        $source = 'E:/BTE/amazon/reports/amazon_ca_listings.txt';
        $filename = Filenames::get('amazon.ca.listing');
        copy($source, $filename);

        $source = 'E:/BTE/amazon/reports/amazon_us_listings.txt';
        $filename = Filenames::get('amazon.us.listing');
        copy($source, $filename);
    }
}
