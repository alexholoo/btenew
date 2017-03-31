<?php

include __DIR__ . '/../public/init.php';

class BestbuyListingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/download/Bestbuy_Listing_Downloader.php");

        $job->download();
    }
}

$job = new BestbuyListingDownloadJob();
$job->run($argv);
