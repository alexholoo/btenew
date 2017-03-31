<?php

include __DIR__ . '/../public/init.php';

class NeweggListingDownloadJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/download/Newegg_Listing_Downloader.php");

        $job->download();
    }
}

$job = new NeweggListingDownloadJob();
$job->run($argv);
