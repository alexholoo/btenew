<?php

include __DIR__ . '/../public/init.php';

class BestbuyListingImportJob extends Job
{
    public function run($argv = [])
    {
        $this->log('>> '. __CLASS__);

        $job = $this->getJob("listing/import/Bestbuy_Listing_Importer.php");

        $job->import();
    }
}

$job = new BestbuyListingImportJob();
$job->run($argv);
