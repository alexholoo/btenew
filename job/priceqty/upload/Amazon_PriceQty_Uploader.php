<?php

use Toolkit\File;

class Amazon_PriceQty_Uploader extends PriceQty_Uploader
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
        $type = '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_';

        $store = 'bte-amazon-ca';
        $filename = Filenames::get('amazon.ca.priceqty');
        if (file_exists($filename)) {
            $this->uploadFeed($store, $filename, $type);
            File::backup($filename);
        }

        $store = 'bte-amazon-us';
        $filename = Filenames::get('amazon.us.priceqty');
        if (file_exists($filename)) {
            $this->uploadFeed($store, $filename, $type);
            File::backup($filename);
        }
    }

    private function uploadFeed($store, $file, $type)
    {
        $this->log("Uploading $type: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType($type);
        $api->setFeedContent($feed);
        $api->submitFeed();

       #$this->log(print_r($api->getResponse(), true));
    }
}
