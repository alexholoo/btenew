<?php

include 'classes/Job.php';

class AmazonNewItemsJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');

        $store = 'bte-amazon-ca';
        $filename = "w:/out/amazon_update/newitems_amazoncanada-$today.txt";
        $this->uploadFeed($store, $filename);

        $store = 'bte-amazon-us';
        $filename = "w:/out/amazon_update/newitems_amazonusa-$today.txt";
        $this->uploadFeed($store, $filename);
    }

    private function uploadFeed($store, $file)
    {
        if (!file_exists($file)) {
            return;
        }

        $this->log("Uploading newitems: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType('_POST_FLAT_FILE_INVLOADER_DATA_');
        $api->setFeedContent($feed);
        $api->submitFeed();
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonNewItemsJob();
$job->run($argv);
