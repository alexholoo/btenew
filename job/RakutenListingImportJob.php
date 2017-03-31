<?php

include 'classes/Job.php';

class RakutenListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/import/Rakuten_Listing_Importer.php");

        $job->import();
    }
}

include __DIR__ . '/../public/init.php';

$job = new RakutenListingImportJob();
$job->run($argv);
