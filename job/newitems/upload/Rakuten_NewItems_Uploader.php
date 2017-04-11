<?php

use Toolkit\File;

class Rakuten_NewItems_Uploader extends NewItems_Uploader
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
        $filename = Filenames::get('rakuten.us.newitems');

        if (file_exists($filename)) {
            $client = new Marketplace\Rakuten\Client('US');
            $client->uploadNewItems($filename);
            File::backup($filename);
        }
    }
}
