<?php

include 'classes/Job.php';

class AmazonPriceQtyUpdateJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');
        $folder = 'w:/out/amazon_update';

        $type = '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_';

        $store = 'bte-amazon-ca';
        $filename = "$folder/amazon_cad_update$today.txt";
        $this->uploadFeed($store, $filename, $type);

        $store = 'bte-amazon-us';
        $filename = "$folder/amazon_usa_update$today.txt";
        $this->uploadFeed($store, $filename, $type);
    }

    private function uploadFeed($store, $file, $type)
    {
        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return;
        }

        $this->log("Uploading $type: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType($type);
        $api->setFeedContent($feed);
        $api->submitFeed();

        $this->log(print_r($api->getResponse(), true));
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonPriceQtyUpdateJob();
$job->run($argv);
