<?php

include 'classes/Job.php';

class AmazonUpdateJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $today = date('m-d-Y');
        $folder = 'w:/out/amazon_update';

        $feeds = [
            'bte-amazon-ca' => [
                [
                    'type' => '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_',
                    'file' => "$folder/amazon_cad_update$today.txt",
                ],
                [
                    'type' => '_POST_FLAT_FILE_INVLOADER_DATA_',
                    'file' => "$folder/ca-shipping-template.txt",
                ],
                [
                    'type' => '_POST_FLAT_FILE_INVLOADER_DATA_',
                    'file' => "$folder/newitems_amazoncanada-$today.txt",
                ],
            ],

            'bte-amazon-us' => [
                [
                    'type' => '_POST_FLAT_FILE_PRICEANDQUANTITYONLY_UPDATE_DATA_',
                    'file' => "$folder/amazon_usa_update$today.txt",
                ],
                [
                    'type' => '_POST_FLAT_FILE_INVLOADER_DATA_',
                    'file' => "$folder/us-shipping-template.txt",
                ],
                [
                    'type' => '_POST_FLAT_FILE_INVLOADER_DATA_',
                    'file' => "$folder/newitems_amazonusa-$today.txt",
                ],
            ],
        ];

        foreach ($feeds as $store => $feed) {
            $this->uploadFeed($store, $feed['file'], $feed['type']);
        }
    }

    private function uploadFeed($store, $file, $type)
    {
        if (!IS_PROD) {
            throw new Exception('This script can only run on production server.');
        }

        if (!file_exists($file)) {
            $this->error(__METHOD_." File not found: $file");
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

include __DIR__ . '/../public/init.php';

$job = new AmazonUpdateJob();
$job->run($argv);
