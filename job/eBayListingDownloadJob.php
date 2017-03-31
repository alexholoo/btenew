<?php

include __DIR__ . '/../public/init.php';

class eBayListingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/download/eBay_Listing_Downloader.php");

        $job->download();
    }
}

$job = new eBayListingDownloadJob();
$job->run($argv);
