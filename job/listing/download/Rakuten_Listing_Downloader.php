<?php

class Rakuten_Listing_Downloader extends Listing_Downloader
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
        $client = new Marketplace\Rakuten\Client('US');
        $filename = Filenames::get('rakuten.us.listing');
        $client->downloadInventory($filename);
    }
}
