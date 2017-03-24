<?php

class Rakuten_Listing extends ListingDownloader
{
    public function download()
    {
        $client = new Marketplace\Rakuten\Client('US');
        $filename = Filenames::get('rakuten.us.listing');
        $client->downloadInventory($filename);
    }
}
