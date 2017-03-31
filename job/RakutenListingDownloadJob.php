<?php

include __DIR__ . '/../public/init.php';

class RakutenListingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/download/Rakuten_Listing_Downloader.php");

        $job->download();
    }
}

$job = new RakutenListingDownloadJob();
$job->run($argv);
