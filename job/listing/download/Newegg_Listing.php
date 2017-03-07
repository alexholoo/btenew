<?php

class Newegg_Listing extends ListingDownloader
{
    public function download()
    {
        $client = new Marketplace\Newegg\Client('CA');

        $filename = Filenames::get('newegg.ca.listing');
        $client->downloadInventory($filename);
    }
}
