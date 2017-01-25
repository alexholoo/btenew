<?php

include 'classes/Job.php';

class AmazonShippingUploadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');

        $store = 'bte-amazon-ca';
        $filename = 'w:/out/shipping/amazon_ca_shipment.txt';
        $this->uploadFeed($store, $filename);

        $store = 'bte-amazon-us';
        $filename = 'w:/out/shipping/amazon_us_shipment.txt';
        $this->uploadFeed($store, $filename);
    }

    private function uploadFeed($store, $file)
    {
        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return;
        }

        $this->log("Uploading shipping: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType('_POST_FLAT_FILE_FULFILLMENT_DATA_');
        $api->setFeedContent($feed);
        $api->submitFeed();

        $this->log(print_r($api->getResponse(), true));
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonShippingUploadJob();
$job->run($argv);
