<?php

include __DIR__ . '/../public/init.php';

class AmazonPriceQtyUpdateJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');
        $folder = 'w:/out/amazon_update';

        $store = 'bte-amazon-ca';
        $filename = "$folder/amazon_cad_update$today.txt";
        $this->uploadFeed($store, $filename);

        $store = 'bte-amazon-us';
        $filename = "$folder/amazon_usa_update$today.txt";
        $this->uploadFeed($store, $filename);
    }

    private function uploadFeed($store, $file)
    {
        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            $this->error(__METHOD__." File not found: $file");
            return;
        }

        $type = '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_';

        $this->log("Uploading $type: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType($type);
        $api->setFeedContent($feed);
        $api->submitFeed();

       #$this->log(print_r($api->getResponse(), true));
    }
}

$job = new AmazonPriceQtyUpdateJob();
$job->run($argv);
