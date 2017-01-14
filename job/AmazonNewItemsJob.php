<?php

include 'classes/Job.php';

class AmazonNewItemsJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $store = 'bte-amazon-ca';
        $filename  = '';
        $this->uploadFeed($store, $filename);

        $store = 'bte-amazon-us';
        $filename  = '';
        $this->uploadFeed($store, $filename);
    }

    private function uploadFeed($store, $file)
    {
        if (!file_exists($file)) {
            return;
        }

        $this->log("Uploading feed: $file");

        $client = new Marketplace\Amazon\Client($store);
        $client->uploadNewItems($file); // TODO
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonNewItemsJob();
$job->run($argv);
