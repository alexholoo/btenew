<?php

class Amazon_NewItems_Uploader extends NewItems_Uploader
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
        $type = '_POST_FLAT_FILE_INVLOADER_DATA_';

        $store = 'bte-amazon-ca';
        $filename = Filenames::get('amazon.ca.newitems');
        $this->uploadFeed($store, $filename, $type);

        $store = 'bte-amazon-us';
        $filename = Filenames::get('amazon.us.newitems');
        $this->uploadFeed($store, $filename, $type);
    }

    private function uploadFeed($store, $file, $type)
    {
        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            $this->error(__METHOD__." File not found: $file");
            return;
        }

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType($type);
        $api->setFeedContent($feed);
        $api->submitFeed();

       #$this->log(print_r($api->getResponse(), true));
    }
}
