<?php

include __DIR__ . '/../public/init.php';

class BestbuyListingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/download/Bestbuy_Listing_Downloader.php");

        $job->run();
    }
}

$job = new BestbuyListingDownloadJob();
$job->run($argv);
