<?php

use Toolkit\File;

class Newegg_PriceQty_Uploader extends PriceQty_Uploader
{
    public function run($argv = [])
    {
        try {
            $this->upload();
        } catch (\Exception $e) {
            echo $e->getMessage(), EOL;
        }
    }

    public function upload()
    {
        $filename = Filenames::get('newegg.ca.priceqty');
        if (file_exists($filename)) {
            $client = new Marketplace\Newegg\Client('CA');
            $client->uploadPriceQty($filename);
            File::archive($filename);
        }

        $filename = Filenames::get('newegg.us.priceqty');
        if (file_exists($filename)) {
            $client = new Marketplace\Newegg\Client('US');
            $client->uploadPriceQty($filename);
            File::archive($filename);
        }
    }
}
