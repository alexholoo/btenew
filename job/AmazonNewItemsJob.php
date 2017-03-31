<?php

include __DIR__ . '/../public/init.php';

class AmazonNewItemsJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');
        $folder = 'w:/out/amazon_update';

        $type = '_POST_FLAT_FILE_INVLOADER_DATA_';

        $store = 'bte-amazon-ca';
        $filename = "$folder/newitems_amazoncanada-$today.txt";
        $this->uploadFeed($store, $filename, $type);

        $store = 'bte-amazon-us';
        $filename = "$folder/newitems_amazonusa-$today.txt";
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

        $this->log("Uploading $type: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType($type);
        $api->setFeedContent($feed);
        $api->submitFeed();

       #$this->log(print_r($api->getResponse(), true));
    }
}

$job = new AmazonNewItemsJob();
$job->run($argv);
