<?php

include __DIR__ . '/../public/init.php';

class RakutenListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/import/Rakuten_Listing_Importer.php");

        $job->run();
    }
}

$job = new RakutenListingImportJob();
$job->run($argv);
