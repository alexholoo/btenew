<?php

use Toolkit\File;

class Rakuten_NewItems_Uploader extends NewItems_Uploader
{
    public function run($argv = [])
    {
        $this->upload();
    }

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
