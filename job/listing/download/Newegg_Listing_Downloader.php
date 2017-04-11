<?php

class Newegg_Listing_Downloader extends Listing_Downloader
{
    public function run($argv = [])
    {
        try {
            $this->download();
        } catch (\Exception $e) {
            echo $e->getMessage, EOL;
        }
    }

    public function download()
    {
        // Newegg CA
        $client = new Marketplace\Newegg\Client('CA');
        $filename = Filenames::get('newegg.ca.listing');
        $client->downloadInventory($filename);

        // Newegg US
        $client = new Marketplace\Newegg\Client('US');
        $filename = Filenames::get('newegg.us.listing');
        $client->downloadInventory($filename);
    }
}
