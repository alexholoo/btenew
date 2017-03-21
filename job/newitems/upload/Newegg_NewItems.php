<?php

use Toolkit\File;

class Newegg_NewItems extends NewItemsUploader
{
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
