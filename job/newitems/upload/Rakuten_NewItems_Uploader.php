<?php

use Toolkit\File;

class Rakuten_NewItems extends NewItemsUploader
{
    public function upload()
    {
        $filename = Filenames::get('rakuten.us.newitems');

        if (file_exists($filename)) {
            $client = new Marketplace\Rakuten\Client('US');
            $client->uploadNewItems($filename);
            File::backup($filename);
        }
    }
}
