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

    protected function getJob($filename)
    {
        include_once($filename);

        $path = pathinfo($filename);
        $class = $path['filename'];

        $job = false;

        if (class_exists($class)) {
            $job = new $class;
        }

        return $job;
    }
}

$job = new NeweggListingDownloadJob();
$job->run($argv);
