<?php

include 'classes/Job.php';

class BestbuyListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/import/Bestbuy_Listing_Importer.php");

        $job->import();
    }
}

include __DIR__ . '/../public/init.php';

$job = new BestbuyListingImportJob();
$job->run($argv);
