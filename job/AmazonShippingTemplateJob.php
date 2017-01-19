<?php

include 'classes/Job.php';

class AmazonShippingTemplateJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $store = 'bte-amazon-ca';
        $filename = "w:/out/amazon_update/us-shipping-template.txt";
        $this->uploadFeed($store, $filename);

        $store = 'bte-amazon-us';
        $filename = "w:/out/amazon_update/us-shipping-template.txt";
        $this->uploadFeed($store, $filename);
    }

    private function uploadFeed($store, $file)
    {
        if (!file_exists($file)) {
            return;
        }

        $this->log("Uploading shipping templates: $file");

        $feed = file_get_contents($file);

        $api = new AmazonFeed($store);
        $api->setFeedType('_POST_FLAT_FILE_INVLOADER_DATA_');
        $api->setFeedContent($feed);
        $api->submitFeed();
    }
}

include __DIR__ . '/../public/init.php';

$job = new AmazonShippingTemplateJob();
$job->run($argv);
