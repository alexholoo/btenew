<?php

include 'classes/Job.php';

class AmazonPriceQtyUpdateJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');

        $store = 'bte-amazon-ca';
        $filename = "w:/out/amazon_update/amazon_cad_update$today.txt";
        $this->uploadFeed($store, $filename);

        $store = 'bte-amazon-us';
        $filename = "w:/out/amazon_update/amazon_usa_update$today.txt";
        $this->uploadFeed($store, $filename);
    }

    private function uploadFeed($store, $file)
    {
        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            $this->log("File not found: $file");
            return;
        }

        $this->log("Uploading PriceQty: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType('_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_');
        $api->setFeedContent($feed);
        $api->submitFeed();

        $this->log(print_r($api->getResponse(), true));
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonPriceQtyUpdateJob();
$job->run($argv);
