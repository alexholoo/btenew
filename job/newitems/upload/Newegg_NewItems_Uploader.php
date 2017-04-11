<?php

use Toolkit\File;

class Newegg_NewItems_Uploader extends NewItems_Uploader
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
        // CA
        $filename = Filenames::get('newegg.ca.newitems');

        if (file_exists($filename)) {
            $client = new Marketplace\Newegg\Client('CA');
            $client->uploadNewItems($filename);
            File::backup($filename);
        }

        // US
        $filename = Filenames::get('newegg.us.newitems');

        if (file_exists($filename)) {
            $client = new Marketplace\Newegg\Client('US');
            $client->uploadNewItems($filename);
            File::backup($filename);
        }
    }
}
