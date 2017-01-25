<?php

include 'classes/Job.php';

class AmazonShippingTemplateJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $store = 'bte-amazon-ca';
        $filename = "w:/out/amazon_update/ca-shipping-template.txt";
        $this->uploadFeed($store, $filename);

        $store = 'bte-amazon-us';
        $filename = "w:/out/amazon_update/us-shipping-template.txt";
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

        $this->log("Uploading shipping templates: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType('_POST_FLAT_FILE_INVLOADER_DATA_');
        $api->setFeedContent($feed);
        $api->submitFeed();

        $this->log(print_r($api->getResponse(), true));
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonShippingTemplateJob();
$job->run($argv);
